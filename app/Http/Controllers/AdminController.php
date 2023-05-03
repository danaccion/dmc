<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientInfo;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function adminIndex(Request $request)
    {
        $clients = Client::where('status','on')->orderby('name','asc')->paginate(10); // retrieve all clients from the database

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

        return view('admin', [
            'clients'=> $clients,
            'client' => $client,
            's' => $s,
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
    public function store(Client $client,Request $request)
    {
        $request->validate([
            "invoice_file" => "required|mimetypes:application/pdf|max:10000"
        ]);
        $file= $request->file('invoice_file');
        $filename= date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('pdf'), $filename);
        $input['file']= $filename;
        $client->load('client_info');
        $request->merge(['file' => $filename]);
        $request->merge(['client_id' => $client->id]);
        $request->merge(['post_id' => null]);
        $request->merge(['description' => $request->travel_description]);
        $request->merge(['currency' => $client->client_currency]);
        $request->merge(['invoice_no' => $request->invoice_order_number]);
        $request->merge(['orig_amount' => $request->total_amount_to_pay]);

        // 👇 replace numbers with empty string
        $result = str_replace('.', "", $request->total_amount_to_pay);
        $request->merge(['amount' => $result]);
        $request->merge(['post_id' => 0]);  
        $currency = $client->client_info->currency;

        ClientInfo::create($request->all());


        $clients = Client::where('status','on')->orderby('name','asc')->paginate(10); // retrieve all clients from the database

        return view('pensopay.success');
    }

    public function index()
    {
        $clients = Client::where('status','on')->orderby('name','asc')->paginate(10); // retrieve all clients from the database

        $query = $request->input('query');

        $clients2 = Client::where('name', 'like', '%' . $query . '%')
                         ->orWhere('email', 'like', '%' . $query . '%')
                         ->get();

       
        return view('admin', compact('clients'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $clients = Client::where('name', 'like', '%' . $query . '%')
                         ->orWhere('email', 'like', '%' . $query . '%')
                         ->get();

        return view('clients.search_results', [
            'clients' => $clients,
        ]);
    }

    public function delete($id)
    {
        dd($id);
        Client::findOrFail($id)->delete();
        return redirect()->route('home')->with('success', 'Client deleted successfully');
    }

}
