<?php
namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IbanValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!empty($value) && !preg_match('/^[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}$/', $value, $matches)) {
			$this->context->addViolation($constraint->message, array('%string%' => $value));
		}
	}
}