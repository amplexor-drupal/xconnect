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

namespace Amplexor\XConnect\Response\File;

use Amplexor\XConnect\Response\Info;
use Amplexor\XConnect\Response\File\FileException;

interface FileInterface
{
    /**
     * Get the file info.
     *
     * @return Info
     */
    public function getInfo();

    /**
     * Get the content of the given file.
     *
     * @param string $filePath
     *   The file path within the file.
     *
     * @return string
     *   The found content.
     *
     * @throws FileException
     *   When the requested file is not available.
     */
    public function getContent($filePath);
}
