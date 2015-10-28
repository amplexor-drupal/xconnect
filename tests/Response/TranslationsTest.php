<?php

namespace Amplexor\XConnect\Test\Response;

use Amplexor\XConnect\Response\Translations;

class TranslationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a InfoFile mock.
     *
     * @param string $fileName
     *   The file name to use in the mock object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInfoFileMock($fileName = 'translation.html')
    {
        $infoFile = $this
            ->getMockBuilder('Amplexor\XConnect\Response\InfoFile')
            ->disableOriginalConstructor()
            ->getMock();

        $infoFile->method('getName')
            ->willReturn($fileName);

        return $infoFile;
    }

    /**
     * Create a InfoFiles mock.
     *
     * @param int $amount
     *   The number of files in the mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInfoFilesIteratorMock($amount = 0)
    {
        $infoFiles = $this
            ->getMockBuilder('Amplexor\XConnect\Response\InfoFiles')
            ->disableOriginalConstructor()
            ->getMock();

        // Add the requested number of mock files to collection.
        // PhpUnitMock has in internal counter (at) for whatever method call on
        // the object. We need to set the expected counter when creating the
        /// mock.
        $j = 0;
        for ($i = 0; $i < $amount; $i++) {
            $infoFiles->expects($this->at($j))
                ->method('valid')
                ->willReturn(true);
            $j++;
            $infoFiles->expects($this->at($j))
                ->method('current')
                ->willReturn(
                    $this->getInfoFileMock('file-' . $i . '.html')
                );
            $j++;
            // One extra since we are calling the next() method.
            $j++;
        }

        $infoFiles->expects($this->at($j))
            ->method('valid')
            ->willReturn(false);

        return $infoFiles;
    }

    /**
     * Create a FileInterface mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFileMock()
    {
        $file = $this
            ->getMockBuilder('Amplexor\XConnect\Response\File\FileInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $file;
    }


    /**
     * Test countable.
     */
    public function testCountable()
    {
        // No translations.
        $translations = new Translations(
            $this->getFileMock(),
            $this->getInfoFilesIteratorMock(0)
        );
        $this->assertEquals(0, $translations->count());

        // 4 translations.
        $translations = new Translations(
            $this->getFileMock(),
            $this->getInfoFilesIteratorMock(4)
        );
        $this->assertEquals(4, count($translations));
    }

    /**
     * Test traversable.
     */
    public function testTraversable()
    {
        $translations = new Translations(
            $this->getFileMock(),
            $this->getInfoFilesIteratorMock(5)
        );

        $expected = [
            'file-0.html',
            'file-1.html',
            'file-2.html',
            'file-3.html',
            'file-4.html',
        ];

        foreach ($translations as $i => $translation) {
            $this->assertInstanceOf(
                'Amplexor\XConnect\Response\Translation',
                $translation
            );
            $this->assertEquals(
                $expected[$i],
                $translation->getInfo()->getName()
            );
        }
    }

    /**
     * Test current()
     */
    public function testCurrent()
    {
        $translations = new Translations(
            $this->getFileMock(),
            $this->getInfoFilesIteratorMock(0)
        );

        $this->assertNull($translations->current());
    }
}
