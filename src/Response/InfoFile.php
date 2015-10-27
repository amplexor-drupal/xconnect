<?php
/**
 * This file is part of the Amplexor\XConnect library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/amplexor-drupal/xconnect/
 * @version 1.0.0
 * @package Amplexor.XConnect
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amplexor\XConnect\Response;

/**
 * Class representing a single delivery file details.
 */
class InfoFile
{
    /**
     * The \SimpleXml wrapper around the XML data.
     *
     * @var \SimpleXmlElement
     */
    private $xml;


    /**
     * Construct a new object by passing the SimpleXmlElement.
     *
     * @param \SimpleXmlElement
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    /**
     * Get the fileName.
     *
     * @return string.
     */
    public function getName()
    {
        return (string) $this->xml->FileName;
    }

    /**
     * Get the FileSize.
     *
     * @return int
     */
    public function getSize()
    {
        return (int) $this->xml->FileSize;
    }

    /**
     * Get the filePath within the Response Zip package.
     *
     * @return string
     */
    public function getPath()
    {
        return (string) $this->xml->FileReference;
    }

    /**
     * Get the source language.
     *
     * @return string
     */
    public function getSourceLanguage()
    {
        return (string) $this->xml->SourceLangIsoCode;
    }

    /**
     * Get the target language.
     *
     * @return string
     */
    public function getTargetLanguage()
    {
        return (string) $this->xml->TargetLangIsoCode;
    }
}
