<?php

namespace Mimazoo\SoaBundle\ValueObject;

use Mimazoo\SoaBundle\ValueObject\Exception\PointException;


/**
 * Basic geospatial type, latitude and longitude coordinates representing point.
 * 
 * @author mitja
 */
class Point
{

	/**
	 * @param float $latitude
	 * @param float $longitude
	 */
	public function __construct($longitude, $latitude)
	{
		if ( !is_numeric($longitude) || !is_numeric($latitude) ) {
			throw new PointException('Longitude and latitude should be numbers.');
		}
		
		$this->longitude = $longitude;
		$this->latitude  = $latitude;
		
	}

	/**
	 * @return float
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}
	
	/**
	 * @return float
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}
	
	/**
	 * Generates WKT -> The Well-Known Textrepresentation of Geometry is designed to exchange geometry data in ASCII form.
	 * 
	 * @return string
	 */
	public function getWkt($justValue = false) {
		
		$wktStr = $this->getLongitude() . ' ' . $this->getLatitude(); 
		
		if (!$justValue) {
			$wktStr = 'POINT(' . $wktStr . ')';
		}
		
		return $wktStr;
		
	}
	
	public function getGeoJsonCoordinatesArr() {
		return array($this->getLongitude(), $this->getLatitude());
	}
	
}