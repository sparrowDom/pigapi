<?php
namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Mimazoo\SoaBundle\ValueObject\Point as PointValueObject;

class PointValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!empty($value) && !($value instanceof PointValueObject)) {
			$this->context->addViolation($constraint->message, array('%string%' => $value));
		}
		
	}
}

