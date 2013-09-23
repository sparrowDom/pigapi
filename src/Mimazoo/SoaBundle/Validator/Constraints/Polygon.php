<?php

namespace Mimazoo\SoaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Polygon extends Constraint
{
	public $message = 'Value should be a valid geo location of type polygon';
}