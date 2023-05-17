@extends('layouts.app')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('#additionalFeeCheckbox').change(function() {
      if ($(this).is(':checked')) {
        $('#additional_fee').val('');
      }
    });
  });
</script>
@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="container">
    <div class="row">
        <div class="col-md-4" style="border-right: 0.5px solid grey; padding-left: 10px; font: size 15px;">
          <!--  <h1>Clients</h1>
            <form action="{{ route('clients.search') }}" method="GET">
                <div class="form-group">
                    <input type="text" class="form-control" name="query" placeholder="Search clients...">
                </div>
                <button style="margin-top:10px;" type="submit" class="btn btn-primary">Search</button>
            </form>
            <hr>-->

        <!--
                <div>
                    <label for="search">Search clients:</label>
                    <input type="text" id="search" name="search" autocomplete="off">
                </div>

            <form action="/deleteclient" method="POST">
                @csrf
                @foreach($clients as $client)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="clients[]" value="{{ $client->id }}">
                    <label class="form-check-label" for="{{ $client->id }}">
                        {{ $client->name }}
                    </label>
                </div>
                @endforeach
                <hr>
                <button type="submit" class="btn btn-danger">Delete selected clients</button>
            </form>
-->
            

          
            @csrf

            <div>
                <h2 for="search">Clients</h2>
                <input type="text" id="search" class="form-control me-2 search" placeholder="Search for clients">
            </div>

            <table class="clients" id="clients">
                <thead>
                    <tr>
                        <th></th>
                    </tr>
                </thead>
                <br>
                <tbody>
                  <!--  @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->name }}</td>
                            <td>
                            <button class="btn btn-danger btn-sm" data-client-id="{{ $client->id }}">
                            <i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    @endforeach -->
                </tbody>
            </table>
            <div id="pagination"></div>

<!-- <script>
    $('#search').on('input', function() {
        var query = $(this).val();
        $.ajax({
            url: '/clientssearch',
            method: 'POST',
            data: {query: query},
            success: function(response) {
                $('tbody').html(response);
            }
        });
    });
</script> -->
        </div>
        <div class="col-md-6" style="padding-left:20px; font-size:15px;">
        <h2>Create New Transaction</h2>
        <form method="post" action="{{ route('admin.payment.store',$client) }}"  enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="transaction_number">Transaction Number:</label>
                    {{$transactionId}}
                    <input type="hidden" class="form-control" id="transaction_number" name="transaction_number" value="<?php echo $transactionId?>" required>
                    <br> <br>
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                </div>

                <div class="form-group">
                    <label for="client_pay_number">Client Pay Number</label>
                    <input type="number" class="form-control" id="client_pay_number" name="client_pay_number" required>
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
                <label for="dropdownSelect">Country</label>
                <select name="country" class="form-control" id="dropdownSelect">
                    <option value="DMC Denmark">DMC Denmark</option>
                    <option value="DMC Nordic">DMC Nordic</option>
                    <option value="DMC Norway">DMC Norway</option>
                    <option value="DMC Sweden">DMC Sweden</option>
                </select>
                </div>

                <div class="form-group">
                <label for="currency">Select a currency:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="currency" id="currency1" value="DKK">
                    <label class="form-check-label" for="currency1">
                    DKK
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="currency" id="currency2" value="SEK">
                    <label class="form-check-label" for="currency2">
                    SEK
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="currency" id="currency3" value="NOK">
                    <label class="form-check-label" for="currency3">
                    NOK
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="currency" id="currency4" value="EUR">
                    <label class="form-check-label" for="currency4">
                    EUR
                    </label>
                </div>
                </div>

                <div class="form-group">
                    <label for="total_amount_to_pay">Total Amount to Pay</label>
                    <input type="number" class="form-control" id="total_amount_to_pay" name="total_amount_to_pay" required>
                </div>
                
                <div class="form-group">
                <label for="additional_fee">Additional Fee: (Check the checkbox to remove the fee) </label>
                <div class="input-group">
                    <div class="input-group-append" style="background-color:none" >
                    <div class="input-group-text"> <br>
                        <input type="checkbox" id="additionalFeeCheckbox">
                    </div>
                    </div>
                    <input type="text" class="form-control" id="additional_fee" value="1.25" name="additional_fee">
                </div>
                </div>

                <br>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

@endsection
