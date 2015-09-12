<?php
namespace tbn\GetSetForeignNormalizerBundle\Tests\Fixtures\AppTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use tbn\GetSetForeignNormalizerBundle\Tests\Fixtures\AppTestBundle\Entity\Traits;

/**
* the doctrine mapping type:
* http://doctrine-dbal.readthedocs.org/en/latest/reference/types.html
*
*   smallint
*   integer
*   bigint
*   decimal
*   float
*   string
*   text
*   guid
*   binary
*   blob
*   boolean
*   date
*   datetime
*   datetimetz
*   time
*   dateinterval
*   array
*   simple_array
*   json_array
*   object
*
*/

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


    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $testSmallInt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $testInteger;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $testBigint;

        /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $testFloat;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $testBoolean = null;

    function getTestBoolean()
    {
        return $this->testBoolean;
    }

    function setTestBoolean($testBoolean)
    {
        $this->testBoolean = $testBoolean;
    }


    function getTestFloat()
    {
        return $this->testFloat;
    }

    function setTestFloat($testFloat)
    {
        $this->testFloat = $testFloat;
    }


    function getTestSmallInt()
    {
        return $this->testSmallInt;
    }

    function getTestInteger()
    {
        return $this->testInteger;
    }

    function setTestSmallInt($testSmallInt)
    {
        $this->testSmallInt = $testSmallInt;
    }

    function setTestInteger($testInteger)
    {
        $this->testInteger = $testInteger;
    }
    function getTestBigint()
    {
        return $this->testBigint;
    }

    function setTestBigint($testBigint)
    {
        $this->testBigint = $testBigint;
    }



}
