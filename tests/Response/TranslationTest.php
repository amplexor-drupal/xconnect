<?php

namespace Amplexor\XConnect\Response\Translation\Test;

use Amplexor\XConnect\Response\Translation;

class TranslationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a InfoFile mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInfoFileMock()
    {
        $info = $this->getMockBuilder('Amplexor\XConnect\Response\InfoFile')
            ->disableOriginalConstructor()
            ->getMock();

        return $info;
    }

    /**
     * Create a FileInterface mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFileMock()
    {
        $file = $this->getMockBuilder('Amplexor\XConnect\Response\File\FileInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $file;
    }

    /**
     * Test get file info.
     */
    public function testGetInfo()
    {
        $info = $this->getInfoFileMock();
        $translation = new Translation($this->getFileMock(), $info);

        $this->assertEquals($info, $translation->getInfo());
    }

    /**
     * Test the get content.
     */
    public function testGetContent()
    {
        $expected = 'Output/nl-BE/translation1.html';

        $file = $this->getFileMock();
        $file->expects($this->once())
            ->method('getContent')
            ->willReturn($expected)
            ->with(
                $this->matches('Output/nl-BE/translation1.html')
            );

        $info = $this->getInfoFileMock();
        $info->expects($this->once())
            ->method('getPath')
            ->willReturn('Output/nl-BE/translation1.html');

        $translation = new Translation($file, $info);
        $this->assertEquals($expected, $translation->getContent());
    }
}
