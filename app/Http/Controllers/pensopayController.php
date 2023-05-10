<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\OrderIdGenerator;
use Illuminate\Http\Request;

class pensopayController extends Controller
{

    public function pensopay(Client $client, Request $request)
    {
        if (empty($request->conditions)) {
            return back()->with('single-error', 'Please agree the terms and conditions.');
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

        $order_id = $this->encrypt(optional($client->oGenerator)->id, 'secretkey');

        $request->merge(['client_id' => $client->id]);
        OrderIdGenerator::create($request->all());

        $facilitator = 'creditcard';
        $amount = intval($client->client_info->orig_amount);
        $currency = $client->client_info->currency;
        $testmode = true;
        $success_url = route('success');
        $cancel_url = route('cancel');
        $callback_url = route('handleCallback');
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
        // print_r($result);
        if (!isset($data['link'])) {
            $order_id = $this->encrypt(optional($client->oGenerator)->id, 'secretkey');
            $request->merge(['client_id' => $client->id]);
            OrderIdGenerator::create($request->all());
        }
        ;
        header('Location: ' . $data['link']);
        curl_close($ch);
        exit;
    }

    function encrypt($value, $key)
    {
        // Convert the value to a string and add padding if necessary
        $padded = str_pad((string) $value, 6, '0', STR_PAD_LEFT);

        // Apply XOR encryption to the digits using the secret key
        $encrypted = '';
        for ($i = 0; $i < 6; $i++) {
            $encrypted .= chr(ord($padded[$i]) ^ ord($key[$i % strlen($key)]));
        }

        // Convert the resulting string to an integer
        $result = intval(bin2hex($encrypted), 16);

        return $result;
    }

    function decrypt($encrypted, $key)
    {
        // Convert the encrypted integer to a binary string
        $hex = dechex($encrypted);
        $hex_padded = str_pad($hex, 12, '0', STR_PAD_LEFT);
        $binary = hex2bin($hex_padded);

        // Apply XOR decryption to the bytes using the secret key
        $decrypted = '';
        for ($i = 0; $i < 6; $i++) {
            $decrypted .= chr(ord($binary[$i]) ^ ord($key[$i % strlen($key)]));
        }

        // Convert the resulting string to an integer
        $result = intval($decrypted);

        return $result;
    }


    function sign($params, $api_key)
    {
        $flattened_params = $this->flatten_params($params);
        ksort($flattened_params);
        $base = implode(" ", $flattened_params);
        dd(hash_hmac("sha256", $base, $api_key));
        return hash_hmac("sha256", $base, $api_key);
    }

    function flatten_params($obj, $result = array(), $path = array())
    {
        if (is_array($obj)) {
            foreach ($obj as $k => $v) {
                $result = array_merge($result, $this->flatten_params($v, $result, array_merge($path, array($k))));
            }
        } else {
            $result[implode("", array_map(function ($p) {
                return "[{$p}]";
            }, $path))] = $obj;
        }

        return $result;
    }

    public function pensopayForm()
    {
        $params = array(
            "version" => "v10",
            "merchant_id" => 150863,
            "agreement_id" => 632923,
            "order_id" => "0001",
            "amount" => 100,
            "currency" => "DKK",
            "continueurl" => "http://shop.domain.tld/continue",
            "cancelurl" => "http://shop.domain.tld/cancel",
            "callbackurl" => "http://shop.domain.tld/callback",
        );

        $params["checksum"] = $this->sign($params, "ed93f788f699c42aefa8a6713794b4d347ff493ecce1aca660581fb1511a1816");
    }

}