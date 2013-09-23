<?php

namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Point extends Constraint
{
	public $message = 'Value should be a valid geo location of type point';
}