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

/**
 * X-Connect service class to send a request as a file.
 */
abstract class ServiceAbstract implements ServiceInterface
{
    /**
     * Remote directory configuration.
     *
     * @var array
     */
    private $directoryConfig = array(
        'send' => 'To_LSP',
        'send_processed' => 'To_LSP_processed',
        'receive' => 'From_LSP',
        'receive_processed' => 'From_LSP_processed',
    );

    /**
     * Constructor.
     *
     * This will extract the remote directory configuration from the config
     * array:
     * - directory_send : The remote directory to store the request file in.
     * - directory_send_processed : The remote directory where the processed
     *   request files are stored.
     * - directory_receive : The remote directory where the translated files are
     *   stored to be picked up.
     * - directory_receive_processed : The repome directory where to put the
     *   translation files that are successfully processed by the local system.
     *
     * @param array $config
     *   The remote directory config.
     */
    public function __construct(array $config = array())
    {
        $this->setDirectoryConfig($config);
    }

    /**
     * Set the config based on the given config array.
     *
     * @param array $config
     *   The configuration array.
     */
    protected function setDirectoryConfig(array $config)
    {
        $configKeys = array_keys($this->directoryConfig);

        foreach ($configKeys as $key) {
            $configKey = 'directory_' . $key;
            if (array_key_exists($configKey, $config)) {
                $this->directoryConfig[$key] = $config[$configKey];
            }
        }
    }

    /**
     * Get the remote directory name to where to store the request files.
     *
     * @return string
     *   The remote directory to store the request file in.
     */
    protected function getDirectorySend()
    {
        return $this->directoryConfig['send'];
    }

    /**
     * Get the remote directory name where the processed requests are stored.
     *
     * @return string
     *   The remote directory where the processed request files are stored.
     */
    protected function getDirectorySendProcessed()
    {
        return $this->directoryConfig['send_processed'];
    }

    /**
     * Get the remote directory where the translated response files are located.
     *
     * @return string
     */
    protected function getDirectoryReceive()
    {
        return $this->directoryConfig['receive'];
    }

    /**
     * Get the remote directory where a processed response should be moved to.
     *
     * @return string
     */
    protected function getDirectoryReceiveProcessed()
    {
        return $this->directoryConfig['receive_processed'];
    }
}
