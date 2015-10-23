<?php

namespace Amplexor\XConnect;

use Amplexor\XConnect\Request\Order;

/**
 * Class representing a translation request.
 */
class Request
{
    /**
     * The order that is part of the request.
     *
     * @var Order
     */
    private $order;

    /**
     * The files that are part of the request.
     *
     * @var array
     */
    private $files = array();

    /**
     * The content that is part of the request.
     *
     * @var array
     */
    private $content = array();


    /**
     * Create a new request by passing the configuration to it.
     *
     * @param string $sourceLanguage
     *   The source language for the request.
     * @param array $config
     *   The configuration for the request.
     */
    public function __construct($sourceLanguage, array $config)
    {
        $this->order = new Order($sourceLanguage, $config);
    }

    /**
     * Get the order object from the request.
     *
     * @return Order
     *   The order object.
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Add a translation target language for the request.
     *
     * @param string $language
     *   The language to translate the request to.
     */
    public function addTargetLanguage($language)
    {
        $this->order->addTargetLanguage($language);
    }

    /**
     * Add a translation instruction.
     *
     * @param string $instruction
     *   The instruction to add to the request.
     */
    public function addInstruction($instruction)
    {
        $this->order->addInstruction($instruction);
    }

    /**
     * Set the reference for the translation.
     *
     * @param string $reference
     *   The client refenece to use in all communication about the request.
     */
    public function setReference($reference)
    {
        $this->order->setReference($reference);
    }

    /**
     * Get the translation files that are part of the request.
     *
     * @return array
     *   Array of 'filename.ext' => 'full/path/to/filename.ext'
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add a file path to a file that needs to be translated.
     *
     * @param string $filePath
     *   A local file path to the file that needs to be included in the request.
     */
    public function addFile($filePath)
    {
        $fileName = basename($filePath);
        $this->files[$fileName] = $filePath;
        $this->getOrder()->addFile($fileName);
    }

    /**
     * Get the translation files content thatt are part of the request.
     *
     * @return array
     *   Array of 'filename.ext' => 'content string'.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add content strings to the request by providing a filename and string.
     *
     * @param string $fileName
     *   The file name to use to pass the content string.
     * @param string $content
     *   The file content as a string.
     */
    public function addContent($fileName, $content)
    {
        $this->content[$fileName] = $content;
        $this->getOrder()->addFile($fileName);
    }
}
