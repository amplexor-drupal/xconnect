<?php

namespace Amplexor\XConnect\Test\Response;

use Amplexor\XConnect\Response\Translations;
use Amplexor\XConnect\Test\Response\InfoMocks;

class TranslationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test countable.
     */
    public function testCountable()
    {
        $mockInfoFiles = InfoMocks::mockInfoFiles($this, []);
        $mockInfo = InfoMocks::mockInfo($this, $mockInfoFiles);
        $mockFile = InfoMocks::mockFileInterface($this, $mockInfo);

        // No translations.
        $translations = new Translations($mockFile, $mockInfoFiles);
        $this->assertEquals(0, $translations->count());

        // 4 translations.
        $items = [
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
            InfoMocks::mockInfoFile($this),
        ];
        $mockInfoFiles = InfoMocks::mockInfoFiles($this, $items);
        $translations = new Translations($mockFile, $mockInfoFiles);
        $this->assertEquals(4, count($translations));
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

        $translations = new Translations($mockFile, $mockInfoFiles);
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

    /**
     * Test current()
     */
    public function testCurrent()
    {
        // No items.
        $mockInfoFiles1 = InfoMocks::mockInfoFiles($this, []);
        $mockInfo1 = InfoMocks::mockInfo($this, $mockInfoFiles1);
        $mockFile1 = InfoMocks::mockFileInterface($this, $mockInfo1);

        $translations1 = new Translations($mockFile1, $mockInfoFiles1);
        $this->assertNull($translations1->current());
    }
}
