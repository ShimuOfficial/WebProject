<?php

namespace App\Library\SslCommerz;

interface SslCommerzInterface
{
    public function makePayment(array $requestData, $type = 'checkout', $pattern = 'json');
}
