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
 * SFTP Service.
 */
class SFtpService extends FtpService
{
    /**
     * The transfer mode.
     *
     * Value is substitute for the NET_SFTP_LOCAL_FILE constant.
     *
     * @var int
     */
    const TRANSFER_MODE = 1;

    /**
     * The SFTP connection.
     *
     * @var \Net_SFTP
     */
    private $connection;


    /**
     * @inheritDoc
     */
    public function __construct(array $config)
    {
        if (!isset($config['port'])) {
            $config['port'] = 22;
        }

        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function send(FileInterface $file)
    {
        $connection = $this->getConnection();
        $from = $file->getPath();
        $to = $this->getDirectorySend() . '/' . $file->getFileName();

        $result = $connection->put($to, $from, self::TRANSFER_MODE);
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
        $files = array();

        $rawFiles = $connection->rawlist($this->getDirectoryReceive());
        if (empty($rawFiles)) {
            return $files;
        }

        // We only want files, no directories.
        foreach ($rawFiles as $rawFile) {
            if ($rawFile['type'] !== 1) {
                continue;
            }

            $files[] = $rawFile['filename'];
        }

        return $files;
    }

    /**
     * @inheritDoc
     */
    public function receive($fileName, $localDirectory)
    {
        $connection = $this->getConnection();

        $to = $localDirectory . '/' . $fileName;
        $from = $this->getDirectoryReceive() . '/' . $fileName;
        $result = $connection->get($from, $to);

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
     * {@inheritdoc}
     */
    public function processed($fileName)
    {
        $connection = $this->getConnection();
        $from = $this->getDirectoryReceive() . '/' . $fileName;
        $to = $this->getDirectoryReceiveProcessed() . '/' . $fileName;

        $result = $connection->rename($from, $to);
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
     * Inject the connection for testing purpose.
     *
     * @param \Net_SFTP $connection
     *   The connection object.
     */
    public function setConnection(\Net_SFTP $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the connection.
     *
     * @return \Net_SFTP
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Connect to the SFTP service.
     */
    protected function connect()
    {
        // Connect to host.

        $this->connection = new \Net_SFTP($this->getHostname(), $this->getPort());
        if (!$this->connection) {
            throw new \Exception(
                sprintf('Can\'t connect to host "%s"', $this->getHostname())
            );
        }

        // Login to host.
        $result = $this->connection->login(
            $this->getUsername(),
            $this->getPassword()
        );
        if (!$result) {
            throw new \Exception(
                sprintf('Can\t connect with user "%s"', $this->getUsername())
            );
        }
    }
}
