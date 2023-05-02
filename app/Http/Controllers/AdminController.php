<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $clients = Client::where('status','on')->orderby('name','asc')->paginate(10); // retrieve all clients from the database

        return view('admin', compact('clients'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $clients = Client::where('name', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%")
                    ->get();

        return view('home', compact('clients'));
    }

    public function delete($id)
    {
        dd($id);
        Client::findOrFail($id)->delete();
        return redirect()->route('home')->with('success', 'Client deleted successfully');
    }

}
