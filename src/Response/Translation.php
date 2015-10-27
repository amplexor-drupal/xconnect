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
use Amplexor\XConnect\Response\InfoFile;

/**
 * Class representing a single translation within the response.
 */
class Translation
{
    /**
     * The response file.
     *
     * @var FileInterface
     */
    private $file;

    /**
     * The file information.
     *
     * @var InfoFile
     */
    private $info;


    /**
     * Construct a new translation by passing te response file and the filename.
     *
     * @param FileInterface $file
     *   The Response file object.
     * @param InfoFile $info
     *   The file info object.
     */
    public function __construct(FileInterface $file, InfoFile $info)
    {
        $this->file = $file;
        $this->info = $info;
    }

    /**
     * Get the file info.
     *
     * @return InfoFile
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Get the translation content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->file->getContent(
            $this->info->getPath()
        );
    }
}