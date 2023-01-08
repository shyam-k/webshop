<?php

namespace App\Payments;

use App\Models\Orders;

interface GatewayInterface
{
    public function init(array $config);
    public function setTestMode(bool $testMode);
    public function purchase(Orders $orders, array $parameters = []): bool;
    public function refund(float $amount, array $parameters, string $transactionId): bool;
}
