<?php

namespace Amplexor\XConnect\Request\File\Test;

use Amplexor\XConnect\Request\File\ZipFile;

class ZipFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Directory & file name to use in tests.
     */
    const ORDER_NAME = 'ORDER-NAME';
    const FILES_DIRECTORY = 'files';

    /**
     * File content to test with.
     */
    const XML_CONTENT = '<TestXml>TEST</TestXml>';


    /**
     * Directory name to use in the tests.
     *
     * @var string
     */
    private $directory;

    /**
     * Setup the environment to run the ZipFile tests.
     */
    public function setUp()
    {
        // Create the files directory if it does not exists yet.
        $directory = __DIR__ . '/files';
        if (!is_dir($directory)) {
            mkdir($directory, 0777);
        }

        // Cleanup old test files.
        $file = $directory . '/' . self::ORDER_NAME . '.zip';
        if (is_file($file)) {
            unlink($file);
        }

        $this->directory = $directory;
    }

    /**
     * Create an Order stub.
     *
     * @return Order.
     */
    protected function getOrderStub()
    {
        $order = $this->getMockBuilder('Amplexor\XConnect\Request\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $order->method('getOrderName')
            ->willReturn('ORDER-NAME');
        $order->method('getRequestDate')
            ->willReturn(new \DateTime());
        $order->method('getDueDate')
            ->willReturn(new \DateTime());
        $order->method('getTargetLanguages')
            ->willReturn(array('NL'));
        $order->method('getInstructions')
            ->willReturn(array());
        $order->method('getFiles')
            ->willReturn(array());

        return $order;
    }

    /**
     * Create an Request stub.
     */
    protected function getRequestStub()
    {
        $request = $this->getMockBuilder('Amplexor\XConnect\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request->method('getOrder')
            ->willReturn($this->getOrderStub());

        // Add a test translation file to the stub.
        $file = $this->createTranslationFile();
        $request->method('getFiles')
            ->willReturn(array(basename($file) => $file));

        // Add test content to the stub.
        $request->method('getContent')
            ->willReturn(array('content-file.xml' => self::XML_CONTENT));


        return $request;
    }

    /**
     * Get a to-translate file to test request file with.
     *
     * @return string
     *   The file path to the file.
     */
    protected function createTranslationFile()
    {
        $filePath = $this->directory . '/translation-file.xml';

        if (!file_exists($filePath)) {
            $file = fopen($filePath, 'w');
            fwrite($file, self::XML_CONTENT);
            fclose($file);
        }

        return $filePath;
    }

    /**
     * Test the file path.
     */
    public function testFilePaths()
    {
        $file = ZipFile::create($this->getRequestStub(), $this->directory);

        $expectedPath = $this->directory . '/' . self::ORDER_NAME . '.zip';
        $this->assertEquals($expectedPath, $file->getPath());

        $expectedFileName = self::ORDER_NAME . '.zip';
        $this->assertEquals($expectedFileName, $file->getFileName());

        $expectedDirectory = $this->directory;
        $this->assertEquals($expectedDirectory, $file->getDirectory());
    }

    /**
     * Test the creation of the zip file.
     */
    public function testZipFileCreation()
    {
        $file = ZipFile::create($this->getRequestStub(), $this->directory);

        $this->assertFileExists($file->getPath());
    }

    /**
     * Test the file destruction.
     */
    public function testZipFileDestruction()
    {
        $file = ZipFile::create($this->getRequestStub(), $this->directory);
        $filePath = $file->getPath();

        unset($file);

        $this->assertFileNotExists($filePath);
    }

    /**
     * Test of the order.xml is in the file.
     */
    public function testOrderInZipArchive()
    {
        $file = ZipFile::create($this->getRequestStub(), $this->directory);

        $zipArchive = new \ZipArchive();
        $zipArchive->open($file->getPath());

        $this->assertNotEmpty($zipArchive->statName('order.xml'));
    }

    /**
     * Test if a given file is in the file.
     */
    public function testFileInZipArchive()
    {
        $file = ZipFile::create($this->getRequestStub(), $this->directory);

        $zipArchive = new \ZipArchive();
        $zipArchive->open($file->getPath());

        $expected = 'Input/' . basename($this->createTranslationFile());
        $this->assertNotEmpty($zipArchive->statName($expected));
    }

    /**
     * Test if a given content is in the file.
     */
    public function testContentInZipArchive()
    {
        $file = ZipFile::create($this->getRequestStub(), $this->directory);

        $zipArchive = new \ZipArchive();
        $zipArchive->open($file->getPath());

        $expected = 'Input/content-file.xml';
        $this->assertNotEmpty($zipArchive->statName($expected));
    }

    /**
     * Test if an exception is thrown when the file can not be created.
     *
     * @expectedException Amplexor\XConnect\Request\File\FileException
     */
    public function testFileException()
    {
        ZipFile::create(
            $this->getRequestStub(),
            '/Non%Existing-FileDirectory/FooBar/TestBar'
        );
    }
}
