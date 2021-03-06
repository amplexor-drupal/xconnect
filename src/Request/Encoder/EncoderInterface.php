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

namespace Amplexor\XConnect\Request\Encoder;

use Amplexor\XConnect\Request\Order;

/**
 * Encoder interface for the Order object.
 *
 * @package Amplexor\XConnect
 */
interface EncoderInterface
{
    /**
     * Get the specific output for the given Amplexor\XCOnnect\Order().
     *
     * @param Order $order
     *   The order to encode.
     *
     * @return mixed
     */
    public function encode(Order $order);
}
