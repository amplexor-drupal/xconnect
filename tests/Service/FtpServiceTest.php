<?php

namespace Amplexor\XConnect\Service;

require_once __DIR__ . '/FtpMock.php';

class FtpServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup the tests.
     */
    protected function setUp()
    {
        FtpServiceTestSpy::reset();
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
            ->willReturn('/test/path/success.zip');
        $file->method('getFileName')
            ->willReturn('success.zip');
        $file->method('getDirectory')
            ->willReturn('/test/path');

        return $file;
    }

    /**
     * Create Request file mock that will fail.
     *
     * @return FileInterface
     */
    protected function getFileMockFail()
    {
        $file = $this->getMockBuilder('Amplexor\XConnect\Request\File\FileInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file->method('getPath')
            ->willReturn('/test/path/fail.zip');
        $file->method('getFileName')
            ->willReturn('fail.zip');
        $file->method('getDirectory')
            ->willReturn('/test/path');

        return $file;
    }

    /**
     * Test failed connection exception.
     *
     * @expectedException Amplexor\XConnect\Service\ServiceException
     * @expectedExceptionMessage Can't connect to host "connection.fail"
     */
    public function testFailedConnectionException()
    {
        $config = ['hostname' => 'connection.fail'];
        $service = new FtpService($config);

        // The connection is only created when a method requires it.
        $service->send($this->getFileMock());
    }

    /**
     * Test failed login exception.
     *
     * @expectedException Amplexor\XConnect\Service\ServiceException
     * @expectedExceptionMessage Can't login with user "login.fail"
     */
    public function testFailedLoginException()
    {
        $config = [
            'hostname' => 'connection.success',
            'username' => 'login.fail',
            'password' => 'password.fail',
        ];
        $service = new FtpService($config);

        // The connection is only created when a method requires it.
        $service->send($this->getFileMock());
    }

    /**
     * Test connection configuration.
     */
    public function testConnectionConfig()
    {
        $config = [
            'hostname' => 'connection.success',
            'port' => 1234,
            'username' => 'login.success',
            'password' => 'password.success',
        ];
        $service = new FtpService($config);

        // The connection is only created when a method requires it.
        $service->send($this->getFileMock());

        // Check if the connection params are passed correctly.
        $expectedConnection = ['connection.success', 1234];
        $this->assertEquals(
            $expectedConnection,
            FtpServiceTestSpy::getLog('ftp_connect')
        );

        // Check if the login was passed correctly.
        $expectedLogin = [true, 'login.success', 'password.success'];
        $this->assertEquals(
            $expectedLogin,
            FtpServiceTestSpy::getLog('ftp_login')
        );
    }

    /**
     * Test the send method.
     */
    public function testSend()
    {
        $service = new FtpService([]);
        $this->assertTrue(
            $service->send($this->getFileMock())
        );
    }

    /**
     * Test the exception when the upload fails.
     *
     * @expectedException Amplexor\XConnect\Service\ServiceException
     * @expectedExceptionMessage File "/test/path/fail.zip" could not be uploaded to "To_LSP/fail.zip".
     */
    public function testSendException()
    {
        $service = new FtpService([]);
        $service->send($this->getFileMockFail());
    }

    /**
     * Test the send method with a path from the configuration.
     */
    public function testSendConfiguration()
    {
        // Default configuration.
        $service = new FtpService([]);
        $service->send($this->getFileMock());

        $expected = [
            true,
            'To_LSP/success.zip',
            '/test/path/success.zip',
            FTP_BINARY,
        ];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_put'));

        // Custom directory.
        $service = new FtpService(['directory_send' => 'To_Test/FooBar']);
        $service->send($this->getFileMock());

        $expected = [
            true,
            'To_Test/FooBar/success.zip',
            '/test/path/success.zip',
            FTP_BINARY,
        ];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_put'));
    }

    /**
     * Test scanning the remote folder.
     */
    public function testScan()
    {
        // Test with empty directory.
        $service = new FtpService([]);
        $this->assertEquals([], $service->scan());

        // Test with directory with subdirectories.
        $service = new FtpService(['directory_receive' => 'withDirectories']);
        $this->assertEquals([], $service->scan());

        // Test with actual files.
        $service = new FtpService(['directory_receive' => 'withFiles']);
        $expected = ['response1.zip', 'response2.zip'];
        $this->assertEquals($expected, $service->scan());
    }

    /**
     * Test the scan config.
     */
    public function testScanConfiguration()
    {
        // Default config.
        $service = new FtpService([]);
        $service->scan();

        $expected = [true, 'From_LSP'];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_nlist'));

        // Custom path.
        $service = new FtpService(['directory_receive' => 'From_Test/FooBar']);
        $service->scan();

        $expected = [true, 'From_Test/FooBar'];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_nlist'));
    }

    /**
     * Test receiving the remote file.
     */
    public function testReceive()
    {
        $service = new FtpService([]);
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
        $service = new FtpService([]);
        $service->receive('fail.zip', '/local/path');
    }

    /**
     * Test the receive config.
     */
    public function testReceiveConfiguration()
    {
        // Default config.
        $service = new FtpService([]);
        $service->receive('success.zip', '/local/path');

        $expected = [
            true,
            '/local/path/success.zip',
            'From_LSP/success.zip',
            FTP_BINARY
        ];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_get'));

        // Custom receive path.
        $service = new FtpService(['directory_receive' => 'From_Test/FooBar']);
        $service->receive('success.zip', '/local/path/123');

        $expected = [
            true,
            '/local/path/123/success.zip',
            'From_Test/FooBar/success.zip',
            FTP_BINARY
        ];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_get'));
    }

    /**
     * Test indicating to the service that we processed a file.
     */
    public function testProcessed()
    {
        $service = new FtpService([]);
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
        $service = new FtpService([]);
        $service->processed('fail.zip');
    }

    /**
     * Test the processed configuration.
     */
    public function testProcessedConfiguration()
    {
        // Default configuration.
        $service = new FtpService([]);
        $service->processed('success.zip');

        $expected = [true, 'From_LSP/success.zip', 'From_LSP_processed/success.zip'];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_rename'));

        // Custom paths.
        $service = new FtpService([
            'directory_receive' => 'from',
            'directory_receive_processed' => 'from/processed',
        ]);
        $service->processed('success.zip');

        $expected = [true, 'from/success.zip', 'from/processed/success.zip'];
        $this->assertEquals($expected, FtpServiceTestSpy::getLog('ftp_rename'));
    }
}
