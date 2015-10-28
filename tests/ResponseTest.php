<?php

namespace Amplexor\XConnect\Test;

use Amplexor\XConnect\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a FileInterface mock.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $infoMock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFileMock($infoMock)
    {
        $file = $this
            ->getMockBuilder('Amplexor\XConnect\Response\File\FileInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file->method('getInfo')
            ->willReturn($infoMock);

        return $file;
    }

    /**
     * Create an Info mock.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $infoFilesMock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInfoMock(\PHPUnit_Framework_MockObject_MockObject $infoFilesMock)
    {
        $info = $this
            ->getMockBuilder('Amplexor\XConnect\Response\Info')
            ->disableOriginalConstructor()
            ->getMock();

        $info->method('getFiles')
            ->willReturn($infoFilesMock);

        return $info;
    }

    /**
     * Create an empty InfoFiles mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInfoFilesMock()
    {
        $infoFiles = $this
            ->getMockBuilder('Amplexor\XConnect\Response\InfoFiles')
            ->disableOriginalConstructor()
            ->getMock();

        return $infoFiles;
    }

    /**
     * Create a InfoFiles mock that mimics the iterator.
     *
     * @param int $amount
     *   The number of files in the mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInfoFilesIteratorMock($amount = 0)
    {
        $infoFiles = $this->getInfoFilesMock();

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
     * Create a InfoFile mock.
     *
     * @param string $fileName
     *   The file name to use in the mock object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInfoFileMock($fileName)
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
     * Test the getInfo method.
     */
    public function testGetInfo()
    {
        $infoMock = $this->getInfoMock($this->getInfoFilesMock());
        $response = new Response(
            $this->getFileMock($infoMock)
        );

        $this->assertEquals($infoMock, $response->getInfo());
    }

    /**
     * Test the getTranslations method.
     */
    public function testGetTranslations()
    {
        $response = new Response($this->getFileMock(
            $this->getInfoMock(
                $this->getInfoFilesIteratorMock(3)
            )
        ));

        $translations = $response->getTranslations();
        $this->assertInstanceOf(
            'Amplexor\XConnect\Response\Translations',
            $translations
        );
        $this->assertEquals(3, count($translations));
    }
}
