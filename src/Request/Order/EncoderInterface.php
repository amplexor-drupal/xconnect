<?php

namespace Amplexor\XConnect\Request\Order;

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
