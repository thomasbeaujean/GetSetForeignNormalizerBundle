
    /**
     * @ORM\Column(type="guid", nullable=true)
     */
    protected $testGuid;

    /**
     * @ORM\Column(type="binary", nullable=true)
     */
    protected $testBinary;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    protected $testBlob;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    protected $testDatetimeTz;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    protected $testDateinterval;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $testArray;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $testSimpleArray;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $testJsonArray;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    protected $testObject;
