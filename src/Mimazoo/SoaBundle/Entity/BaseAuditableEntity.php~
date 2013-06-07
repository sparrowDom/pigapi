<?php

namespace Mimazoo\SoaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;
use Mimazoo\SoaBundle\Entity\Traits\BaseTrait;

/**
 * @ORM\MappedSuperclass
 */
class BaseAuditableEntity implements Entity {
	
	use BaseTrait;
	
	/**
	 * @var \DateTime $created
	 *
	 * @ORM\Column(type="datetime")
	 * @Gedmo\Timestampable(on="create")
	 * @Groups({"always"})
	 */
	private $created;
	
	/**
	 * @var \DateTime $updated
	 *
	 * @ORM\Column(type="datetime")
	 * @Gedmo\Timestampable(on="update")
	 * @Groups({"always"})
	 */
	private $updated;
	
	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return BaseAuditableEntity
	 */
	public function setCreated($created)
	{
		$this->created = $created;

		return $this;
	}
	
	/**
	 * Get created
	 *
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}
	
	/**
	 * Set updated
	 *
	 * @param \DateTime $updated
	 * @return BaseAuditableEntity
	 */
	public function setUpdated($updated)
	{
		$this->updated = $updated;
	
		return $this;
	}
	
	/**
	 * Get updated
	 *
	 * @return \DateTime
	 */
	public function getUpdated()
	{
		return $this->updated;
	}
}
