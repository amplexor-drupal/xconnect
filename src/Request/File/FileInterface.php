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

namespace Amplexor\XConnect\Request\File;

use Amplexor\XConnect\Request;

interface FileInterface
{
    /**
     * Create a File object based on the given request and save in file path.
     *
     * @param Request $request
     *   The request to create the file for.
     * @param string $directory
     *   The directory where to store the file.
     *
     * @return FileInterface
     */
    public static function create(Request $request, $directory);

    /**
     * Get the filePath of the created file.
     *
     * @return string
     *   The full path to the file representation of the request.
     */
    public function getPath();
}
