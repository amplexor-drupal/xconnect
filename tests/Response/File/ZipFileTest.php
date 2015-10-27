<?php

namespace Amplexor\XConnect\Response\File\ZipFile\Test;

use Amplexor\XConnect\Response\File\ZipFile;

class ZipFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get the test file path.
     *
     * @return string
     */
    protected function getFilePath()
    {
        return __DIR__ . '/files/response-test.zip';
    }


    /**
     * Test the exception when the file does not exists.
     *
     * @expectedException Amplexor\XConnect\Response\File\FileException
     * @expectedExceptionMessageRegExp /^File ".*" does not exists or is not readable\.$/
     */
    public function testConstructorNotReadableException()
    {
        new ZipFile(__DIR__ . '/files/fail.zip');
    }

    /**
     * Test the exception when the file is not a zip file.
     *
     * @expectedException Amplexor\XConnect\Response\File\FileException
     * @expectedExceptionMessageRegExp /^Can\'t open file ".*"\.$/
     */
    public function testConstructorCantOpenException()
    {
        new ZipFile(__DIR__ . '/files/not-zip.txt');
    }

    /**
     * Test getting the delivery info from the reponse.
     */
    public function testGetInfo()
    {
        $zip = new ZipFile($this->getFilePath());
        $info = $zip->getInfo();

        $this->assertInstanceOf(
            'Amplexor\XConnect\Response\Info',
            $info
        );

        $this->assertEquals('response-test', $info->getId());
    }

    /**
     * Test exception when trying to access non existing file within the package.
     *
     * @expectedException Amplexor\XConnect\Response\File\FileException
     * @expectedExceptionMessage File "non/existing/file.txt" not found in archive.
     */
    public function testGetContentException()
    {
        $zip = new ZipFile($this->getFilePath());
        $zip->getContent('non/existing/file.txt');
    }
}
