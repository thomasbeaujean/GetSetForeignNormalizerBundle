<?php
namespace tbn\GetSetForeignNormalizerBundle\GetSetForeignNormalizerBundle\Tests\Fixtures\AppTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use tbn\GetSetForeignNormalizerBundle\GetSetForeignNormalizerBundle\Tests\Fixtures\AppTestBundle\Entity\Traits;

/**
 *
 * @author Thomas BEAUJEAN
 *
 * @ORM\Entity()
 *
 */
class TcReference
{
    use Traits\IdTrait;
    use Traits\NameTrait;
}
