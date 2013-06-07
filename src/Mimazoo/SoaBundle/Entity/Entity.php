<?php

namespace Mimazoo\SoaBundle\Entity;
use Symfony\Component\DependencyInjection\Container;

/**
 * Interface that define entity class
 * @author mitja
 */
interface Entity {
	public function fromArray ($fieldsArr);
	public function getLowerCaseEntityName();
	public function getLinks(Container $container, $absolute = true);
	public function prepareSelfUrl();
}