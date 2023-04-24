<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client; // import the Client model

class ClientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $client = Client::all(); // retrieve all clients from the database
    
        return view('home', compact('clients')); // pass clients data to the view
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $clients = Client::where('name', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%")
                    ->get();

        return view('home', compact('clients'));
    }

}
