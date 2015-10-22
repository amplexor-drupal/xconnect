<?php

namespace Amplexor\XConnect\Request\Order\Test;

use Amplexor\XConnect\Request\Order;
use Amplexor\XConnect\Request\Order\FormatXml;

class FormatXmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Expected XML for the test based on the getOrderStub().
     *
     * @var string
     */
    private $expectedXmlFromOrderStub = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<tns:ClientWoRequest xmlns:tns="http://www.euroscript.com/escaepe/types" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.euroscript.com/escaepe/types clientOrderRequestTypes.xsd"><ClientId>CLIENT-ID</ClientId><OrderName>ORDER-NAME</OrderName><TemplateId>TEMPLATE-ID</TemplateId><RequestDate>2015-10-22T12:00:00</RequestDate><RequestedDueDate>2015-10-22</RequestedDueDate><IssuedBy>ISSUED-BY@DOMAIN.COM</IssuedBy><ConfidentialOrder>0</ConfidentialOrder><SourceLanguageIsoCode>EN</SourceLanguageIsoCode><TargetLanguages><IsoCode>NL</IsoCode><IsoCode>FR</IsoCode></TargetLanguages><Service>SERVICE</Service><ClientInstructions>TRANSLATION-INSTRUCTIONS</ClientInstructions><ClientReference>REFERENCE</ClientReference><ConfirmationRequested>1</ConfirmationRequested><QuotationRequested>0</QuotationRequested><InputFiles><InputFile><FileName>FILENAME1.html</FileName><FileReference>Input/FILENAME1.html</FileReference></InputFile><InputFile><FileName>FILENAME2.html</FileName><FileReference>Input/FILENAME2.html</FileReference></InputFile></InputFiles></tns:ClientWoRequest>
EOF;

    /**
     * Expected XML for the test based on the getOrderStubEmpty().
     *
     * @var string
     */
    private $expectedXmlFromOrderStubEmpty = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<tns:ClientWoRequest xmlns:tns="http://www.euroscript.com/escaepe/types" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.euroscript.com/escaepe/types clientOrderRequestTypes.xsd"><ClientId/><OrderName/><TemplateId/><RequestDate>2015-10-22T12:00:00</RequestDate><RequestedDueDate>2015-10-22</RequestedDueDate><IssuedBy/><ConfidentialOrder>1</ConfidentialOrder><SourceLanguageIsoCode>EN</SourceLanguageIsoCode><TargetLanguages/><Service/><ClientInstructions>None</ClientInstructions><ClientReference/><ConfirmationRequested>0</ConfirmationRequested><QuotationRequested>1</QuotationRequested><InputFiles/></tns:ClientWoRequest>
EOF;

    /**
     * Get an Order stub.
     *
     * @return Order.
     */
    protected function getOrderStub()
    {
        $order = $this->getMockBuilder('Amplexor\XConnect\Request\Order')
                      ->disableOriginalConstructor()
                      ->getMock();

        $order->method('getClientId')
              ->willReturn('CLIENT-ID');
        $order->method('getOrderName')
              ->willReturn('ORDER-NAME');
        $order->method('getTemplateId')
              ->willReturn('TEMPLATE-ID');
        $order->method('getRequestDate')
              ->willReturn(new \DateTime('2015-10-22 12:00:00'));
        $order->method('getDueDate')
              ->willReturn(new \DateTime('2015-10-22 12:00:00'));
        $order->method('getIssuedBy')
              ->willReturn('ISSUED-BY@DOMAIN.COM');
        $order->method('isConfidential')
              ->willReturn(false);
        $order->method('getSourceLanguage')
              ->willReturn('EN');
        $order->method('getTargetLanguages')
              ->willReturn(array('NL', 'FR'));
        $order->method('getService')
              ->willReturn('SERVICE');
        $order->method('getInstructions')
              ->willReturn(array('TRANSLATION-INSTRUCTIONS'));
        $order->method('getReference')
              ->willReturn('REFERENCE');
        $order->method('needsConfirmation')
              ->willReturn(true);
        $order->method('needsQuotation')
              ->willReturn(false);
        $order->method('getFiles')
              ->willReturn(array('FILENAME1.html', 'FILENAME2.html'));

        return $order;
    }

    /**
     * Get an Order stub with empty values.
     *
     * @return Order
     */
    protected function getOrderStubEmpty()
    {
        $order = $this->getMockBuilder('Amplexor\XConnect\Request\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $order->method('getClientId')
            ->willReturn('');
        $order->method('getOrderName')
            ->willReturn('');
        $order->method('getTemplateId')
            ->willReturn('');
        $order->method('getRequestDate')
            ->willReturn(new \DateTime('2015-10-22 12:00:00'));
        $order->method('getDueDate')
            ->willReturn(new \DateTime('2015-10-22 12:00:00'));
        $order->method('getIssuedBy')
            ->willReturn('');
        $order->method('isConfidential')
            ->willReturn(true);
        $order->method('getSourceLanguage')
            ->willReturn('EN');
        $order->method('getTargetLanguages')
            ->willReturn(array());
        $order->method('getService')
            ->willReturn('');
        $order->method('getInstructions')
            ->willReturn(array());
        $order->method('getReference')
            ->willReturn('');
        $order->method('needsConfirmation')
            ->willReturn(false);
        $order->method('needsQuotation')
            ->willReturn(true);
        $order->method('getFiles')
            ->willReturn(array());

        return $order;
    }

    /**
     * Test the format method with all data.
     */
    public function testFormatWithFullOrder()
    {
        $order = $this->getOrderStub();
        $format = new FormatXml();
        $xml = $format->format($order);

        // Work around for different XML behaviour based on the platform.
        $expected = new \SimpleXMLElement($this->expectedXmlFromOrderStub);

        $this->assertEquals(
            $expected->asXML(),
            $xml
        );
    }

    /**
     * Test the format method with empty values in the Stub.
     */
    public function testFormatWithEmptyOrder()
    {
        $order = $this->getOrderStubEmpty();
        $format = new FormatXml();
        $xml = $format->format($order);

        // Work around for different XML behaviour based on the platform.
        $expected = new \SimpleXMLElement($this->expectedXmlFromOrderStubEmpty);

        $this->assertEquals(
            $expected->asXML(),
            $xml
        );
    }
}
