@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4" style="border-right: 0.5px solid grey; padding-left: 10px; font: size 15px;">
            <h1>Clients</h1>
            <form action="{{ route('clients.search') }}" method="GET">
                <div class="form-group">
                    <input type="text" class="form-control" name="query" placeholder="Search clients...">
                </div>
                <button style="margin-top:10px;" type="submit" class="btn btn-primary">Search</button>
            </form>
            <hr>

            <form action="{{ route('clients.delete', ['2']) }}" method="POST">
                @csrf
                @csrf
                @method('DELETE')
                @foreach($clients as $client)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="clients[]" value="{{ $client->id }}">
                    <label class="form-check-label" for="{{ $client->id }}">
                        {{ $client->name }}
                    </label>
                </div>
                @endforeach
                <hr>
                <button type="submit" value="Delete" class="btn btn-danger">Delete selected clients</button>
            </form>
        </div>
        <div class="col-md-6" style="padding-left:20px; font-size:15px;">
        <h1>Create New Transaction</h1>
            <form method="POST" action="" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="transaction_number">Transaction Number</label>
                    <input type="text" class="form-control" id="transaction_number" name="transaction_number" required>
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                </div>

                <div class="form-group">
                    <label for="client_pay_number">Client Pay Number</label>
                    <input type="text" class="form-control" id="client_pay_number" name="client_pay_number" required>
                </div>

                <div class="form-group">
                    <label for="travel_description">Description of the Travel</label>
                    <textarea class="form-control" id="travel_description" name="travel_description" required></textarea>
                </div>
                <br>
                <div class="form-group">
                    <label for="invoice_file">Upload Invoice</label>
                    <input type="file" class="form-control-file" id="invoice_file" name="invoice_file" required>
                </div>
                <br>
                <div class="form-group">
                    <label for="invoice_order_number">The Invoice Order Number</label>
                    <input type="text" class="form-control" id="invoice_order_number" name="invoice_order_number" required>
                </div>

                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" id="country" name="country" required>
                </div>

                <div class="form-group">
                    <label for="currency">Currency</label>
                    <input type="text" class="form-control" id="currency" name="currency" required>
                </div>

                <div class="form-group">
                    <label for="total_amount_to_pay">Total Amount to Pay</label>
                    <input type="number" class="form-control" id="total_amount_to_pay" name="total_amount_to_pay" required>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

@endsection
