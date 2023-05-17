<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientInfo;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function adminIndex(Request $request)
    {
        $clients = Client::where('status', 'on')->orderby('name', 'asc')->paginate(10); // retrieve all clients from the database
        $transactionId = uniqid('TXN');
        // Check if the transaction ID already exists in the database and regenerate if necessary
        while (ClientInfo::where('transaction_id', $transactionId)->exists()) {
            $transactionId = uniqid('TXN');
        }

        $s = $request->s ?? "";
        if (!empty($s)) {
            $client = Client::query()
                ->with('client_info')
                ->where('pay_no', $s)
                ->Orwhere('name', $s)
                ->first();
        } else {
            $client = null;
        }
        return view('admin', [
            'clients' => $clients,
            'client' => $client,
            's' => $s,
            'transactionId' => $transactionId,
            'text' => 'We hereby provide you with the service of paying with credit card online, at our new pay system.',
            'conditions' => 'DMC conditions for payment of our services by credit card: All payments by credit card to the DMC
                group of companies are non-refundable and subjects to a subcharge corresponding to the charge imposed by the credit
                card company to DMC. These charges are based on currency quoted and accepted by our client.',
        ]);
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Client $client, Request $request)
    {
        $request->validate([
            "invoice_file" => "required|mimetypes:application/pdf|max:10000"
        ]);

        $client = new Client();
        $client->name = $request->input('company_name');
        $client->pay_no = $request->input('client_pay_number');
        $client->use_no = 1;
        $client->role = 'admin';
        $client->status = 'on';
        $client->client_currency = $request->input('currency');
        $client->country = $request->input('country');
        $client->created_at = Carbon::now();
        $client->save();

        $client1 = Client::where('pay_no', $request->input('client_pay_number'))->first();
       
        $file = $request->file('invoice_file');
        $filename = date('YmdHi') . $file->getClientOriginalName();
        $file->move(public_path('pdf'), $filename);
        $input['file'] = $filename;
        $client->load('client_info');
        $request->merge(['file' => $filename]);
        $request->merge(['client_id' => $client->id]);
        $request->merge(['post_id' => null]);
        $request->merge(['description' => $request->travel_description]);
        $request->merge(['currency' => $client1->client_currency]);
        $request->merge(['invoice_no' => $request->invoice_order_number]);
        $request->merge(['orig_amount' => $request->total_amount_to_pay + $request->additional_fee]);
        $request->merge(['transaction_id' => $request->transaction_number]);
        $request->merge(['additional_fee' => $request->additional_fee]);

        // 👇 replace numbers with empty string
        $result = str_replace('.', "", $request->total_amount_to_pay);
        $request->merge(['amount' => $result]);
        $request->merge(['post_id' => 0]);
        $currency = $client1->client_currency;

        ClientInfo::create($request->all());


        $clients = Client::where('status', 'on')->orderby('name', 'asc')->paginate(10); // retrieve all clients from the database
        $transactionId = uniqid('TXN');
        // Check if the transaction ID already exists in the database and regenerate if necessary
        while (ClientInfo::where('transaction_id', $transactionId)->exists()) {
            $transactionId = uniqid('TXN');
        }
        //return view('admin', ['clients' => $clients, 'transactionId' => $transactionId])->with('success', 'Operation completed successfully.');
        return redirect()->back()->with('success', 'Transaction Successfully Created!');
    }

    public function index(Request $request)
    {
        $clients = Client::where('status', 'on')->orderby('name', 'asc')->paginate(10); // retrieve all clients from the database
        return view('admin', compact('clients'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $clientss = Client::where('name', 'LIKE', '%' . $query . '%')->where('status', 'on')->orderby('name', 'asc')->get();
        return response()->json($clientss);
    }
    

    public function remove(Request $request)
    {
        try {
            Client::findOrFail($request->id)->delete();
            return response()->json(['success' => true]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Client not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }        
 
    public function delete(Request $request)
    {
        dd('test');
        $id = $request->input('client_id');
        $client = Client::find($id);
        dd($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $client->delete();

        return response()->json(['message' => 'Client deleted'], 200);
        
        }
    
}