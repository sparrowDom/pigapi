<?php
namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!empty($value) && !preg_match('/^\+\d+\s\d{4,}$/', $value, $matches)) {
			$this->context->addViolation($constraint->message, array('%string%' => $value));
		}
	}
}