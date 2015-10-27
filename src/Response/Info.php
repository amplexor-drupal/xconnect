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
 * Class representing the delivery details.
 */
class Info
{
    /**
     * The XML.
     *
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Constructor.
     *
     * @param string $rawXml
     *   The raw XML as received from the translation service.
     */
    public function __construct($rawXml)
    {
        $this->xml = new \SimpleXMLElement($rawXml);
    }

    /**
     * Get the ID.
     *
     * @return string
     *   The DeliveryId property.
     */
    public function getId()
    {
        return (string) $this->xml->DeliveryId;
    }

    /**
     * Get the Date.
     *
     * @return DateTime
     *   The DeliveryDate property.
     */
    public function getDate()
    {
        return new \DateTime((string) $this->xml->DeliveryDate);
    }

    /**
     * Get the status.
     *
     * @return string
     *   The DeliveryStatus property.
     */
    public function getStatus()
    {
        return (string) $this->xml->DeliveryStatus;
    }

    /**
     * Get the reference.
     *
     * @return string
     *   The Client reference related to this translation.
     */
    public function getReference()
    {
        return (string) $this->xml->WOClientReference;
    }

    /**
     * Get the person who issued the translation.
     *
     * @return string
     *   The IssuedBy property.
     */
    public function getIssuedBy()
    {
        return (string) $this->xml->IssuedBy;
    }

    /**
     * Get an array with info about the files within the translation delivery.
     *
     * @return InfoFiles
     *   Array with info for each file.
     */
    public function getFiles()
    {
        return new InfoFiles($this->xml->DeliveryFiles);
    }
}
