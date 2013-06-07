<?php

namespace Mimazoo\SoaBundle\Utils;
use Mimazoo\SoaBundle\Utils\Exception\RegexException;


class Regex
{
	
	/**
	 * Get resource id from absolute url
	 * 
	 * @param string $url
	 * @param string $baseUrl
	 * @throws \Exception
	 * 
	 * @return integer
	 */
	public static function getIdFromUrl($url) {
		
		$url = filter_var($url, FILTER_VALIDATE_URL);
		
		$pattern = '|^http://[^/]+(.*)/([0-9]+)(\\?.*)?$|' ;
			
    	preg_match($pattern, $url, $matches);
    	
    	if (count($matches) < 3) {
    		throw new RegexException('Invalid url. Id cannot be extracted.');
    	}
    	
    	return  $matches[2];
		
	}
	
	
}