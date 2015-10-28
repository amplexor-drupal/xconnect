<?php

namespace Amplexor\XConnect\Test\Response;

/**
 * Class containing shared mocks definitions for the info objects.
 *
 * @package Amplexor\XConnect\Test\Response
 */
class InfoMocks
{
    /**
     * Create a new mock.
     *
     * @param \PHPUnit_Framework_TestCase $test
     *   The test for who to create the mock.
     * @param string $className
     *   The className for who to create the mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private static function createMock(\PHPUnit_Framework_TestCase $test, $className)
    {
        $mock = $test->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    /**
     * Create a FileInterface mock by passing the Info mock object.
     *
     * @param \PHPUnit_Framework_TestCase $test
     *   The test for who to create the mock.
     * @param \PHPUnit_Framework_MockObject_MockObject $infoMock
     *   An Info mock to include in the File mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public static function mockFileInterface(
        \PHPUnit_Framework_TestCase $test,
        \PHPUnit_Framework_MockObject_MockObject $infoMock
    ) {
        $file = static::createMock(
            $test,
            'Amplexor\XConnect\Response\File\FileInterface'
        );

        $file->method('getInfo')
            ->willReturn($infoMock);

        return $file;
    }

    /**
     * Create an Info mock.
     *
     * @param \PHPUnit_Framework_TestCase $test
     *   The test for who to create the mock.
     * @param \PHPUnit_Framework_MockObject_MockObject $infoFilesMock
     *   A Files mock to include in the Info mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public static function mockInfo(
        \PHPUnit_Framework_TestCase $test,
        \PHPUnit_Framework_MockObject_MockObject $infoFilesMock
    ) {
        $info = static::createMock($test, 'Amplexor\XConnect\Response\Info');

        $info->method('getFiles')
            ->willReturn($infoFilesMock);

        return $info;
    }

    /**
     * Create an empty InfoFiles mock.
     *
     * @param \PHPUnit_Framework_TestCase $test
     *   The test for who to create the mock.
     * @param \PHPUnit_Framework_MockObject_MockObject[] $items
     *   An array of InfoFile mocks to include in the InfoFiles mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public static function mockInfoFiles(
        \PHPUnit_Framework_TestCase $test,
        array $items
    ) {
        $infoFiles = static::createMock(
            $test,
            'Amplexor\XConnect\Response\InfoFiles'
        );

        static::addIteratorToMock($test, $infoFiles, $items);
        return $infoFiles;
    }

    /**
     * Create a InfoFile mock.
     *
     * @param \PHPUnit_Framework_TestCase $test
     *   The test for who to create the mock.
     * @param string $fileName
     *   The file name to use in the mock object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public static function mockInfoFile(
        \PHPUnit_Framework_TestCase $test,
        $fileName = 'InfoFileMock.html'
    ) {
        $infoFile = static::createMock(
            $test,
            'Amplexor\XConnect\Response\InfoFile'
        );

        $infoFile->method('getName')
            ->willReturn($fileName);

        return $infoFile;
    }

    /**
     * Add mocked iteration behaviour to a mock.
     *
     * @param \PHPUnit_Framework_TestCase $test
     *   The test this mock will be used in.
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     *   The mock to add the iterator to.
     * @param array $items
     *   The items to use in the mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private static function addIteratorToMock(
        \PHPUnit_Framework_TestCase $test,
        \PHPUnit_Framework_MockObject_MockObject $mock,
        array $items
    ) {
        $iteratorData = new \stdClass();
        $iteratorData->array = array_values($items);
        $iteratorData->position = 0;

        $mock->expects($test->any())
            ->method('rewind')
            ->will(
                $test->returnCallback(
                    function () use ($iteratorData) {
                        $iteratorData->position = 0;
                    }
                )
            );

        $mock->expects($test->any())
            ->method('current')
            ->will(
                $test->returnCallback(
                    function () use ($iteratorData) {
                        return $iteratorData->array[$iteratorData->position];
                    }
                )
            );

        $mock->expects($test->any())
            ->method('key')
            ->will(
                $test->returnCallback(
                    function () use ($iteratorData) {
                        return $iteratorData->position;
                    }
                )
            );

        $mock->expects($test->any())
            ->method('next')
            ->will(
                $test->returnCallback(
                    function () use ($iteratorData) {
                        $iteratorData->position++;
                    }
                )
            );

        $mock->expects($test->any())
            ->method('valid')
            ->will(
                $test->returnCallback(
                    function () use ($iteratorData) {
                        return isset($iteratorData->array[$iteratorData->position]);
                    }
                )
            );

        $mock->expects($test->any())
            ->method('count')
            ->will(
                $test->returnCallback(
                    function () use ($iteratorData) {
                        return sizeof($iteratorData->array);
                    }
                )
            );

        return $mock;
    }
}
