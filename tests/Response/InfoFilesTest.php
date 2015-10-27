<?php

namespace Amplexor\XConnect\Response\InfoFiles\Test;

use Amplexor\XConnect\Response\InfoFiles;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test raw XML string.
     *
     * @var string
     */
    private $rawXml = <<<EOF
    <DeliveryFiles>
        <DeliveryFile>
            <FileName>FILE-1.html</FileName>
            <FileSize>1855</FileSize>
            <SourceLangIsoCode>en-GB</SourceLangIsoCode>
            <TargetLangIsoCode>nl-BE</TargetLangIsoCode>
            <FileReference>Output/nl-BE/FILE-1.html</FileReference>
        </DeliveryFile>
        <DeliveryFile>
            <FileName>FILE-2.html</FileName>
            <FileSize>1768</FileSize>
            <SourceLangIsoCode>en-GB</SourceLangIsoCode>
            <TargetLangIsoCode>nl-BE</TargetLangIsoCode>
            <FileReference>Output/nl-BE/FILE-2.html</FileReference>
        </DeliveryFile>
    </DeliveryFiles>
EOF;

    /**
     * Test raw XML string with only 1 file.
     *
     * @var string
     */
    private $rawXmlSingleFile = <<<EOF
    <DeliveryFiles>
        <DeliveryFile>
            <FileName>FILE-1.html</FileName>
            <FileSize>1855</FileSize>
            <SourceLangIsoCode>en-GB</SourceLangIsoCode>
            <TargetLangIsoCode>nl-BE</TargetLangIsoCode>
            <FileReference>Output/nl-BE/FILE-1.html</FileReference>
        </DeliveryFile>
    </DeliveryFiles>
EOF;

    /**
     * Get a \SimpleXmlElement representing the files.
     *
     * @return \SimpleXmlElement
     */
    protected function getXml()
    {
        return new \SimpleXMLElement($this->rawXml);
    }

    /**
     * Testing the getId method.
     */
    public function testCountable()
    {
        // Empty set.
        $xml = new \SimpleXMLElement('<DeliveryFiles></DeliveryFiles>');
        $infoFiles = new InfoFiles($xml);
        $this->assertEquals(0, $infoFiles->count());
        $this->assertEquals(0, count($infoFiles));

        // 1 file in the set. We need to test this seperatly since single
        // elements are not stored in the same way witin the \SimpleXmlElement.
        $xml = new \SimpleXMLElement($this->rawXmlSingleFile);
        $infoFiles = new InfoFiles($xml);
        $this->assertEquals(1, $infoFiles->count());
        $this->assertEquals(1, count($infoFiles));

        // 2 files in the set.
        $infoFiles = new InfoFiles($this->getXml());
        $this->assertEquals(2, $infoFiles->count());
        $this->assertEquals(2, count($infoFiles));
    }

    /**
     * Test the valid method.
     */
    public function testValid()
    {
        // Empty set.
        $xml = new \SimpleXMLElement('<DeliveryFiles></DeliveryFiles>');
        $infoFiles = new InfoFiles($xml);
        $this->assertFalse($infoFiles->valid());

        // 2 files in the set.
        $infoFiles = new InfoFiles($this->getXml());
        $this->assertTrue($infoFiles->valid());
    }

    /**
     * Test the current method.
     */
    public function testCurrent()
    {
        // Empty set.
        $xml = new \SimpleXMLElement('<DeliveryFiles></DeliveryFiles>');
        $infoFiles = new InfoFiles($xml);
        $this->assertNull($infoFiles->current());

        // 2 files in the set.
        $infoFiles = new InfoFiles($this->getXml());
        $this->assertInstanceOf(
            'Amplexor\XConnect\Response\InfoFile',
            $infoFiles->current()
        );
        $this->assertEquals(
            'FILE-1.html',
            $infoFiles->current()->getName()
        );
    }

    /**
     * Test the traversable.
     */
    public function testTraversable()
    {
        $infoFiles = new InfoFiles($this->getXml());

        $files = ['FILE-1.html', 'FILE-2.html'];
        $i = 0;

        foreach ($infoFiles as $infoFile) {
            $this->assertEquals(
                $files[$infoFiles->key()],
                $infoFile->getName()
            );
            $i++;
        }
    }
}
