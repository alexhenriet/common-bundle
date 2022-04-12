<?php

namespace Alexhenriet\Bundle\CommonBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

abstract class AbstractController
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ParameterBagInterface
     */
    protected $params;

    public function __construct(UrlGeneratorInterface $router,
                                ManagerRegistry $doctrine,
                                Security $security,
                                Environment $twig,
                                FormFactoryInterface $formFactory,
                                ParameterBagInterface $params
                                )
    {
        $this->router = $router;
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->params = $params;
    }

    protected function getDoctrine() : ManagerRegistry
    {
        return $this->doctrine;
    }

    protected function isGranted($attributes)
    {
        return $this->security->isGranted($attributes);
    }

    protected function getUser()
    {
        return $this->security->getUser();
    }

    protected function renderView($view, array $parameters = []): string
    {
        return $this->twig->render($view, $parameters);
    }

    protected function render($view, array $parameters = [], Response $response = null): Response
    {
        $content = $this->renderView($view, $parameters);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    protected function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    protected function getParameter(string $name)
    {
        return $this->params->get($name);
    }
}