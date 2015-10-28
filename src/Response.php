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

namespace Amplexor\XConnect;

use Amplexor\XConnect\Response\Info;
use Amplexor\XConnect\Response\File\FileInterface;
use Amplexor\XConnect\Response\Translations;

/**
 * Class representing a translation response.
 */
class Response
{
    /**
     * The file that represents the response.
     *
     * @var FileInterface
     */
    private $file;

    /**
     * We cache the translations internally.
     *
     * @var Translations
     */
    private $translations;


    /**
     * Create a new response object by passing the file into it.
     *
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * Get the information attached to the response.
     *
     * @return Info
     */
    public function getInfo()
    {
        return $this->file->getInfo();
    }

    /**
     * Get the translations attached to the response.
     *
     * @return Translations
     */
    public function getTranslations()
    {
        if (!$this->translations) {
            $this->translations = new Translations($this->file);
        }

        return $this->translations;
    }
}
