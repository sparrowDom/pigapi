<?php

namespace Mimazoo\WebProfilerBundle\Controller;

use Symfony\Bundle\WebProfilerBundle\Controller\ExceptionController as BaseController;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;

/**
 * ExceptionController.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExceptionController extends BaseController
{
    
    protected $twig;
    protected $debug;
    protected $profiler;
    
    public function __construct(Profiler $profiler, \Twig_Environment $twig, $debug)
    {
        $this->profiler = $profiler;
        $this->twig = $twig;
        $this->debug = $debug;
    }
    
    /**
     * Renders the exception panel for the given token.
     *
     * @param string $token The profiler token
     *
     * @return Response A Response instance
     */
    public function showAction($token)
    {
        $this->profiler->disable();

        $exception = $this->profiler->loadProfile($token)->getCollector('exception')->getException();
        $template = $this->getTemplate();

        if (!$this->twig->getLoader()->exists($template)) {
            $handler = new ExceptionHandler();

            return new Response($handler->getContent($exception));
        }

        $code = $exception->getStatusCode();

        return new Response($this->twig->render(
            $template,
            array(
                'status_code'    => $code,
                'status_text'    => Response::$statusTexts[$code],
                'exception'      => $exception,
                'logger'         => null,
                'currentContent' => '',
            )
        ), 200, array('Content-Type' => 'text/html'));
    }
    
    /**
     * Renders the exception panel stylesheet for the given token.
     *
     * @param string $token The profiler token
     *
     * @return Response A Response instance
     */
    public function cssAction($token)
    {
        $this->profiler->disable();
    
        $exception = $this->profiler->loadProfile($token)->getCollector('exception')->getException();
        $template = $this->getTemplate();
    
        if (!$this->templateExists($template)) {
            $handler = new ExceptionHandler();
    
            return new Response($handler->getStylesheet($exception));
        }
    
        return new Response($this->twig->render('@WebProfiler/Collector/exception.css.twig'));
    }
    
    protected function getTemplate()
    {
        return '@Twig/Exception/'.($this->debug ? 'exception' : 'error').'.html.twig';
    }
    
    // to be removed when the minimum required version of Twig is >= 2.0
    protected function templateExists($template)
    {
        $loader = $this->twig->getLoader();
        if ($loader instanceof \Twig_ExistsLoaderInterface) {
            return $loader->exists($template);
        }
    
        try {
            $loader->getSource($template);
    
            return true;
        } catch (\Twig_Error_Loader $e) {
        }
    
        return false;
    }
}
