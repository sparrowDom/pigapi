<?php
/**
 * Custom view handler for supporting Hal objects and serializing them to json and xml
 * 
 * @author mitja
 *
 */
namespace Mimazoo\SoaBundle\View;

use Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nocarrier\Hal;
use Mimazoo\SoaBundle\View\Exception\HalViewHandlerException;
use FOS\Rest\Util\Codes;
use FOS\RestBundle\Util\ExceptionWrapper;

class HalViewHandler
{
	
	/**
	 * Deserialize Hal object using Nocarrier's Hal library instead of using JMS Serializer
	 * 
	 * @param ViewHandler $viewHandler
	 * @param View $view
	 * @param Request $request
	 * @param string $format
	 *
	 * @return Response
	 */
	public function createResponse(ViewHandler $handler, View $view, Request $request, $format) {
		
		$hal = $view->getData();
		
		//if not hal object process it with default view handler
		if (!($hal instanceof Hal)) {
			return $handler->createResponse($view, $request, $format);
		}
		
		switch ($format) {
			case 'json':
				$content = $hal->asJson();
				
				break;

			case 'xml':
				$content = $hal->asXml();
				break;

			default:
				throw new HttpException(500, 'Custom HalViewHandler is misconfigured. Formats for deserializing HAL objects should be json or xml.');
		}
				
		$response = $view->getResponse();
		$response->setContent($content);
		
		$response->setStatusCode($this->getStatusCode($view));
		
		if (!$response->headers->has('Content-Type')) {
			$response->headers->set('Content-Type', $request->getMimeType($format));
		}
		
		return $response;
		
	}	
	
	/**
	 * FOSRest's ViewHandler has a private getStatusCode method. Because we cannot use it, we added
	 * this lightweight method (without forms support) for Hal custom handler.
	 *
	 * @param View $view view instance
	 * 
	 * @return int HTTP status code
	 */
	private function getStatusCode(View $view)
	{
		if (null !== ($code = $view->getStatusCode())) {
			return $code;
		}
	
		return Codes::HTTP_OK;
	}
	
}