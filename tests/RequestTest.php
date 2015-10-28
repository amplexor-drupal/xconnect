<?php

namespace Amplexor\XConnect\Test;

use Amplexor\XConnect\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the order within the request.
     */
    public function testOrder()
    {
        $sourceLanguage = 'en';
        $config = array('clientId' => 'TEST-CLIENT-ID');

        $request = new Request($sourceLanguage, $config);

        $order = $request->getOrder();
        $this->assertInstanceOf('Amplexor\XConnect\Request\Order', $order);
        $this->assertEquals($sourceLanguage, $order->getSourceLanguage());
        $this->assertEquals($config['clientId'], $order->getClientId());
    }

    /**
     * Test the add target language.
     */
    public function testAddTargetLanguage()
    {
        $request = new Request('en', array());
        $this->assertEmpty($request->getOrder()->getTargetLanguages());

        $request->addTargetLanguage('nl');
        $request->addTargetLanguage('fr');
        $expected = array('nl', 'fr');
        $this->assertEquals(
            $expected,
            $request->getOrder()->getTargetLanguages()
        );
    }

    /**
     * Test the add instructions.
     */
    public function testAddInstruction()
    {
        $request = new Request('en', array());
        $this->assertEmpty($request->getOrder()->getInstructions());

        $request->addInstruction('Instruction1');
        $request->addInstruction('Instruction2');

        $expected = array('Instruction1', 'Instruction2');
        $this->assertEquals(
            $expected,
            $request->getOrder()->getInstructions()
        );
    }

    /**
     * Test the reference.
     */
    public function testSetReference()
    {
        $request = new Request('en', array());
        $this->assertEmpty($request->getOrder()->getReference());

        $reference = 'MY-TEST-REFERENCE';
        $request->setReference($reference);
        $this->assertEquals($reference, $request->getOrder()->getReference());
    }

    /**
     * Test adding files.
     */
    public function testAddFile()
    {
        $request = new Request('en', array());
        $this->assertEmpty($request->getFiles());
        $this->assertEmpty($request->getOrder()->getFiles());

        $request->addFile('path/to/filename1.xliff');
        $request->addFile('path/to/filename2.docx');

        $expected = array(
            'filename1.xliff' => 'path/to/filename1.xliff',
            'filename2.docx' => 'path/to/filename2.docx',
        );
        $this->assertEquals($expected, $request->getFiles());
        $this->assertEquals(
            array_keys($expected),
            $request->getOrder()->getFiles()
        );
    }

    /**
     * Test adding content.
     */
    public function testAddContent()
    {
        $request = new Request('en', array());
        $this->assertEmpty($request->getContent());

        $request->addContent('filename1.xml', '<testXml></testXml>');
        $request->addContent('filename2.html', '<html></html>');

        $expected = array(
            'filename1.xml' => '<testXml></testXml>',
            'filename2.html' => '<html></html>',
        );

        $this->assertEquals($expected, $request->getContent());
        $this->assertEquals(
            array_keys($expected),
            $request->getOrder()->getFiles()
        );
    }
}
