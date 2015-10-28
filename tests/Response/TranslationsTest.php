<?php

namespace Amplexor\XConnect\Test\Response;

use Amplexor\XConnect\Response\Translations;
use Amplexor\XConnect\Test\Response\InfoMocks;

class TranslationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test countable with no files.
     */
    public function testCountableWithoutFiles()
    {
        $mockInfoFiles = InfoMocks::mockInfoFiles($this, []);
        $mockInfo = InfoMocks::mockInfo($this, $mockInfoFiles);
        $mockFile = InfoMocks::mockFileInterface($this, $mockInfo);

        // No translations.
        $translations = new Translations($mockFile);
        $this->assertEquals(0, $translations->count());
    }

    /**
     * Test countable with files.
     */
    public function testCountableWithFiles()
    {
        $items = [
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
        ];
        $mockInfoFiles = InfoMocks::mockInfoFiles($this, $items);
        $mockInfo = InfoMocks::mockInfo($this, $mockInfoFiles);
        $mockFile = InfoMocks::mockFileInterface($this, $mockInfo);

        // No translations.
        $translations = new Translations($mockFile);
        $this->assertEquals(4, $translations->count());
    }


    /**
     * Test traversable.
     */
    public function testTraversable()
    {
        $files = [
            'file-1.html' => InfoMocks::mockInfoFile($this, 'file-1.html'),
            'file-2.html' => InfoMocks::mockInfoFile($this, 'file-2.html'),
            'file-3.html' => InfoMocks::mockInfoFile($this, 'file-3.html'),
            'file-4.html' => InfoMocks::mockInfoFile($this, 'file-4.html'),
            'file-5.html' => InfoMocks::mockInfoFile($this, 'file-5.html'),
        ];
        $mockInfoFiles = InfoMocks::mockInfoFiles($this, $files);
        $mockInfo = InfoMocks::mockInfo($this, $mockInfoFiles);
        $mockFile = InfoMocks::mockFileInterface($this, $mockInfo);

        $translations = new Translations($mockFile);
        $expected = array_keys($files);
        $result = [];
        foreach ($translations as $i => $translation) {
            $this->assertInstanceOf(
                'Amplexor\XConnect\Response\Translation',
                $translation
            );
            $result[] = $translation->getInfo()->getName();
        }

        $this->assertEquals($expected, $result);
    }
}
