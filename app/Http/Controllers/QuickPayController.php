<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientInfo;
use App\Models\OrderIdGenerator;
use Illuminate\Http\Request;
use QuickPay\QuickPay;

class QuickPayController extends Controller
{

    function encrypt($num) {
        // Return the result as a 6-digit integer
        return sprintf("%06d", $num);
    }
    
    function decrypt($encrypted) {
        // Convert the encrypted string to an integer
        $encrypted_num = intval($encrypted);
    
        // Return the result as a string
        return strval($encrypted_num);
    }

    function get_error_message($status) {
        $error_messages = [
            400 => 'Invalid authentication credentials',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            // add more error messages as needed
        ];
        
        if (array_key_exists($status, $error_messages)) {
            return [
                'status_code' => $status,
                'status_message' => $error_messages[$status]
            ];
        } else {
            return [
                'status_code' => $status,
                'status_message' => 'Unknown error'
            ];
        }
    }

    public function pensopay(Client $client, Request $request)
    {
        if(empty($request->conditions))
        {
            return back()->with('single-error','Please agree the terms and conditions.');
        }
        $client->load('client_info');
        try {
            $api_key = env('QUICKPAY_API_KEY');
            $client_Quikpay = new QuickPay(":{$api_key}");
            $request->merge(['client_id' => $client->id]);
            $request->merge(['status' => 'Initial']);
            OrderIdGenerator::create($request->all());
            
            $order_id='';
            $order_id = $this->encrypt($client->oGenerator->id);

            $facilitator = 'creditcard';
            $amount = intval($client->client_info->orig_amount);
            $currency = $client->client_info->currency;
            $testmode = true;
            $autocapture = true;
            $merchant_id = '150863';

            $initform = array(
                'order_id' => $order_id,
                'currency' => $client->client_info->currency,
                'company_name'=>$client->name,
                'country'=>$client->country,
            );
                $payments = $client_Quikpay->request->post('/payments', $initform);
                $status = $payments->httpStatus();
                if ($status == 201) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $responseData = $responseData->response_data;
                    $array = $responseData;
                    $jsonData = json_decode($array);
                    $id = $jsonData->id;
                    // Authorized
                    $url = $this->putPayment($id, $client_Quikpay, $amount, $merchant_id, $client->client_info->id);
                    header('Location:'. $url);
                    exit;
                }
                else{
                    $status_code = $status;
                    $error_message = $this->get_error_message($status_code);
                    $json_response = json_encode($error_message);
                    // Set the content type to JSON
                    header('Content-Type: application/json');

                    // Output the JSON response
                    echo $json_response;
                }
            } catch (Exception $e) {
                echo $e;
            }
    }

    public function putPayment($id, $client_Quikpay ,$amount, $merchant_id, $client_id)
    {
         $form = array(
            'id'=>$id,
            'amount' => $amount,
            "merchant_id" => $merchant_id,
            "payment_methods" => "creditcard",
            "auto_capture" => true,
            "continue_url" => route('success', ['id' => $client_id]),
            "cancel_url" =>  route('cancel', ['id' => $client_id]),
            "callback_url" => route('handleCallback')
         );
        $url = '/payments/'.$id.'/link';
        $payments =  $client_Quikpay->request->put($url, $form);
        $status = $payments->httpStatus();
        if($status == 200){
            $jsonObj = json_encode($payments);
            $responseData = json_decode($jsonObj);
            $responseData = $responseData->response_data;
            $array = $responseData;
            $jsonData = json_decode($array);
            $link = $jsonData->url;
        }
        return $jsonData->url;
    }

    public function handleCallback(Request $request)
    {
        // Get the request body
        $request_body = $request->getContent();
    
        // Calculate the checksum
        $private_key = env('QUICKPAY_PRIVATE_KEY');
        $checksum = $this->sign($request_body, $private_key);
    
        // Check if the request is authenticated
        $is_authenticated = ($checksum == $request->header('QuickPay-Checksum-Sha256'));
    
        // Parse the request body as JSON
        $data = json_decode($request_body, true);
    
        // Get the order_id from the JSON data
        $order_id = $this->decrypt($data['order_id']);
    
        if ($is_authenticated) {
            // Request is authenticated
            // Check if the operation status is approved
            $response = $this->getQuickPayStatus($data['operations'][0]['qp_status_code']);

            $response_array = json_decode($response, true);

            // Now you can access the code and message like this:
            $code = $response_array['code'];
            $message = $response_array['message'];
            $order = OrderIdGenerator::where('id', $order_id)->first();
            $order->status = $message;
            $order->save();
            $message = $code == null? 'Un-Paid' : $message ;
            $clientInfoIds = ClientInfo::where('client_id', $order->client_id)
            ->update(['status' => $message]);
            return $response;
        } else {
            // Request is NOT authenticated
            // Update the order status to Failed
            $order = OrderIdGenerator::where('id', $order_id)->first();
            $order->status = 'Failed';
            $order->save();
            
            $status_code = 401;
            $status_message = 'NOT AUTHENTICATED';
        }
    
        // Prepare the response data
        $response_data = [
            'status_code' => $status_code,
            'status_message' => $status_message
        ];
    
        // Return the response as JSON
        return response()->json($response_data);
    }
    
    function getQuickPayStatus($code) {
        $status_codes = array(
            20000 => "Approved",
            40000 => "Rejected by acquirer",
            40001 => "Request declined by acquirer",
            40002 => "Referral to acquirer",
            40003 => "Rejected due to risk",
            40004 => "Rejected due to anti-fraud",
            40005 => "Expired card",
            40006 => "Insufficient funds",
            40007 => "Card blocked",
            40008 => "Card lost or stolen",
            40009 => "Card has been restricted by the issuer",
            40010 => "Invalid card number",
            40011 => "Invalid card expiry date",
            40012 => "Invalid card security code",
            40013 => "Invalid card type",
            40014 => "Card blacklisted",
            40015 => "Invalid authentication",
            40016 => "Acquirer communication error",
            40017 => "Transaction timeout",
            40018 => "Request not allowed",
            40019 => "Request not supported",
            40020 => "Request not valid",
            40021 => "Transaction already refunded",
            40022 => "Request partially refunded",
            40023 => "Request refunded",
            40024 => "Request partially captured",
            40025 => "Request captured",
            40026 => "Request partially reversed",
            40027 => "Request reversed",
            40028 => "Request already reversed",
            40029 => "Request already settled",
            40030 => "Request partially settled",
            40031 => "Request settled",
            40032 => "Request already cancelled",
            40033 => "Request cancelled",
            40034 => "Request already updated",
            40035 => "Request updated",
            40036 => "Request already processed",
            40037 => "Request processed",
            40038 => "Request pending",
            40039 => "Request expired"
        );
    
        if (array_key_exists($code, $status_codes)) {
            return json_encode(array("code" => $code, "message" => $status_codes[$code]));
        } else {
            return json_encode(array("code" => null, "message" => "Invalid status code"));
        }
    }

    private function sign($base, $private_key)
    {
        return hash_hmac("sha256", $base, $private_key);
    }
    
    public function deletePaymentById(Request $request){
        try {
            $api_key = env('QUICKPAY_API_KEY');
            $client = new QuickPay(":{$api_key}");
            $url = "/payments/{$request->id}/link";
            echo $url;
            $payments = $client->request->delete($url);
                $status = $payments->httpStatus();
                if ($status == 200) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $response = $responseData->response_data;
                    echo $status;
                }
            } catch (Exception $e) {
                echo $e;
            }
    }

    public function getAllPaymentByOrderId(Request $request)
    {
        // Get all payments By Order ID
        try {
            $api_key = env('QUICKPAY_API_KEY');
            $client = new QuickPay(":{$api_key}");
            // Create payment
            $payments = $client->request->get('/payments?order_id='.$request->id);
                $status = $payments->httpStatus();
                if ($status == 200) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $response = $responseData->response_data;
                    $this->getTableOrder($response);
                }
            } catch (Exception $e) {
                echo $e;
            }
    }

    public function getTableOrder($response)
    {
        $output = '';

        $output .= '<table>
                            <tr>
                            <th>No.</th>
                            <th>Payment id</th>
                            <th>Company Name</th>
                            <th>Invoice No</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Date Paid</th>
                            </tr>';

                            $array = json_decode($response, true);
                            $count = 0;
                            foreach($array as $item){
                                $count++;
                                $output .= "<tr>";
                                $output .= "<td>".$count."</td>";
                                $output .= "<td>".$item['id']."</td>";
                                $output .= "<td>".$item['metadata']['shopsystem_name']."</td>";
                                $output .= "<td><button id='view' class='view' value={$item['order_id']}>View</button> ".$item['order_id']."</td>";
                                if(!empty($item['operations'])){
                                    $output .= "<td>".$item['currency']." ".$item['operations'][0]['amount']."</td>";
                                    if($item['operations'][0]['qp_status_code'] == 20000)
                                    {
                                        $code = "Approved";
                                    }
                                    else if( $item['operations'][0]['qp_status_code'] == 40000){
                                        $code = "Rejected";
                                    }
                                    else if( $item['operations'][0]['qp_status_code'] == 50000){
                                        $code = "Gateaway Error";
                                    }
                                    else if( $item['operations'][0]['qp_status_code'] == 40001){
                                        $code = "Request Data Error";
                                    }
                                    else if( $item['operations'][0]['qp_status_code'] == 40002){
                                        $code = "Authorization expired";
                                    }
                                    else if( $item['operations'][0]['qp_status_code'] == 40003){
                                        $code = "Aborted";
                                    }
                                    $output .= "<td>".$code."</td>";
                                    $output .= "<td>".$item['operations'][0]['created_at']."</td>";
                                }else{
                                    $output .= "<td>0</td>";
                                    $output .= "<td>Pending</td>";
                                    $output .= "<td>".$item['created_at']."</td>";
                                }
                                $output .= "</tr>";
                            }
                            $output .= '
                        </table>';
                        echo $output;
                        return view('quickpay.table',['orders' => $output]);
    }

    public function getHistory(Request $request)
    {
        // Get all payments By Order ID
        try {
            $api_key = env('QUICKPAY_API_KEY');;
            $client = new QuickPay(":{$api_key}");
            // Create payment
            $payments = $client->request->get('/payments');
                $status = $payments->httpStatus();
                if ($status == 200) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $response = $responseData->response_data;
                $output = '';

                $output .= '<table class="table table-hover mb-2">
                                    <tr>
                                    <th>No.</th>
                                    <th>Payment id</th>
                                    <th>Company Name</th>
                                    <th>Invoice No</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Date Paid</th>
                                    </tr>';

                                    $array = json_decode($response, true);
                                    $count = 0;
                                    $button = '<i class="bi bi-eye-fill"> </i>';
                                    foreach($array as $item){
                                        $count++;
                                        $output .= '<tr>';
                                        $output .= '<td class="text-muted fw-bold">'.$count.'</td>';
                                        $output .= '<td class="text-muted fw-bold">'.$item['id']."</td>";
                                        $output .= '<td>'.$item['metadata']['shopsystem_name'].'</td>';
                                        $output .= '<td><button id="view" class="view btn btn-primary" value="'.$item['order_id'].'">'. $button .' '.$item['order_id'].'</button></td>';
                                        if(!empty($item['operations'])){
                                            $output .= "<td>".$item['currency']." ".$item['operations'][0]['amount']."</td>";
                                            if($item['operations'][0]['qp_status_code'] == 20000)
                                            {
                                                $code = "Approved";
                                            }
                                            else if( $item['operations'][0]['qp_status_code'] == 40000){
                                                $code = "Rejected";
                                            }
                                            else if( $item['operations'][0]['qp_status_code'] == 50000){
                                                $code = "Gateaway Error";
                                            }
                                            else if( $item['operations'][0]['qp_status_code'] == 40001){
                                                $code = "Request Data Error";
                                            }
                                            else if( $item['operations'][0]['qp_status_code'] == 40002){
                                                $code = "Authorization expired";
                                            }
                                            else if( $item['operations'][0]['qp_status_code'] == 40003){
                                                $code = "Aborted";
                                            }
                                            $data = $code == 'Approved' ? 'badge bg-success' : 'badge bg-danger';
                                            $output .= '<td class="mt-2 '.$data.'">'.$code.'</td>';
                                            $output .= "<td>".$item['operations'][0]['created_at']."</td>";
                                        }else{
                                            $output .= "<td>0</td>";
                                            $output .= '<td class="mt-2 badge bg-secondary">Un-Paid</td>';
                                            $output .= "<td>".$item['created_at']."</td>";
                                        }
                                        $output .= "</tr>";
                                    }
                                    $output .= '
                                </table>';
                                return view('quickpay.table',['orders_history' => $output]);
                                }
                            } catch (Exception $e) {
                                echo $e;
                            }
    }

    public function getInvoice(Request $request)
    {
        // Get all payments By Order ID
        $clientInfoIds = ClientInfo::where('invoice_no', '=', $request->viewId)->pluck('file');
        return response()->json($clientInfoIds);
    }

    public function quickPayTable()
    {
        return view('quickpay.table');
    }
    
    public function getAllPayment()
    {
        // Get all payments
        try {
            $api_key = env('QUICKPAY_API_KEY');
            $client = new QuickPay(":{$api_key}");
            // Create payment
            $payments = $client->request->get('/payments');
                $status = $payments->httpStatus();
                if ($status == 200) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $responseData = $responseData->response_data;
                    $array = $responseData;
                }
            } catch (Exception $e) {
                echo $e;
            }
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
