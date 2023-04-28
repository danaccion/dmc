<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class pensopayController extends Controller
{
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

        $order_id = '1234';
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

    public function pensopay(Client $client, Request $request)
    {
        if(empty($request->conditions))
        {
            return back()->with('single-error','Please agree the terms and conditions.');
        }
        $client->load('client_info');
        $order = [
            'billing_address' => [
                'name' => auth()->user()->name,
                'client_company' => $client->name,
                'address' => $client->country,
                'pay_no' => $client->pay_no,
                'currency' => $client->client_info->currency,
                'email' => auth()->user()->email,
            ],
        ];

		$order_id = $client->client_info->invoice_no;
        $facilitator = 'creditcard';
        $amount = $client->client_info->orig_amount;
        $currency = $client->client_info->currency;
        $testmode = true;
        $success_url = route('success');
        $cancel_url = route('cancel');
        $callback_url = route('callback');
        $autocapture = true;

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

    function sign($params, $api_key) {
        $flattened_params = $this->flatten_params($params);
        ksort($flattened_params);
        $base = implode(" ", $flattened_params);
        dd(hash_hmac("sha256", $base, $api_key));
        return hash_hmac("sha256", $base, $api_key);
    }

    function flatten_params($obj, $result = array(), $path = array()) {
        if (is_array($obj)) {
            foreach ($obj as $k => $v) {
                $result = array_merge($result, $this->flatten_params($v, $result, array_merge($path, array($k))));
            }
        } else {
            $result[implode("", array_map(function($p) { return "[{$p}]"; }, $path))] = $obj;
        }

        return $result;
    }

    public function pensopayForm(){
        $params = array(
            "version"      => "v10",
            "merchant_id"  => 150863,
            "agreement_id" => 632923,
            "order_id"     => "0001",
            "amount"       => 100,
            "currency"     => "DKK",
            "continueurl" => "http://shop.domain.tld/continue",
            "cancelurl"   => "http://shop.domain.tld/cancel",
            "callbackurl" => "http://shop.domain.tld/callback",
          );

          $params["checksum"] = $this->sign($params, "ed93f788f699c42aefa8a6713794b4d347ff493ecce1aca660581fb1511a1816");
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
