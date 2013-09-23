<?php

namespace Mimazoo\SoaBundle\Tests\Utils;

use Mimazoo\SoaBundle\Utils\Regex;


class RegexTest extends \PHPUnit_Framework_TestCase
{
	
	public function providerOkGetIdFromUrl()
	{
		return array(
			array('http://api.buksl.loc/app_dev.php/providers/2/employees/5?expand=selectionItems', 5),
			array('http://api.buksl.loc/app_dev.php/providers/2/employees/7', 7),
		);
	}
	
	/**
	 * @dataProvider providerOkGetIdFromUrl
	 */
	public function testGetIdFromUrlOk($url, $expectedValue)
    {
        $this->assertEquals( $expectedValue, Regex::getIdFromUrl($url) );
    }
    
   public function providerExceptionGetIdFromUrl()
	{
		return array(
			array('http://api.buksl.loc/app_dev.php/providers/2/employees'),
		);
	}
    
    /**
     * @dataProvider providerExceptionGetIdFromUrl
     * @expectedException Mimazoo\SoaBundle\Utils\Exception\RegexException
     */
    public function testGetIdFromUrlException($url)
    {
    	Regex::getIdFromUrl($url);
    }
    
}