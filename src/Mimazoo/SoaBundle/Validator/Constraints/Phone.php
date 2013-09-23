<?php

namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Phone extends Constraint
{
	public $message = '"This is not a valid phone number, format should be /^\+\d+\s\d{4,}/  like +386 41123456';
}