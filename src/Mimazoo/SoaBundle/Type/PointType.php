<?php

namespace Mimazoo\SoaBundle\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

use Mimazoo\SoaBundle\ValueObject\Point;

class PointType extends Type
{
	const POINT = 'point';

	public function getName()
	{
		return self::POINT;
	}

	public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return 'POINT';
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if (NULL === $value) {
			return NULL;
		} else {
			list($longitude, $latitude) = sscanf($value, 'POINT(%f %f)');
			return new Point($longitude, $latitude);
		}
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value instanceof Point) {
			$value = $value->getWkt();
		}

		return $value;
	}

	public function canRequireSQLConversion()
	{
		return true;
	}

	public function convertToPHPValueSQL($sqlExpr, $platform)
	{
		return sprintf('AsText(%s)', $sqlExpr);
	}

	public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
	{
		return sprintf('PointFromText(%s)', $sqlExpr);
	}
	
}