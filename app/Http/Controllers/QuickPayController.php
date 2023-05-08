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

    public function pensopay(Client $client, Request $request)
    {
        if(empty($request->conditions))
        {
            return back()->with('single-error','Please agree the terms and conditions.');
        }
        $client->load('client_info');
        try {
            $api_key = '86f281fd65f00d33e5712587379532aeb283049735c209d2ded8f645b9a18dd2';
            $client_Quikpay = new QuickPay(":{$api_key}");
            // Create payment
            // IINIT
            $request->merge(['client_id' => $client->id]);
            $request->merge(['status' => 'Un-Paid']);
            OrderIdGenerator::create($request->all());
            
            $order_id='';
            $order_id = $this->encrypt($client->oGenerator->id);

            $facilitator = 'creditcard';
            $amount = intval($client->client_info->orig_amount);
            $currency = 'DKK';
            $testmode = true;
            $autocapture = true;
            $merchant_id = '171565';

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
                    $this->putPayment($id, $client_Quikpay, $amount, $merchant_id);
                }
            } catch (Exception $e) {
                echo $e;
            }
    }

    public function putPayment($id, $client_Quikpay ,$amount, $merchant_id)
    {
         $form = array(
            'id'=>$id,
            'amount' => $amount,
            "merchant_id" => $merchant_id,
            "payment_methods" => "creditcard",
            "auto_capture" => true,
            "continue_url" => route('success'),
            "cancel_url" =>  route('cancel'),
            "callback_url" => 'https://stg.dmc-pay.com/handleCallback'
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
            return redirect($jsonData->url);
        }
        return $jsonData->url;
    }

    public function handleCallback(Request $request)
    {

        // Get the request body
        $request_body = $request->getContent();
        // Calculate the checksum
        $private_key = "86f281fd65f00d33e5712587379532aeb283049735c209d2ded8f645b9a18dd2";
        $checksum = $this->sign($request_body, $private_key);
        $checksum2 =$this->sign($request_body, $request->header('QuickPay-Checksum-Sha256')); 

        // Check if the request is authenticated
        if ($checksum == $checksum2) {
            // Request is authenticated
            // Your code here
            $message = 'Authenticated';

            // Parse the request body as JSON
            $data = json_decode($request_body, true);

            // Get the order_id from the JSON data
            $order_id = $data['order_id'];

            $order_id = $this->decrypt($order_id);
            // $clientInfoIds = ClientInfo::where('client_id', $order_id)
            //     ->update(['status' => 'Paid']);
            OrderIdGenerator::where('id', $order_id)
                ->update(['status' => 'Paid']);
            $data = [
                'status_code' => 200,
                'status_message' => 'OK'
            ];
        } else {
            $message = 'NOT authenticated';
               // Parse the request body as JSON
            $data = json_decode($request_body, true);

            // Get the order_id from the JSON data
            $order_id = $data['order_id'];

            $order_id = $this->decrypt($order_id);
            
            $clientInfoIds = ClientInfo::where('id', $order_id)
            ->update(['status' => 'Failed']);
            // Request is NOT authenticated
            // Your code here
            $data = [
                'status_code' => 401,
                'status_message' => 'NOT AUTHENTICATED'
            ];
        }
    
        return response()->json($data);
    }
    

    private function sign($base, $private_key)
    {
        return hash_hmac("sha256", $base, $private_key);
    }
    
    public function deletePaymentById(Request $request){
        try {
            $api_key = 'c131a294f2a44ff648ade3941195fcda6a83c2b579e788ac16327b8701735c1b';
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
            $api_key = 'c131a294f2a44ff648ade3941195fcda6a83c2b579e788ac16327b8701735c1b';
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
            $api_key = '86f281fd65f00d33e5712587379532aeb283049735c209d2ded8f645b9a18dd2';
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
            $api_key = 'c131a294f2a44ff648ade3941195fcda6a83c2b579e788ac16327b8701735c1b';
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

    public function pay(){

        try {
            $api_key = '86f281fd65f00d33e5712587379532aeb283049735c209d2ded8f645b9a18dd2';
            $client = new QuickPay(":{$api_key}");
            // Create payment
            $order_id='';
            $random_number = mt_rand(100000, 999999);
            $order_id .= $random_number;
            $facilitator = 'creditcard';
            $amount = 500;
            $currency = 'DKK';
            $testmode = true;
            $autocapture = true;

            $initform = array(
                'order_id' => $order_id,
                'currency' => $currency,
                'street'=>'dan',
                'city'=>'company name',
                'zip_code' =>'8000',
                'country_code' => 'PH',
                'email' =>'d@mail.com'
                
            );
                $payments = $client->request->post('/payments', $initform);
                $status = $payments->httpStatus();
                if ($status == 201) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $responseData = $responseData->response_data;
                    $array = $responseData;
                    $jsonData = json_decode($array);
                    $id = $jsonData->id;
                    // Authorized
                    $this->putPayment($id, $client);
                }
            } catch (Exception $e) {
                echo $e;
            }
   
    }

    // public function putPayment($id, $client)
    // {
    //      $form = array(
    //         'id'=>$id,
    //         'amount' => 1,
    //         "merchant_id" => 171565,
    //         "payment_methods" => "creditcard",
    //         "auto_capture" => true,
    //         "continue_url" => route('success'),
    //         "cancel_url" =>  route('cancel'),
    //         "callback_url" => route('handleCallback')
    //      );
    //     $url = '/payments/'.$id.'/link';
    //     $payments =  $client->request->put($url, $form);
    //     $status = $payments->httpStatus();
    //     if($status == 200){
    //         $jsonObj = json_encode($payments);
    //         $responseData = json_decode($jsonObj);
    //         $responseData = $responseData->response_data;
    //         $array = $responseData;
    //         $jsonData = json_decode($array);
    //         $link = $jsonData->url;
    //         echo $link;
    //         header('Location:'.$link);
    //         exit();
    //     }
    // }

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
