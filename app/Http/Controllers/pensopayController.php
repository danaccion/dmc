<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class pensopayController extends Controller
{
    //
    public function post(Request $request)
    {
        $order = [
            'billing_address' => [
                'name' => 'Firstname Lastname',
                'address' => 'Søndergade 23b, 2.t.v.',
                'zipcode' => '7100',
                'city' => 'Vejle',
                'country' => 'DNK',
                'email' => 'support@pensopay.com',
                'phone_number' => '77344388',
                'mobile_number' => '77344388'
            ],
            'shipping_address' => [
                'name' => 'Firstname Lastname',
                'address' => 'Søndergade 23b, 2.t.v.',
                'zipcode' => '7100',
                'city' => 'Vejle',
                'country' => 'DNK',
                'email' => 'support@pensopay.com',
                'phone_number' => '77344388',
                'mobile_number' => '77344388'
            ],
            'basket' => [
                0 => [
                    'qty' => 1,
                    'sku' => 'item-sku',
                    'vat' => 0.25,
                    'name' => 'My awesome item',
                    'price' => 500
                ],
                'qty' => 20,
                'name' => 'Warren Arendain',
                'vat' => 212071.6,
                'sku' => 'omnis',
                'price' => 11
            ],
            'shipping' => [
                'amount' => 4900,
                'method' => 'own_delivery',
                'company' => 'My shipping company',
                'vat_rate' => 0.25
            ]
        ];

        $order_id = '1234777';
        $facilitator = 'creditcard';
        $amount = 500;
        $currency = 'DKK';
        $testmode = true;
        $success_url = 'https://www.google.com/search?q=success';
        $cancel_url = 'https://www.google.com/search?q=cancel';
        $callback_url = 'https://www.google.com/search?q=callback';
        $autocapture = false;

        $data = [
            'order' => $order,
            'order_id' => $order_id,
            'facilitator' => $facilitator,
            'amount' => $amount,
            'currency' => $currency,
            'testmode' => $testmode,
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
            'callback_url' => $callback_url,
            'autocapture' => $autocapture
        ];

        $data_string = json_encode($data);

        // Generated @ codebeautify.org
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.pensopay.com/v1/payments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer 2e94879f5a767043f0cdb4d5225db5a628efb50f88183956be6a4909f86fc927';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $data = json_decode($result, true);
        foreach ($data['Results'] as $result) {
                echo $result['link']; 
                header("Location: ".$result['link']);
        };

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
    }
    
    public function pensopay(Request $request)
    {
        $order = [
            'billing_address' => [
                'name' => 'Firstname Lastname',
                'address' => 'Søndergade 23b, 2.t.v.',
                'zipcode' => '7100',
                'city' => 'Vejle',
                'country' => 'DNK',
                'email' => 'support@pensopay.com',
                'phone_number' => '77344388',
                'mobile_number' => '77344388'
            ],
            'shipping_address' => [
                'name' => 'Firstname Lastname',
                'address' => 'Søndergade 23b, 2.t.v.',
                'zipcode' => '7100',
                'city' => 'Vejle',
                'country' => 'DNK',
                'email' => 'support@pensopay.com',
                'phone_number' => '77344388',
                'mobile_number' => '77344388'
            ],
            'basket' => [
                0 => [
                    'qty' => 1,
                    'sku' => 'item-sku',
                    'vat' => 0.25,
                    'name' => 'My awesome item',
                    'price' => 500
                ],
                'qty' => 20,
                'name' => 'Warren Arendain',
                'vat' => 212071.6,
                'sku' => 'omnis',
                'price' => 11
            ],
            'shipping' => [
                'amount' => 4900,
                'method' => 'own_delivery',
                'company' => 'My shipping company',
                'vat_rate' => 0.25
            ]
        ];
		$order_id='';
		 $random_number = mt_rand(100000, 999999);

		 $order_id .= $random_number;
        $facilitator = 'creditcard';
        $amount = 500;
        $currency = 'DKK';
        $testmode = true;
        $success_url = route('success');
        $cancel_url = route('cancel');
        $callback_url = route('callback');
        $autocapture = false;

        $data = [
            'order' => $order,
            'order_id' => $order_id,
            'facilitator' => $facilitator,
            'amount' => $amount,
            'currency' => $currency,
            'testmode' => $testmode,
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
            'callback_url' => $callback_url,
            'autocapture' => $autocapture
        ];

        $data_string = json_encode($data);

        // Generated @ codebeautify.org
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.pensopay.com/v1/payments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer 2e94879f5a767043f0cdb4d5225db5a628efb50f88183956be6a4909f86fc927';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $data = json_decode($result, true);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        echo $data['link'];
        header('Location: '.$data['link']);
        curl_close($ch);
        exit;
    }

    public function getSuccess(Request $request)
    {
        return view('pensopay.success');
    }

    public function getCancel(Request $request)
    {
        return view('pensopay.cancel');
    }

    public function getCallback(Request $request)
    {
        return view('pensopay.callback');
    }
}
