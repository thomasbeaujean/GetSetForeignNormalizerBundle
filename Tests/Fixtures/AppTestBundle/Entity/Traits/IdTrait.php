<?php

namespace tbn\GetSetForeignNormalizerBundle\GetSetForeignNormalizerBundle\Tests\Fixtures\AppTestBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer",nullable=false)
     */
    protected $id;

    /**
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}