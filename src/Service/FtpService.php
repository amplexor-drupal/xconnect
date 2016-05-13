<?php
/**
 * This file is part of the Amplexor\XConnect library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/amplexor-drupal/xconnect/
 * @version 1.0.0
 * @package Amplexor.XConnect
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amplexor\XConnect\Service;

use Amplexor\XConnect\Request\File\FileInterface;

/**
 * FTP Service.
 */
class FtpService extends ServiceAbstract
{
    /**
     * The FTP transfer mode.
     *
     * We set this value = 2, thats the same as the FTP_BINARY constant.
     *
     * @var int
     */
    const TRANSFER_MODE = 2;

    /**
     * Service connection configuration.
     *
     * @var array
     */
    private $config = array(
        'hostname' => '',
        'port' => 21,
        'timeout' => 90,
        'username' => '',
        'password' => '',
    );

    /**
     * The FTP connection resource.
     *
     * @var resource
     */
    private $ftp;

    /**
     * Class constructor.
     *
     * Create a new service by passing the configuration:
     * - hostname : The hostname of the FTP service.
     * - port : The FRP port.
     * - username : The username to connect to the FTP service.
     * - password : The password to connect to the FTP service.
     *
     * Optional configuration:
     * - directory_send : The remote directory to store the request file in.
     * - directory_send_processed : The remote directory where the processed
     *   request files are stored.
     * - directory_receive : The remote directory where the translated files are
     *   stored to be picked up.
     * - directory_receive_processed : The repome directory where to put the
     *   translation files that are successfully processed by the local system.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->setConfig($config);
    }

    /**
     * @inheritDoc
     */
    public function send(FileInterface $file)
    {
        $connection = $this->getConnection();
        $from = $file->getPath();
        $to = $this->getDirectorySend() . '/' . $file->getFileName();
        $result = ftp_put($connection, $to, $from, self::TRANSFER_MODE);

        if (!$result) {
            throw new ServiceException(
                sprintf(
                    'File "%s" could not be uploaded to "%s".',
                    $from,
                    $to
                )
            );
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function scan()
    {
        $connection = $this->getConnection();
        $directory = $this->getDirectoryReceive();

        // Get the full file list.
        $files = ftp_nlist($connection, $directory);

        // We only want files, no directories.
        foreach ($files as $key => $file) {
            if (ftp_size($connection, $directory . '/' . $file) < 0) {
                unset($files[$key]);
                continue;
            }
        }

        return array_values($files);
    }

    /**
     * @inheritDoc
     */
    public function receive($fileName, $directory)
    {
        $connection = $this->getConnection();
        $to = $directory . '/' . $fileName;
        $from = $this->getDirectoryReceive() . '/' . $fileName;

        $result = ftp_get($connection, $to, $from, self::TRANSFER_MODE);

        if (!$result) {
            throw new ServiceException(
                sprintf(
                    'File "%s" could not be downloaded to "%s".',
                    $from,
                    $to
                )
            );
        }

        return $to;
    }

    /**
     * @inheritDoc
     */
    public function processed($fileName)
    {
        $connection = $this->getConnection();

        $from = $this->getDirectoryReceive() . '/' . $fileName;
        $to = $this->getDirectoryReceiveProcessed() . '/' . $fileName;

        $result = ftp_rename($connection, $from, $to);

        if (!$result) {
            throw new ServiceException(
                sprintf(
                    'File "%s" could not be moved to "%s".',
                    $from,
                    $to
                )
            );
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete($fileName)
    {
        $connection = $this->getConnection();

        $file = $this->getDirectoryReceive() . '/' . $fileName;

        $result = ftp_delete($connection, $file);

        if (!$result) {
            throw new ServiceException(
              sprintf(
                'File "%s" could not be deleter.',
                $from
              )
            );
        }

        return $result;
    }

    /**
     * Get the connection.
     *
     * @return resource
     *   A FTP connection resource.
     */
    protected function getConnection()
    {
        if (!$this->ftp) {
            $this->connect();
        }
        return $this->ftp;
    }

    /**
     * Connect to the FTP server.
     */
    protected function connect()
    {
        $connection = ftp_connect($this->getHostname(), $this->getPort(), $this->getTimeout());
        if (!$connection) {
            throw new ServiceException(
                sprintf('Can\'t connect to host "%s"', $this->getHostname())
            );
        }

        // Login to host.
        $result = ftp_login($connection, $this->getUsername(), $this->getPassword());
        if (!$result) {
            throw new ServiceException(
                sprintf('Can\'t login with user "%s"', $this->getUsername())
            );
        }

        // Set connection to passive mode.
        ftp_pasv($connection, true);

        $this->ftp = $connection;
    }


    /**
     * Write the config to the config array.
     *
     * @param array $config
     */
    protected function setConfig(array $config)
    {
        $keyNames = array_keys($this->config);
        foreach ($keyNames as $key) {
            if (array_key_exists($key, $config)) {
                $this->config[$key] = $config[$key];
            }
        }
    }

    /**
     * Get the hostname from the config.
     *
     * @return string
     */
    protected function getHostname()
    {
        return $this->config['hostname'];
    }

    /**
     * Get the portnumber from the config.
     *
     * @return int
     */
    protected function getPort()
    {
        return (int) $this->config['port'];
    }

    /**
     * Get the timeout from the config.
     *
     * @return int
     */
    protected function getTimeout()
    {
        return (int) $this->config['timeout'];
    }

    /**
     * Get the username.
     *
     * @return string
     */
    protected function getUsername()
    {
        return $this->config['username'];
    }

    /**
     * Get the password.
     *
     * @return string
     */
    protected function getPassword()
    {
        return $this->config['password'];
    }
}
