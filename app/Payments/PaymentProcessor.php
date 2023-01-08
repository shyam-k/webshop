<?php

namespace App\Payments;

use App\Models\Orders;
use App\Payments\SuperpayGateway;
use Config;

class PaymentProcessor
{

    /**
     * create a payment gateway.
     *
     * @param string $gatewayName The name of the payment gateway   
     *  
     */
    public static function create($gatewayName)
    {
        $config = Config('payment');    
        switch ($gatewayName) {
            case 'Superpay':
                $superpayConfig = $config['Superpay'];
                $gateway = new SuperpayGateway();
                break;    
            default:
                throw new Exception('Invalid payment gateway');
        }
 
        return $gateway;
    }

}