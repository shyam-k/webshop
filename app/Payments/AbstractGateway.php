<?php 

namespace App\Payments;

use App\Models\Orders;

abstract class AbstractGateway implements GatewayInterface
{
    private $testMode = false;

    public function init(array $config) 
    {
        return true;
    }
    public function setTestMode(bool $testMode) {
        $this->testMode = $testMode;
    }
    public function purchase(Orders $orders, array $parameters = []): bool {
        return false;
    }
    public function refund(float $amount, array $parameters, string $transactionId): bool {
        return false;
    }
}