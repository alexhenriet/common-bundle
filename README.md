# CommonBundle

Common stuffs used in Symfony projects.

## How to install the bundle ?

1. Create bundle configuration in config/packages/alexhenriet_common.yaml BEFORE requiring the package or you'll get an error during install (the bundle has no recipe)

       common:
           ldap_host: 'ldap-host.lan'
           # ldap_port: 389
           # ldap_opt_protocol_version: 3
           # ldap_opt_referrals: 0
           login_prefix: 'DOMAIN'
           # login_route: 'app_login'
           bypass_user_identifiers: ['MyLogin']
           bypass_environments: ['loc']

2. Install the package with composer

       composer require alexhenriet/common-bundle

3. Enable the package in config/bundles.php

       return [
           ...
           Alexhenriet\Bundle\CommonBundle\CommonBundle::class => ['all' => true],
       ];

## How to use the bundle ?

### Custom Authenticator

1. Generate user and auth using commands provided by symfony/maker-bundle


2. Replace the custom authenticator in the security.yaml

       security:
           enable_authenticator_manager: true

       firewalls:
           main:
               custom_authenticators:
                   - Alexhenriet\Bundle\CommonBundle\Security\BypassableLdapLoginFormAuthenticator

3. Use _username and _password as input names in your template\security\login.html.twig

### Abstract Controller

1. Extend the abstract controller in a controller

       use Alexhenriet\Bundle\CommonBundle\Controller\AbstractController;

       class ActionsController extends AbstractController
       {}

2. If you get error "Controller ... cannot be fetched from the container because it is private",
add the following lines to your services.yaml

       # controllers are imported separately to make sure services can be injected
       # as action arguments even if you don't extend any base controller class
       App\Controller\:
         resource: '../src/Controller/'
         tags: ['controller.service_arguments']

## Warranty Disclaimer and Limitation of Liability

THIS FREE LIBRARY IS PROVIDED ON AN "AS IS" BASIS, WITHOUT WARRANTY OF ANY KIND. 
TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, THE AUTHOR DISCLAIMS ALL WARRANTIES, EXPRESS, IMPLIED, 
STATUTORY OR OTHERWISE, INCLUDING BUT NOT LIMITED TO IMPLIED WARRANTIES OF FITNESS FOR A PARTICULAR 
PURPOSE AND MERCHANTABILITY. UNDER NO CIRCUMSTANCES WILL THE AUTHOR BE LIABLE FOR ANY CONSEQUENTIAL, 
SPECIAL, INDIRECT, INCIDENTAL OR PUNITIVE DAMAGES WHATSOEVER ARISING OUT OF THE USE OR INABILITY 
TO USE THE LIBRARY, EVEN IF THE AUTHOR HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. 
SOME JURISDICTIONS DO NOT ALLOW LIMITATIONS ON IMPLIED WARRANTIES OR THE EXCLUSION OR LIMITATION OF 
LIABILITY FOR CONSEQUENTIAL OR INCIDENTAL DAMAGES, SO THE ABOVE LIMITATIONS MAY NOT APPLY TO YOU. 
THIS EULA WILL BE GOVERNED BY AND CONSTRUED IN ACCORDANCE WITH THE LAWS OF BELGIUM WITHOUT REGARD 
TO ITS CONFLICTS OF LAWS OR ITS PRINCIPLES. ANY CLAIM OR SUIT ARISING OUT OF OR RELATING TO THIS EULA 
WILL BE BROUGHT EXCLUSIVELY IN ANY COURT OF COMPETENT JURISDICTION LOCATED IN HAINAUT (BELGIUM).