<?php

namespace Amplexor\XConnect\Request;

/**
 * Class that represents the X-Connect order.
 */
class Order
{
    /**
     * The order name.
     *
     * @var string
     */
    private $orderName;

    /**
     * The source language.
     *
     * @var string
     */
    private $sourceLanguage;

    /**
     * The target languages.
     *
     * @var array
     */
    private $targetLanguages = array();

    /**
     * The order instruction.
     *
     * @var array
     */
    private $instructions = array();

    /**
     * The client reference.
     *
     * @var string
     */
    private $reference;

    /**
     * The configuration.
     *
     * @var array
     */
    private $config = array(
        'clientId'          => '',
        'orderNamePrefix'   => '',
        'templateId'        => '',
        'dueDate'           => 0,
        'issuedBy'          => '',
        'isConfidential'    => false,
        'service'           => '',
        'needsConfirmation' => true,
        'needsQuotation'    => false,
    );

    /**
     * Array of files that are part of the order.
     *
     * @var array
     */
    private $files = array();


    /**
     * Create a new order.
     *
     * @param string $source_language
     *   The source language for the translation order.
     * @param array $config
     *   The configuration elements for the order.
     */
    public function __construct($source_language, $config = array())
    {
        $this->sourceLanguage = $source_language;
        $this->setConfig($config);

        $this->orderName = $this->createOrderName();
    }

    /**
     * Get the source language.
     *
     * @return string
     *   The source language of the translation.
     */
    public function getSourceLanguage()
    {
        return $this->sourceLanguage;
    }

    /**
     * Helper function to generate the order name when the object is created.
     *
     * @return string
     */
    protected function createOrderName()
    {
        $prefix = !empty($this->config['orderNamePrefix'])
            ? $this->config['orderNamePrefix']
            : 'translation_order';

        // Add timestamp.
        $timestamp = new \DateTime();

        // Get a 3-digit microsecond representation.
        $microseconds = floor((substr((string) microtime(), 1, 8) * 1000));
        $microseconds = str_pad($microseconds, 3, '0', STR_PAD_LEFT);

        return sprintf(
            '%s_%s%s',
            $prefix,
            $timestamp->format('YmdHis'),
            $microseconds
        );
    }

    /**
     * Fill in the configuration based on the given config array.
     *
     * @param array $config
     *   The config array to store.
     */
    protected function setConfig($config)
    {
        $config_keys = array_keys($this->config);
        foreach ($config_keys as $key) {
            if (isset($config[$key])) {
                $this->config[$key] = $config[$key];
            }
        }
    }

    /**
     * Get the order name.
     *
     * @return string
     *   The order name.
     */
    public function getOrderName()
    {
        return $this->orderName;
    }

    /**
     * Get the client id.
     *
     * @return string
     *   The client ID.
     */
    public function getClientId()
    {
        return $this->config['clientId'];
    }

    /**
     * Get the template id.
     *
     * @todo: get data from where?
     *
     * @return string
     *   The template ID.
     */
    public function getTemplateId()
    {
        return $this->config['templateId'];
    }

    /**
     * Get the request date.
     *
     * @return DateTime
     *   The date time presentation of the request date.
     */
    public function getRequestDate()
    {
        $date = new \DateTime();
        return $date;
    }

    /**
     * Get the request due date.
     *
     * The date is calculated based on the dueDate value in the settings.
     *
     * @return DateTime
     *   The date time presentation of the due date.
     */
    public function getDueDate()
    {
        // When no due date interval given, use today.
        $dueDate = new \DateTime();
        if (empty($this->config['dueDate'])) {
            return $dueDate;
        }

        // Calculate the date by adding the dueDate interval.
        $interval = new \DateInterval(
            'P' . (int) $this->config['dueDate'] . 'D'
        );
        $dueDate->add($interval);

        return $dueDate;
    }

    /**
     * Get the issuer identifier.
     *
     * @return string
     *   The email address.
     */
    public function getIssuedBy()
    {
        return $this->config['issuedBy'];
    }

    /**
     * Get if the translation content is confidential.
     *
     * @return bool
     *   Translation vcontent is confidential true/false.
     */
    public function isConfidential()
    {
        return (bool) $this->config['isConfidential'];
    }

    /**
     * Add a target language.
     *
     * @param string $language
     *   The target language to add to the order.
     */
    public function addTargetLanguage($language)
    {
        if (!in_array($language, $this->targetLanguages)) {
            $this->targetLanguages[] = $language;
        }
    }

    /**
     * Get the target languages.
     *
     * @return array
     *   The target language codes.
     */
    public function getTargetLanguages()
    {
        return $this->targetLanguages;
    }

    /**
     * Get the service name.
     *
     * @return string
     *   The Service name.
     */
    public function getService()
    {
        return $this->config['service'];
    }

    /**
     * Add instruction to the order.
     *
     * @param string $instruction
     *   The instruction.
     */
    public function addInstruction($instruction)
    {
        $this->instructions[] = $instruction;
    }

    /**
     * Get the client instruction.
     *
     * @return string
     *   The client instruction regarding the translation.
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Set the client reference.
     *
     * @param string $reference
     *   The reference to use in the order.
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get the client reference.
     *
     * @return string
     *   The client reference.
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Is there a confirmation needed before the translation may be processed.
     *
     * @return bool
     *   Needs confirmation true/false.
     */
    public function needsConfirmation()
    {
        return (bool) $this->config['needsConfirmation'];
    }

    /**
     * Get if an quotation is required before translation is processed.
     *
     * @return bool
     *   Needs quotation true/false.
     */
    public function needsQuotation()
    {
        return (bool) $this->config['needsQuotation'];
    }

    /**
     * Add an input file to the order.
     *
     * @param string $file_name
     *   The file name of the file to add to the order.
     */
    public function addFile($file_name)
    {
        if (!in_array($file_name, $this->files)) {
            $this->files[] = $file_name;
        }
    }

    /**
     * Get the input files.
     *
     * @return array
     *   The input file names part of the translation order.
     */
    public function getFiles()
    {
        return $this->files;
    }
}
