<?php

namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Iban extends Constraint
{
	public $message = 'Value should be a valid IBAN account number.';
}