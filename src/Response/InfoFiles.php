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

use Amplexor\XConnect\Response\InfoFile;

/**
 * Class representing a single delivery file details.
 */
class InfoFiles implements \Iterator, \Countable
{
    /**
     * The array collection.
     *
     * @var InfoFile[]
     */
    private $files = [];

    /**
     * The current item.
     *
     * @var int
     */
    private $pointer = 0;

    /**
     * Construct the object by passing the \SimpleXmlElement.
     *
     * @param \SimpleXmlElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        if (empty($xml)) {
            return;
        }

        // Single elements are by default not an array.
        if (1 < count($xml->DeliveryFile)) {
            foreach ($xml->DeliveryFile as $fileXml) {
                $this->files[] = new InfoFile($fileXml);
            }
            return;
        }

        // Add a single file to the collection.
        $this->files[] = new InfoFile($xml->DeliveryFile);
    }

    /**
     * Get the current file.
     *
     * @return InfoFile
     */
    public function current()
    {
        return $this->files[$this->pointer];
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return array_key_exists($this->pointer, $this->files);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->files);
    }
}
