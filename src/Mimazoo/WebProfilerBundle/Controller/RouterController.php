<?php

namespace Mimazoo\WebProfilerBundle\Controller;

use Symfony\Bundle\WebProfilerBundle\Controller\RouterController as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;
/**
 * RouterController.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RouterController extends BaseController
{
    private $profiler;
    private $twig;
    private $matcher;
    private $routes;
    
    public function __construct(Profiler $profiler, \Twig_Environment $twig, UrlMatcherInterface $matcher = null, $routes = null)
    {
        $this->profiler = $profiler;
        $this->twig = $twig;
        $this->matcher = $matcher;
        $this->routes = $routes;
    
        if (null === $this->routes && null !== $this->matcher && $this->matcher instanceof RouterInterface) {
            $this->routes = $matcher->getRouteCollection();
        }
    }
    
    /**
     * Renders the profiler panel for the given token.
     *
     * @param string $token The profiler token
     *
     * @return Response A Response instance
     */
    public function panelAction($token)
    {
        $this->profiler->disable();

        if (null === $this->matcher || null === $this->routes) {
            return new Response('The Router is not enabled.');
        }

        $profile = $this->profiler->loadProfile($token);

        $context = $this->matcher->getContext();
        $context->setMethod($profile->getMethod());
        $matcher = new TraceableUrlMatcher($this->routes, $context);

        $request = $profile->getCollector('request');

        return new Response($this->twig->render('@WebProfiler/Router/panel.html.twig', array(
            'request' => $request,
            'router'  => $profile->getCollector('router'),
            'traces'  => $matcher->getTraces($request->getPathInfo()),
        )), 200, array('Content-Type' => 'text/html'));
    }
}
