<?php
namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Mimazoo\SoaBundle\ValueObject\Polygon as PolygonValueObject;

class PolygonValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!empty($value) && !($value instanceof PolygonValueObject)) {
			$this->context->addViolation($constraint->message, array('%string%' => $value));
		}
		
	}
}

