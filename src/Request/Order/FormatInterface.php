<?php

namespace Amplexor\XConnect\Request\Order;

use Amplexor\XConnect\Request\Order;

/**
 * Format interface for the Order object.
 *
 * @package Amplexor\XConnect
 */
interface FormatInterface
{
    /**
     * Get the specific output for the given Amplexor\XCOnnect\Order().
     *
     * @param Order $order
     *   The order to format.
     *
     * @return mixed
     */
    public function format(Order $order);
}
