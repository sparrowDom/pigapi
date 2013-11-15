<?php

namespace Mimazoo\SoaBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;

use Symfony\Component\Yaml\Yaml;
use Guzzle\Common\Event;
use Mimazoo\SoaBundle\ValueObject\Polygon;
use Mimazoo\SoaBundle\ValueObject\Point;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Rest context.
 */
class RestContext extends BehatContext implements KernelAwareInterface
{

    private $_restObject        = null;
    private $_restObjectType    = null;
    private $_restObjectMethod  = 'get';
    private $_client            = null;
    private $_response          = null;
    private $_requestUrl        = null;
    private $_queryParameters = array();

    private $_parameters			= array();

    private $kernel;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here

        $this->_restObject  = new \stdClass();
        $this->_client      = new \Guzzle\Service\Client();
        $this->_queryParameters = array();
        
        //do not return exceptions on 4xx and 5xx responses guzzle fix
        $this->_client->getEventDispatcher()->addListener(
        		'request.error', 
        		function(Event $event) {
        			$event->stopPropagation();
        		},
        		-254
        );
        
    	$this->_parameters = $parameters;
    }
    
    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
    	$this->kernel = $kernel;
    }

    public function getParameter($name)
    {
    	if (count($this->_parameters) === 0) {

    		throw new \Exception('Parameters not loaded!');
    	} else {

            $parameters = $this->_parameters;
            return (isset($parameters[$name])) ? $parameters[$name] : null;
    	}
    }

     /**
     * @Given /^that I want to make a new "([^"]*)"$/
     */
    public function thatIWantToMakeANew($objectType)
    {
        $this->_restObjectType   = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'post';
    }
    
    /**
     * @Given /^that I want to update an? "([^"]*)"$/
     */
    public function thatIWantToUpdateA($objectType)
    {
    	$this->_restObjectType   = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'post';
    }
    
    /**
     * @Given /^that I want to patch update an? "([^"]*)"$/
     */
    public function thatIWantToPatchUpdateA($objectType)
    {
    	$this->_restObjectType   = ucwords(strtolower($objectType));
    	$this->_restObjectMethod = 'patch';
    }
    
    
     /**
     * @Given /^that I want to find an? "([^"]*)"$/
     */
    public function thatIWantToFindA($objectType)
    {
        $this->_restObjectType   = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'get';
    }
    
    /**
     * @Given /^that I want to delete an? "([^"]*)"$/
     */
    public function thatIWantToDeleteA($objectType)
    {
        $this->_restObjectType   = ucwords(strtolower($objectType));
        $this->_restObjectMethod = 'delete';
    }

    /**
     * @Given /^that its "([^"]*)" is "([^"]*)"$/
     */
    public function thatTheItsIs($propertyName, $propertyValue)
    {
        $this->_restObject->$propertyName = $propertyValue;
    }
    
    /**
     * @Given /^that its "([^"]*)" is boolean "([^"]*)"$/
     */
    public function thatItsIsBoolean($propertyName, $string)
    {
    	if ('true' === $string) {
    		$this->_restObject->$propertyName = true;
    	} else if ('false' === $string) {
    		$this->_restObject->$propertyName = false;
    	} else {
    		throw new \Exception('Value should be true or false');
    	}
    }
    
    
    /**
     * @Given /^that its "([^"]*)" is a geometry with WKT value "([^"]*)"$/
     */
    public function thatItsIsAGeometryWithWktValue($propertyName, $wkt)
    {
    	$geometryType = substr($wkt, 0, strpos($wkt, '('));
    	switch ($geometryType) {
    		case 'POINT':
    			list($longitude, $latitude) = sscanf($wkt, 'POINT(%f %f)');
    			$this->_restObject->$propertyName = array('type' => 'Point', 'coordinates' => array($longitude, $latitude));
    			break;
    		case 'POLYGON':
    			$polygonArr = array();
    			$polygonArr['type'] = 'Polygon';
    			$polygonArr['coordinates'] = Polygon::getObjectFromWkt($wkt)->getGeoJsonCoordinatesArr();
    			$this->_restObject->$propertyName = $polygonArr;
    			break;
    		default:
    			throw new \Exception('Geometry type not expected.');
    	}
    }
    
    /**
     * @Given /^that "([^"]*)" is a time range with json value '([^']*)'$/
     */
    public function thatIsATimeRangeWithJsonValue($propertyName, $json)
    {
    	$this->_restObject->$propertyName = $json;
    }
    
    /**
     * @When /^I request for first item in "([^"]*)"$/
     */
    public function iRequestForFirstItemIn($pageUrl)
    {
    	$response = $this->_client
    	->get($this->getParameter('base_url') . $pageUrl, $this->getOauthHeader())
    	->send();
    	
    	$entity = substr($pageUrl, (strrpos($pageUrl, '/') + 1),-1);
    	
    	$data = json_decode($response->getBody());
    	
    	if (isset($data->_embedded->$entity) && is_array($data->_embedded->$entity)) {
    		$firstId = current($data->_embedded->$entity)->id;
    		$this->iRequest($pageUrl . '/' . $firstId);
    	} else {
    		throw new \Exception('No item found');
    	}
    }

    /**
     * @Given /^that query parameter\'s "([^"]*)" value is "([^"]*)"$/
     */
    public function thatQueryParameterSValueIs($parameterName, $value)
    {
        $this->_queryParameters[$parameterName] = $value;
    }

    /**
     * @When /^I request "([^"]*)"$/
     */
    public function iRequest($pageUrl)
    {
        $baseUrl 			= $this->getParameter('base_url');
        $this->_requestUrl 	= $baseUrl.$pageUrl;

        $first = true;
        foreach($this->_queryParameters as $name => $value){
            $this->_requestUrl .= ($first ? '?' : '&') . $name .'='.$value;
            $first = false;
        }

        //every request should include oauth token
        //$oauth = $this->getOauthHeader();

        //TODO handle that you set a token here
        switch (strtoupper($this->_restObjectMethod)) {
            case 'GET':
                $response = $this->_client
                    ->get($this->_requestUrl)
                    ->send();
                break;
            case 'POST':
            	//$postFields = (array)$this->_restObject;
                $body = json_encode((array)$this->_restObject);
                $response = $this->_client
                    //->post($this->_requestUrl,$oauth,$postFields)
                    ->post($this->_requestUrl,null,$body)
                    ->setHeader("Content-Type", "application/json")
                    ->send();
                
                break;
            case 'PUT':
                $body = json_encode((array)$this->_restObject);
                $response = $this->_client
                	//->put($this->_requestUrl,$oauth,$body)
                    ->put($this->_requestUrl,null,$body)
                    ->setHeader("Content-Type", "application/json")
                	->send();
                break;
            case 'PATCH':
               $body = json_encode((array)$this->_restObject);
                $response = $this->_client
                	//->patch($this->_requestUrl,$oauth,$body)
                    ->patch($this->_requestUrl,null,$body)
                    ->setHeader("Content-Type", "application/json")
                	->send();
                break;
            case 'DELETE':
            	$response = $this->_client
            		//->delete($this->_requestUrl.'?'.http_build_str((array)$this->_restObject), $oauth)
                    ->delete($this->_requestUrl.'?'.http_build_str((array)$this->_restObject), null)
            		->send();
            	break;
            	
        }

        $this->_response = $response;
    }

    /**
     * @Then /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = json_decode($this->_response->getBody(true));

        if (empty($data)) {
            throw new \Exception("Response was not JSON\n" . $this->_response);
        }
    }


    /**
     * @Then /^the response data has an array property "([^"]*)" of length "([^"]*)"$/
     */
    public function theResponseDataHasAnArrayPropertyOfLength($propertyName, $arrayLength)
    {
        $data = json_decode($this->_response->getBody(true));

        if (!empty($data)) {
            $data = $data->data[0];
            if (!empty($data)) {
                if (!isset($data->$propertyName)) {
                    throw new \Exception("Property '".$propertyName."' is not set!\n");
                }
                if(!is_array($data->$propertyName)){
                    throw new \Exception("Property '".$propertyName."' is not an array!\n");
                }

                if(count($data->$propertyName) !== intval($arrayLength)){
                    print_r($data);
                    throw new \Exception("Array '".$propertyName."' is of length " . count($data->$propertyName) . " expected: " . $arrayLength . "!\n");
                }
            } else {
                throw new \Exception("Response data was empty\n" . $data);
            }
        } else {
            throw new \Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the response data is an array that has "([^"]*)" items$/
     */
    public function theResponseDataArrayCount($arraySize)
    {
        $data = json_decode($this->_response->getBody(true));

        if (!empty($data)) {
            $data = $data->data;
            if (!empty($data)) {
                if (count($data) != intval($arraySize)) {
                    throw new \Exception("Data has '".count($data)."' items and not '$arraySize' as expected!\n");
                }
            } else {
                throw new \Exception("Response data was empty\n" . $data);
            }
        } else {
            throw new \Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the response data has a "([^"]*)" property$/
     */
    public function theResponseDataHasAProperty($propertyName)
    {
        $data = json_decode($this->_response->getBody(true));

        if (!empty($data)) {
            $data = $data->data[0];
            if (!empty($data)) {
                if (!isset($data->$propertyName)) {
                    throw new \Exception("Property '".$propertyName."' is not set!\n");
                }
            } else {
                throw new \Exception("Response data was empty\n" . $data);
            }
        } else {
            throw new \Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^store the response "([^"]*)" property as new token$/
     */
    public function storeTheResponseProperty($propertyName)
    {
        $data = json_decode($this->_response->getBody(true));

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new \Exception("Property '".$propertyName."' is not set!\n");
            }
            $this->_queryParameters['token'] = $data->$propertyName;
        } else {
            throw new \Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the response has a "([^"]*)" property$/
     */
    public function theResponseHasAProperty($propertyName)
    {
        $data = json_decode($this->_response->getBody(true));

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new \Exception("Property '".$propertyName."' is not set!\n");
            }
        } else {
            throw new \Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the "([^"]*)" property equals "([^"]*)" of type "([^"]*)"$/
     */
    public function thePropertyEquals($propertyName, $propertyValue, $type)
    {

    	settype($propertyValue, $type);

    	$data = json_decode($this->_response->getBody(true));

        if (!empty($data)) {
        	if (!isset($data->$propertyName)) {
                throw new \Exception("Property '".$propertyName."' is not set!\n");
            }
            if ($data->$propertyName !== $propertyValue) {
            	throw new \Exception('Property value mismatch! (given: '.$propertyValue.', match: '.$data->$propertyName.')');
            }
        } else {
            throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the "([^"]*)" data property equals "([^"]*)" of type "([^"]*)"$/
     */
    public function theDataPropertyEquals($propertyName, $propertyValue, $type)
    {

        settype($propertyValue, $type);

        $data = json_decode($this->_response->getBody(true));

        if (empty($data)) {
            throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }

        $data = $data->data[0];

        if (empty($data)) {
            throw new Exception("Response data was empty!\n");
        }

        if (!isset($data->$propertyName)) {
            throw new \Exception("Property '".$propertyName."' is not set!\n");
        }
        if ($data->$propertyName !== $propertyValue) {
            throw new \Exception('Property value mismatch! (given: '.$propertyValue.', match: '.$data->$propertyName.')');
        }

    }

    /**
     * @Given /^the type of the "([^"]*)" property is ([^"]*)$/
     */
    public function theTypeOfThePropertyIsNumeric($propertyName,$typeString)
    {
        $data = json_decode($this->_response->getBody(true));

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new Exception("Property '".$propertyName."' is not set!\n");
            }
            // check our type
            switch (strtolower($typeString)) {
                case 'numeric':
                    if (!is_numeric($data->$propertyName)) {
                        throw new Exception("Property '".$propertyName."' is not of the correct type: " . $theTypeOfThePropertyIsNumeric . "!\n");
                    }
                    break;
            }

        } else {
            throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($httpStatus)
    {
    	
        if ((string)$this->_response->getStatusCode() !== $httpStatus) {
        	throw new \Exception('HTTP code does not match '.$httpStatus.
        		' (actual: '.$this->_response->getStatusCode().') body:' . $this->_response->getBody(true));
        }
    }

    /**
     * @Then /^the new resource has a "([^"]*)" property that equals "([^"]*)" of type "([^"]*)"$/
     */
    public function theNewResourceHasAPropertyThatEqualsOfType($name, $value, $type)
    {
    	echo $this->_response->getHeader('location')->__toString();
    	die();
    	$this->_response = $this->_client
    	->get($this->_response->getHeader('location')->__toString(), $this->getOauthHeader())
    	->send();
    	
    	$this->thePropertyEquals($name, $value, $type);
    }
    
    /**
     * Generate authorization header with oauth
     * 
     * @return array
     */
    public function getOauthHeader() {
    	return array('Authorization' => 'Bearer ' . $this->kernel->getContainer()->getParameter('oauth_token'));
    }
    
    
     /**
     * @Then /^echo last response$/
     */
    public function echoLastResponse()
    {
        $this->printDebug(
            $this->_requestUrl."\n\n".
            $this->_response
        );
    }
}