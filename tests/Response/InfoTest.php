<?php

namespace Amplexor\XConnect\Test\Response;

use Amplexor\XConnect\Response\Info;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test raw XML string.
     *
     * @var string
     */
    private $rawXml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<ns2:ClientWoDelivery xmlns:ns2="http://www.euroscript.com/escaepe/types">
    <DeliveryId>DELIVERY-TEST</DeliveryId>
    <DeliveryDate>2015-10-13T13:10:00.000Z</DeliveryDate>
    <DeliveryStatus>FullDelivery</DeliveryStatus>
    <WOClientReference>REFERENCE-TEST</WOClientReference>
    <IssuedBy>ISSUED-BY-TEST</IssuedBy>
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
</ns2:ClientWoDelivery>
EOF;


    /**
     * Testing the getId method.
     */
    public function testGetId()
    {
        $info = new Info($this->rawXml);
        $expected = 'DELIVERY-TEST';

        $this->assertEquals($expected, $info->getId());
    }

    /**
     * Test getting the dateTime object.
     */
    public function testGetDate()
    {
        $info = new Info($this->rawXml);
        $expected = new \DateTime('2015-10-13T13:10:00.000Z');

        $this->assertEquals($expected, $info->getDate());
    }

    /**
     * Get the status info.
     */
    public function testGetStatus()
    {
        $info = new Info($this->rawXml);
        $expected = 'FullDelivery';

        $this->assertEquals($expected, $info->getStatus());
    }

    /**
     * Get the client reference.
     */
    public function testGetReference()
    {
        $info = new Info($this->rawXml);
        $expected = 'REFERENCE-TEST';

        $this->assertEquals($expected, $info->getReference());
    }

    /**
     * Test the get issued by.
     */
    public function testGetIssuedBy()
    {
        $info = new Info($this->rawXml);
        $expected = 'ISSUED-BY-TEST';

        $this->assertEquals($expected, $info->getIssuedBy());
    }

    /**
     * Test getting the information about the translations.
     */
    public function testGetFiles()
    {
        $info = new Info($this->rawXml);
        $files = $info->getFiles();

        $this->assertInstanceOf(
            'Amplexor\XConnect\Response\InfoFiles',
            $files
        );
        $this->assertEquals(2, count($files));
    }
}
