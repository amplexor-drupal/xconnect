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

use Amplexor\XConnect\Response\File\FileInterface;
use Amplexor\XConnect\Response\Translation;

/**
 * Class representing a collection of translations.
 */
class Translations implements \Iterator, \Countable
{
    /**
     * The collection.
     *
     * @var Translation[]
     */
    private $translations = [];

    /**
     * The current item pointer.
     *
     * @var int
     */
    private $pointer = 0;


    /**
     * Construct the translations collection from the info file.
     *
     * @param FileInterface $file
     *   The Response file object.
     */
    public function __construct(FileInterface $file)
    {
        $infoFiles = $file->getInfo()->getFiles();
        foreach ($infoFiles as $infoFile) {
            $this->translations[] = new Translation($file, $infoFile);
        }
    }

    /**
     * Get the current translation.
     */
    public function current()
    {
        if (!$this->valid()) {
            return;
        }
        return $this->translations[$this->pointer];
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
        return array_key_exists($this->pointer, $this->translations);
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
        return count($this->translations);
    }
}
