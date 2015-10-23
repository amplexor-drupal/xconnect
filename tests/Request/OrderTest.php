<?php

namespace Amplexor\XConnect\Request\Order\Test;

use Amplexor\XConnect\Request\Order;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Source language.
     */
    public function testSourceLanguage()
    {
        $language1 = 'en';
        $order1 = new Order($language1);

        $this->assertEquals($language1, $order1->getSourceLanguage());

        $language2 = 'nl';
        $order2 = new Order($language2);
        $this->assertEquals($language2, $order2->getSourceLanguage());
    }

    /**
     * Order name.
     */
    public function testOrderName()
    {
        // Order name should be in the
        // <default-prefix>_HHiiss<3-digit-microseconds> format.
        $regex =
            '/^%s_'
            . date('Ymd')
            . '(([0-1][0-9])|([2][0-3]))[0-5][0-9][0-9]{5}$/';

        // Default prefixed with "translation_order".
        $order1 = new Order('en');
        $expectedNameRegExp1 = sprintf($regex, 'translation_order');
        $this->assertRegExp($expectedNameRegExp1, $order1->getOrderName());

        // Prefix from config array.
        $prefix = 'my_order_test';
        $order2 = new Order('en', array('orderNamePrefix' => $prefix));
        $expectedNameRegExp2 = sprintf($regex, $prefix);
        $this->assertRegExp($expectedNameRegExp2, $order2->getOrderName());
    }

    /**
     * Client ID.
     */
    public function testClientId()
    {
        // Default empty.
        $order1 = new Order('en');
        $this->assertEquals('', $order1->getClientId());

        // Client id from config array.
        $clientId = '12345-abcdef-67890';
        $order2 = new Order('en', array('clientId' => $clientId));
        $this->assertEquals($clientId, $order2->getClientId());
    }

    /**
     * Template ID.
     */
    public function testTemplateId()
    {
        // Default empty.
        $order1 = new Order('en');
        $this->assertEquals('', $order1->getTemplateId());

        // Template id from config array.
        $templateId2 = 'test-template-id';
        $order2 = new Order('en', array('templateId' => $templateId2));
        $this->assertEquals($templateId2, $order2->getTemplateId());
    }

    /**
     * Request date.
     */
    public function testRequestDate()
    {
        $expectedDate1 = date('Ymd');

        $order1 = new Order('en');
        $this->assertInstanceOf('\DateTime', $order1->getRequestDate());
        $this->assertEquals($expectedDate1, $order1->getRequestDate()->format('Ymd'));
    }

    /**
     * Due date.
     */
    public function testDueDate()
    {
        // Default due date = 0 interval.
        $expectedDate1 = new \DateTime();
        $order1 = new Order('en');
        $this->assertEquals(
            $expectedDate1,
            $order1->getDueDate(),
            'Request date should be now().',
            // Allow 5 seconds interval because of computing cycles.
            5
        );

        // Due date set using config.
        $interval2 = new \DateInterval('P6D');
        $expectedDate2 = new \DateTime();
        $expectedDate2->add($interval2);
        $order2 = new Order('en', array('dueDate' => 6));
        $this->assertEquals(
            $expectedDate2,
            $order2->getDueDate(),
            'Request date should be now() + 6 days.',
            // Allow 5 seconds interval because of computing cycles.
            5
        );
    }

    /**
     * Issued By.
     */
    public function testIssuedBy()
    {
        // Default value.
        $order1 = new Order('en');
        $this->assertEquals('', $order1->getIssuedBy());

        // Issued by from config array.
        $issuedBy2 = 'test@my-domain.com';
        $order2 = new Order('en', array('issuedBy' => $issuedBy2));
        $this->assertEquals($issuedBy2, $order2->getIssuedBy());
    }

    /**
     * Is confidential order.
     */
    public function testIsConfidential()
    {
        // Default value.
        $order1 = new Order('en');
        $this->assertFalse($order1->isConfidential());

        // Is confidential from config array.
        $order2 = new Order('en', array('isConfidential' => true));
        $this->assertTrue($order2->isConfidential());
    }

    /**
     * Target languages.
     */
    public function testTargetLanguages()
    {
        // Default none.
        $order = new Order('en');
        $this->assertEquals(array(), $order->getTargetLanguages());

        // Add target languages.
        $expectedLanguages = array('nl', 'fr');
        $order->addTargetLanguage('nl');
        $order->addTargetLanguage('fr');
        $this->assertEquals($expectedLanguages, $order->getTargetLanguages());

        // Add already added language.
        $order->addTargetLanguage('nl');
        $this->assertEquals($expectedLanguages, $order->getTargetLanguages());
    }

    /**
     * Service.
     */
    public function testService()
    {
        // Default value.
        $order1 = new Order('en');
        $this->assertEquals('', $order1->getService());

        // Service from config array.
        $service = 'test-service';
        $order2 = new Order('en', array('service' => $service));
        $this->assertEquals($service, $order2->getService());
    }

    /**
     * Test the instructions.
     */
    public function testInstructions()
    {
        // Default no instructions.
        $order = new Order('en');
        $this->assertEquals(array(), $order->getInstructions());

        // Add some instructions.
        $instructions = array(
            'Instruction1 : Test of the instructions.',
            'Instruction2 : Extra test for the instructions.'
        );
        $order->addInstruction($instructions[0]);
        $order->addInstruction($instructions[1]);
        $this->assertEquals($instructions, $order->getInstructions());
    }

    /**
     * Test the reference.
     */
    public function testReference()
    {
        // Default no value.
        $order = new Order('en');
        $this->assertEquals('', $order->getReference());

        // Add a reference.
        $reference = 'test-reference';
        $order->setReference($reference);
        $this->assertEquals($reference, $order->getReference());
    }

    /**
     * Test needs confirmation.
     */
    public function testNeedsConfirmation()
    {
        // Default value = true.
        $order1 = new Order('en');
        $this->assertTrue($order1->needsConfirmation());

        // Is confidential from config array.
        $order2 = new Order('en', array('needsConfirmation' => false));
        $this->assertFalse($order2->needsConfirmation());
    }

    /**
     * Test needs quotation.
     */
    public function testNeedsQuotation()
    {
        // Default value = true.
        $order1 = new Order('en');
        $this->assertFalse($order1->needsQuotation());

        // Is confidential from config array.
        $order2 = new Order('en', array('needsQuotation' => true));
        $this->assertTrue($order2->needsQuotation());
    }

    /**
     * Test the files.
     */
    public function testFiles()
    {
        // Default no files.
        $order = new Order('en');
        $this->assertEquals(array(), $order->getFiles());

        // Add some files.
        $files = array(
            'test-file-1.html',
            'test-file-2.html'
        );
        $order->addFile($files[0]);
        $order->addFile($files[1]);
        $this->assertEquals($files, $order->getFiles());
    }
}
