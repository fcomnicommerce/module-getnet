<?php

namespace FCamara\Getnet\Api;


interface GenerateQRCodeInterface
{

    /**
     * @api
     *
     * @param string $amount
     * @param string $currency
     * @param string $orderId
     * @param string $customerId
     * @return mixed
     */
    public function execute(string $amount, string $currency, string $orderId, string $customerId);
}
