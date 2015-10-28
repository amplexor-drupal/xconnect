<?php

namespace Amplexor\XConnect\Test;

use Amplexor\XConnect\Response;
use Amplexor\XConnect\Test\Response\InfoMocks;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the getInfo method.
     */
    public function testGetInfo()
    {
        $mockInfoFiles = InfoMocks::mockInfoFiles($this, []);
        $mockInfo = InfoMocks::mockInfo($this, $mockInfoFiles);
        $mockFile = InfoMocks::mockFileInterface($this, $mockInfo);

        $response = new Response($mockFile);
        $this->assertEquals($mockInfo, $response->getInfo());
    }

    /**
     * Test the getTranslations method.
     */
    public function testGetTranslations()
    {
        $items = [
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
        ];
        $mockInfoFiles = InfoMocks::mockInfoFiles($this, $items);
        $mockInfo = InfoMocks::mockInfo($this, $mockInfoFiles);
        $mockFile = InfoMocks::mockFileInterface($this, $mockInfo);

        $response = new Response($mockFile);

        $translations = $response->getTranslations();
        $this->assertInstanceOf(
            'Amplexor\XConnect\Response\Translations',
            $translations
        );
        $this->assertEquals(3, count($translations));
    }
}
