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

namespace Amplexor\XConnect\Request\File;

use Amplexor\XConnect\Request;
use Amplexor\XConnect\Request\Encoder\XmlEncoder;

/**
 * Request as a Zip file.
 */
class ZipFile implements FileInterface
{
    /**
     * The filePath where the file is located.
     *
     * @var string
     */
    private $filePath;

    /**
     * Constructor.
     *
     * @param string $filePath
     *   The path where the file is located.
     */
    private function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Destructor.
     *
     * Destructing the file object will delete the file from the filesystem.
     */
    public function __destruct()
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    /**
     * @inheritDoc
     */
    public static function create(Request $request, $directory)
    {
        $order = $request->getOrder();

        // Create the filename based on the order name.
        $zipFilePath = sprintf(
            '%s/%s.zip',
            rtrim(rtrim($directory), '/'),
            $order->getOrderName()
        );

        // Create a new ZipFile on disk.
        $zip = new \ZipArchive();
        $zip->open($zipFilePath, \ZipArchive::CREATE);

        // Add the order to the archive as an xml file.
        $encoder = new XmlEncoder();
        $zip->addFromString('order.xml', $encoder->encode($order));

        // Add the files to the archive.
        foreach ($request->getFiles() as $fileName => $filePath) {
            $zip->addFile($filePath, 'Input/' . $fileName);
        }

        // Add the content to the archive.
        foreach ($request->getContent() as $fileName => $content) {
            $zip->addFromString('Input/' . $fileName, $content);
        }

        // Close the file so the content gets compressed and the file is saved.
        $result = $zip->close();

        // Check if no errors.
        if ($result !== true) {
            throw new FileException(
                sprintf('Can\'t create the zip archive "%s"', $zipFilePath)
            );
        }

        return new static($zipFilePath);
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->filePath;
    }
}
