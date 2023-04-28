<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client; // import the Client model

class ClientController extends Controller
{
    public function clientIndex(Request $request)
    {
        $s = $request->s ?? "";
        if(!empty($s)) {
            $client = Client::query()
                ->with('client_info')
                ->where('pay_no',$s)
                ->Orwhere('name', $s)
                ->first();
        }
        else {
            $client = null;
        }

        return view('clients.index', [
            'client' => $client,
            's' => $s,
            'text' => 'We hereby provide you with the service of paying with credit card online, at our new pay system.',
            'conditions' => 'DMC conditions for payment of our services by credit card: All payments by credit card to the DMC
                group of companies are non-refundable and subjects to a subcharge corresponding to the charge imposed by the credit
                card company to DMC. These charges are based on currency quoted and accepted by our client.',
        ]);
    }
}
