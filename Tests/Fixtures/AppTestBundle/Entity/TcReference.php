<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests\Fixtures\AppTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use tbn\GetSetForeignNormalizerBundle\Tests\Fixtures\AppTestBundle\Entity\Traits;

/**
 * the doctrine mapping type:
 * http://doctrine-dbal.readthedocs.org/en/latest/reference/types.html.
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
 */

/**
 * @author Thomas BEAUJEAN
 *
 * @ORM\Entity()
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

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $testDate;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $testTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $testDatetime;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    protected $testDecimal;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $testString;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $testText;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $testArray;

    function getTestArray()
    {
        return $this->testArray;
    }

    function setTestArray($testArray)
    {
        $this->testArray = $testArray;
    }


    function getTestText()
    {
        return $this->testText;
    }

    function setTestText($testText)
    {
        $this->testText = $testText;
    }


    function getTestString()
    {
        return $this->testString;
    }

    function setTestString($testString)
    {
        $this->testString = $testString;
    }

        function getTestDecimal()
    {
        return $this->testDecimal;
    }

    function setTestDecimal($testDecimal)
    {
        $this->testDecimal = $testDecimal;
    }


    public function getTestDatetime()
    {
        return $this->testDatetime;
    }

    public function setTestDatetime($testDatetime)
    {
        $this->testDatetime = $testDatetime;
    }

    public function getTestTime()
    {
        return $this->testTime;
    }

    public function setTestTime($testTime)
    {
        $this->testTime = $testTime;
    }

    public function getTestDate()
    {
        return $this->testDate;
    }

    public function setTestDate($testDate)
    {
        $this->testDate = $testDate;
    }

    public function getTestBoolean()
    {
        return $this->testBoolean;
    }

    public function setTestBoolean($testBoolean)
    {
        $this->testBoolean = $testBoolean;
    }

    public function getTestFloat()
    {
        return $this->testFloat;
    }

    public function setTestFloat($testFloat)
    {
        $this->testFloat = $testFloat;
    }

    public function getTestSmallInt()
    {
        return $this->testSmallInt;
    }

    public function getTestInteger()
    {
        return $this->testInteger;
    }

    public function setTestSmallInt($testSmallInt)
    {
        $this->testSmallInt = $testSmallInt;
    }

    public function setTestInteger($testInteger)
    {
        $this->testInteger = $testInteger;
    }
    public function getTestBigint()
    {
        return $this->testBigint;
    }

    public function setTestBigint($testBigint)
    {
        $this->testBigint = $testBigint;
    }
}
