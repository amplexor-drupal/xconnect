<?php

namespace Amplexor\XConnect\Test\Service;

use Amplexor\XConnect\Service\SFtpService;

class SFtpServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get a mock of the \Net_SFTP object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getNetSFTPMock()
    {
        $sftp = $this->getMockBuilder('\Net_SFTP')
            ->disableOriginalConstructor()
            ->getMock();

        $sftp->method('login')
            ->willReturn(true);

        return $sftp;
    }

    /**
     * Create Request file mock.
     *
     * @return FileInterface
     */
    protected function getFileMock()
    {
        $file = $this->getMockBuilder('Amplexor\XConnect\Request\File\FileInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file->method('getPath')
            ->willReturn('/test/path/file.zip');
        $file->method('getFileName')
            ->willReturn('file.zip');
        $file->method('getDirectory')
            ->willReturn('/test/path');

        return $file;
    }

    /**
     * Test if loggin is called only once.
     */
    public function testLoginCalledOnce()
    {
        $username = 'username.success';
        $password = 'password.success';

        $connection = $this->getMockBuilder('\Net_SFTP')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('login')
            ->willReturn(true)
            ->with(
                $this->matches($username),
                $this->matches($password)
            );
        $connection->expects($this->exactly(2))
            ->method('put')
            ->willReturn(true);

        $service = new SFtpService([
            'username' => $username,
            'password' => $password
        ]);
        $service->setConnection($connection);

        // Call send twice, it should trigger login only once.
        $service->send($this->getFileMock());
        $service->send($this->getFileMock());
    }

    /**
     * Test failed loggin exception.
     *
     * @expectedException Amplexor\XConnect\Service\ServiceException
     * @expectedExceptionMessage Can't login with user "login.fail".
     */
    public function testFailedLoginException()
    {
        $connection = $this->getMockBuilder('\Net_SFTP')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->method('login')
            ->willReturn(false);

        $service = new SFtpService(['username' => 'login.fail']);
        $service->setConnection($connection);
        $service->send($this->getFileMock());
    }

    /**
     * Test the send method.
     */
    public function testSend()
    {
        $connection = $this->getNetSFTPMock();
        $connection->method('put')
            ->willReturn(true);

        $service = new SFtpService([]);
        $service->setConnection($connection);

        $this->assertTrue(
            $service->send($this->getFileMock())
        );
    }

    /**
     * Test the send exception.
     *
     * @expectedException Amplexor\XConnect\Service\ServiceException
     * @expectedExceptionMessage File "/test/path/file.zip" could not be uploaded to "To_LSP/file.zip".
     */
    public function testSendException()
    {
        $connection = $this->getNetSFTPMock();
        $connection->method('put')
            ->willReturn(false);

        $service = new SFtpService([]);
        $service->setConnection($connection);

        $service->send($this->getFileMock());
    }

    /**
     * Test the send configuration.
     */
    public function testSendConfiguration()
    {
        // Default config.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('put')
            ->willReturn(true)
            ->with(
                $this->matches('To_LSP/file.zip'),
                $this->matches('/test/path/file.zip')
            );

        $service = new SFtpService([]);
        $service->setConnection($connection);
        $service->send($this->getFileMock());

        // Send directory passed in configuration.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('put')
            ->willReturn(true)
            ->with(
                $this->matches('To_Test/FooBar/file.zip'),
                $this->matches('/test/path/file.zip')
            );

        $service = new SFtpService(['directory_send' => 'To_Test/FooBar']);
        $service->setConnection($connection);
        $service->send($this->getFileMock());
    }

    /**
     * Test the scan functionality.
     */
    public function testScan()
    {
        // Test with empty directory.
        $connection = $this->getNetSFTPMock();
        $connection->method('rawlist')
            ->willReturn([
                ['type' => 0, 'filename' => '.'],
                ['type' => 0, 'filename' => '..'],
            ]);

        $service = new SFtpService([]);
        $service->setConnection($connection);
        $expected = [];
        $this->assertEquals($expected, $service->scan());

        // Test with files in the directory.
        $connection = $this->getNetSFTPMock();
        $connection->method('rawlist')
            ->willReturn([
                ['type' => 0, 'filename' => '.'],
                ['type' => 0, 'filename' => '..'],
                ['type' => 1, 'filename' => 'result1.zip'],
                ['type' => 1, 'filename' => 'result2.zip'],
            ]);

        $service = new SFtpService([]);
        $service->setConnection($connection);
        $expected = ['result1.zip', 'result2.zip'];
        $this->assertEquals($expected, $service->scan());
    }

    /**
     * Test the scan configuration.
     */
    public function testScanConfiguration()
    {
        // Default configuration.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('rawlist')
            ->willReturn([])
            ->with(
                $this->matches('From_LSP')
            );

        $service = new SFtpService([]);
        $service->setConnection($connection);
        $service->scan();

        // Custom directory.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('rawlist')
            ->willReturn([])
            ->with(
                $this->matches('From_Test/FooBar')
            );

        $service = new SFtpService(['directory_receive' => 'From_Test/FooBar']);
        $service->setConnection($connection);
        $service->scan();
    }

    /**
     * Test the receive.
     */
    public function testReceive()
    {
        $connection = $this->getNetSFTPMock();
        $connection->method('get')
            ->willReturn(true);

        $service = new SFtpService([]);
        $service->setConnection($connection);

        $this->assertEquals(
            '/local/path/test.zip',
            $service->receive('test.zip', '/local/path')
        );
    }

    /**
     * Test receive exception.
     *
     * @expectedException Amplexor\XConnect\Service\ServiceException
     * @expectedExceptionMessage File "From_LSP/fail.zip" could not be downloaded to "/local/path/fail.zip".
     */
    public function testReceiveException()
    {
        $connection = $this->getNetSFTPMock();
        $connection->method('get')
            ->willReturn(false);

        $service = new SFtpService([]);
        $service->setConnection($connection);

        $service->receive('fail.zip', '/local/path');
    }

    /**
     * Test the receive configuration.
     */
    public function testReceiveConfiguration()
    {
        // Test with default configuration.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('get')
            ->willReturn(true)
            ->with(
                $this->matches('From_LSP/test.zip'),
                $this->matches('/local/path/test.zip')
            );

        $service = new SFtpService([]);
        $service->setConnection($connection);

        $service->receive('test.zip', '/local/path');

        // test with custom path.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('get')
            ->willReturn(true)
            ->with(
                $this->matches('From_Test/FooBar/test.zip'),
                $this->matches('/local/path/FooBar/test.zip')
            );

        $service = new SFtpService(['directory_receive' => 'From_Test/FooBar']);
        $service->setConnection($connection);

        $service->receive('test.zip', '/local/path/FooBar');
    }

    /**
     * Test indicating to the service that we processed a file.
     */
    public function testProcessed()
    {
        $connection = $this->getNetSFTPMock();
        $connection->method('rename')
            ->willReturn(true);

        $service = new SFtpService([]);
        $service->setConnection($connection);
        $this->assertTrue($service->processed('success.zip'));
    }

    /**
     * Test the exception when the processed() method fails.
     *
     * @expectedException Amplexor\XConnect\Service\ServiceException
     * @expectedExceptionMessage File "From_LSP/fail.zip" could not be moved to "From_LSP_processed/fail.zip".
     */
    public function testProcessedException()
    {
        $connection = $this->getNetSFTPMock();
        $connection->method('rename')
            ->willReturn(false);

        $service = new SFtpService([]);
        $service->setConnection($connection);
        $service->processed('fail.zip');
    }

    /**
     * Test the processed configuration.
     */
    public function testProcessedConfiguration()
    {
        // Default configuration.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('rename')
            ->willReturn(true)
            ->with(
                $this->matches('From_LSP/success.zip'),
                $this->matches('From_LSP_processed/success.zip')
            );

        $service = new SFtpService([]);
        $service->setConnection($connection);
        $service->processed('success.zip');

        // Custom paths in configuration.
        $connection = $this->getNetSFTPMock();
        $connection->expects($this->once())
            ->method('rename')
            ->willReturn(true)
            ->with(
                $this->matches('from/success.zip'),
                $this->matches('from/processed/success.zip')
            );


        $service = new SFtpService([
            'directory_receive' => 'from',
            'directory_receive_processed' => 'from/processed',
        ]);
        $service->setConnection($connection);
        $service->processed('success.zip');
    }
}
