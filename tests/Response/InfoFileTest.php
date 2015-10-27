<?php

namespace Amplexor\XConnect\Response\InfoFile\Test;

use Amplexor\XConnect\Response\InfoFile;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test raw XML string.
     *
     * @var string
     */
    private $rawXml = <<<EOF
        <DeliveryFile>
            <FileName>FILE-1.html</FileName>
            <FileSize>1768</FileSize>
            <SourceLangIsoCode>en-GB</SourceLangIsoCode>
            <TargetLangIsoCode>nl-BE</TargetLangIsoCode>
            <FileReference>Output/nl-BE/FILE-1.html</FileReference>
        </DeliveryFile>
EOF;

    /**
     * Get a SimpleXmlElement representing the DeliveryFile content.
     *
     * @return \SimpleXmlElement
     */
    protected function getXml()
    {
        return new \SimpleXMLElement($this->rawXml);
    }

    /**
     * Testing the getName method.
     */
    public function testGetName()
    {
        $infoFile = new InfoFile($this->getXml());
        $expected = 'FILE-1.html';

        $this->assertEquals($expected, $infoFile->getName());
    }

    /**
     * Test getting the file size.
     */
    public function testGetSize()
    {
        $infoFile = new InfoFile($this->getXml());
        $expected = 1768;

        $this->assertEquals($expected, $infoFile->getSize());
    }

    /**
     * Test getting the path.
     */
    public function testGetPath()
    {
        $infoFile = new InfoFile($this->getXml());
        $expected = 'Output/nl-BE/FILE-1.html';

        $this->assertEquals($expected, $infoFile->getPath());
    }

    /**
     * Test getting the source language.
     */
    public function testGetSourceLanguage()
    {
        $infoFile = new InfoFile($this->getXml());
        $expected = 'en-GB';

        $this->assertEquals($expected, $infoFile->getSourceLanguage());
    }

    /**
     * Test getting the target language.
     */
    public function testGetTargetLanguage()
    {
        $infoFile = new InfoFile($this->getXml());
        $expected = 'nl-BE';

        $this->assertEquals($expected, $infoFile->getTargetLanguage());
    }
}
