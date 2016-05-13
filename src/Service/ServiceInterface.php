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

namespace Amplexor\XConnect\Service;

use Amplexor\XConnect\Request\File\FileInterface;

interface ServiceInterface
{
    /**
     * Send a request file to the service.
     *
     * @param FileInterface $file
     *   The request file to send.
     *
     * @return bool
     *   Success.
     *
     * @throws ServiceException
     *   When the file could not be transferred to the remote location.
     */
    public function send(FileInterface $file);

    /**
     * Scan the remote directory for translation results.
     *
     * @return array
     *   Array of file names on the remote location.
     */
    public function scan();

    /**
     * Get a translation result from the remote location.
     *
     * @param string $fileName
     *   The remote file name to get.
     * @param string $directory
     *   The local directory to download the file to.
     *
     * @return string
     *   Locale file path.
     *
     * @throws ServiceException
     *   When the file could not be downloaded from the remote location.
     */
    public function receive($fileName, $directory);

    /**
     * Let the remote service know that the received translation is processed.
     *
     * @param string $fileName
     *   The remote file name we processed.
     *
     * @throws ServiceException
     *   When the file could not be moved to the processed folder on the remote
     *   location.
     */
    public function processed($fileName);
}
