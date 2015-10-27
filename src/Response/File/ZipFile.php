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

class ZipFile implements FileInterface
{
    /**
     * The ZipArchive.
     *
     * @var \ZipArchive
     */
    private $zip;

    /**
     * The Info object retrieved from the ZipFile.
     *
     * @var Info
     */
    private $info;

    /**
     * The filepath where the file is opened from.
     *
     * @var string
     */
    private $filePath;


    /**
     * Create the object based on the file path.
     *
     * @param string $filePath
     *   The full file path to the Zip file.
     *
     * @throws FileException
     *   When the file can not be opened.
     */
    public function __construct($filePath)
    {
        if (!is_readable($filePath)) {
            throw new FileException(
                sprintf(
                    'File "%s" does not exists or is not readable.',
                    $filePath
                )
            );
        }

        // Create and open the archive.
        $zip = new \ZipArchive();
        $result = $zip->open($filePath);

        if ($result !== true) {
            throw new FileException(
                sprintf(
                    'Can\'t open file "%s".',
                    $filePath
                )
            );
        }

        $this->zip = $zip;
        $this->filePath = $filePath;
    }

    /**
     * Close the ZipArchive when the object is destructed.
     */
    public function __destruct()
    {
        if ($this->zip) {
            $this->zip->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function getInfo()
    {
        if (!$this->info) {
            $fileName = basename($this->filePath);
            $infoName = preg_replace('/\.zip$/', '', $fileName) . '.xml';

            $this->info = new Info(
                $this->getContent($infoName)
            );
        }

        return $this->info;
    }

    /**
     * @inheritDoc
     */
    public function getContent($path)
    {
        $content = '';
        $fp = $this->zip->getStream($path);

        if (!$fp) {
            throw new FileException(
                sprintf('File "%s" not found in archive.', $path)
            );
        }

        while (!feof($fp)) {
            $content .= fread($fp, 2);
        }
        fclose($fp);

        return $content;
    }
}