<?php

namespace Alexhenriet\Bundle\CommonBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class BypassableLdapLoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    /** @var string */
    protected $ldapHost;

    /** @var int */
    protected $ldapPort;

    /** @var int */
    protected $ldapOptProtocolVersion;

    /** @var int */
    protected $ldapOptReferrals;

    /** @var ?string */
    protected $loginPrefix;

    /** @var string */
    protected $loginRoute;

    /** @var string[] */
    protected $bypassUserIdentifiers;

    /** @var string[] */
    protected $bypassEnvironments;

    /** @var UrlGeneratorInterface */
    protected UrlGeneratorInterface $urlGenerator;

    /** @var string */
    protected string $environment;

    public function __construct(UrlGeneratorInterface $urlGenerator,
                                string $environment,
                                string $ldapHost,
                                int $ldapPort,
                                int $ldapOptProtocolVersion,
                                int $ldapOptReferrals,
                                ?string $loginPrefix,
                                string $loginRoute,
                                array $bypassUserIdentifiers,
                                array $bypassEnvironments
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->environment = $environment;
        $this->ldapHost = $ldapHost;
        $this->ldapPort = $ldapPort;
        $this->ldapOptProtocolVersion = $ldapOptProtocolVersion;
        $this->ldapOptReferrals = $ldapOptReferrals;
        $this->loginPrefix = $loginPrefix;
        $this->loginRoute = $loginRoute;
        $this->bypassUserIdentifiers = $bypassUserIdentifiers;
        $this->bypassEnvironments = $bypassEnvironments;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate($this->loginRoute);
    }

    public function authenticate(Request $request) : Passport
    {
        $password = $request->request->get('_password');
        $username = $request->request->get('_username');
        $csrfToken = $request->request->get('_csrf_token');
        return new Passport(
            new UserBadge($username),
            new CustomCredentials(
                function ($credentials, UserInterface $user) : bool {
                    if (in_array($this->environment, $this->bypassEnvironments) &&
                        in_array($user->getUserIdentifier(), $this->bypassUserIdentifiers)) {
                        return true;
                    }
                    return $this->check($this->loginPrefix . '\\' . $user->getUserIdentifier(), $credentials);
                },
                $password
            ),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge()
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    private function check(string $login, string $password) : bool
    {
        $adLink = \ldap_connect($this->ldapHost, $this->ldapPort);
        \ldap_set_option($adLink, LDAP_OPT_PROTOCOL_VERSION, $this->ldapOptProtocolVersion);
        \ldap_set_option($adLink, LDAP_OPT_REFERRALS, $this->ldapOptReferrals);
        if (!@\ldap_bind($adLink, $login, $password)) {
            return false;
        }
        return true;
    }
}