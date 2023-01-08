<?php 

namespace App\Payments;

use Illuminate\Support\Facades\Http;
use App\Models\Orders;

class SuperpayGateway extends AbstractGateway
{
    public function purchase(Orders $orders, array $parameters = []): bool {
        $price = number_format(($orders->products->sum('price')),2);
        $data = [
            'order_id' => $orders->id,
            'customer_email' => $orders->customer->email,
            'value' =>  $price 
        ];      
        $response = Http::post('https://superpay.view.agentur-loop.com/pay', $data);
        $jsonData = $response->json();
        if( $jsonData['message'] == 'Payment Successful' ){
            return true;
        }else{
            return false;
        }
    }
}