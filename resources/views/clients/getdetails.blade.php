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
         
          
            @csrf

            <!-- <div>
                <h2 for="search">Clients</h2>
                <input type="text" id="search" class="form-control me-2 search" placeholder="Search for clients">
            </div>
            --->
            <table class="clients" id="clients">
                <thead>
                    <tr>
                        <th></th>
                    </tr>
                </thead>
                <br>
                <tbody>

      <table class="table" style="font-size:13px;">
      <thead>
        <tr>
          <th>Transaktionspriser</th>
          <th></th>
          <th></th>
        </tr>
        <tr>
          <th>Serviceafgifter</th>
          <th>%</th>
          <th>DKK</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Visa og Mastercard debetkort</td>
          <td>0,74%</td>
          <td>-</td>
        </tr>
        <tr>
          <td>Visa og Mastercard kreditkort</td>
          <td>0,99%</td>
          <td>-</td>
        </tr>
        <tr>
          <td>JCB og UnionPay</td>
          <td>3,85%</td>
          <td>-</td>
        </tr>
        <tr>
          <td>Diners/Discover</td>
          <td>3,75%</td>
          <td>-</td>
        </tr>
        <thead>
        <tr>
          <th>Tillægssatser ud over serviceafgifter</th>
          <th>%</th>
          <th>DKK</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Firmakort</td>
          <td>0,80%</td>
          <td>-</td>
        </tr>
        <tr>
          <td>Udenlandske kort (EU)</td>
          <td>0,00%</td>
          <td>-</td>
        </tr>
        <tr>
          <td>Udenlandske kort (ikke EU)</td>
          <td>1,20%</td>
          <td>-</td>
        </tr>
        <tr>
          <td>Card not present transaktioner og selvbetjente automater</td>
          <td>0,39%</td>
          <td>-</td>
        </tr>
        <tr>
          <td>Processing-gebyr pr. transaktion</td>
          <td>-</td>
          <td>0,19</td>
        </tr>
      </tbody>
    </table>
              
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
        <h2>History of Transaction</h2>
        @foreach($client_info as $client_inf)
        @foreach($client_main as $client_m)
        <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="transaction_number">Transaction Number: <?php echo $client_inf->transaction_id ?> </label>
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $client_m->name ?>" required>
                </div>

                <div class="form-group">
                    <label for="client_pay_number">Client Pay Number</label>
                    <input type="text" class="form-control" id="client_pay_number" name="client_pay_number" value="<?php echo $client_m->pay_no ?>" required>
                </div>

                <div class="form-group">
                    <label for="travel_description">Description of the Travel</label>
                    <textarea class="form-control" id="travel_description" name="travel_description" value="" required> <?php echo $client_inf->description ?> </textarea>
                </div>
                <br>
                <div class="form-group">
                    <label for="invoice_file">View PDF File </label>
                    <a href="http://localhost:8000/pdf/{{$client_inf->file}}"><?php echo $client_inf->file ?> </a>
                </div>
                <br>
                <div class="form-group">
                    <label for="invoice_order_number">The Invoice Order Number</label>
                    <input type="text" class="form-control" id="invoice_order_number" name="invoice_order_number" value="<?php echo $client_inf->invoice_no ?>" required>
                </div>

                <div class="form-group">
                <label for="dropdownSelect">Country</label>
                <input type="text" class="form-control" id="invoice_order_number" name="invoice_order_number" value="<?php echo $client_m->country ?>" required> <br>
                </div>

            <div class="form-group">
                <label for="currency">Select a currency:</label>
                <div class="form-check"> 
                    @if($client_inf->currency == 'DKK') 
                    <input class="form-check-input" type="radio" name="currency" id="currency1" value="DKK" checked>
                    @else
                    <input class="form-check-input" type="radio" name="currency" id="currency1" value="DKK" required>
                    @endif
                    <label class="form-check-label" for="currency1">
                        DKK
                    </label>
                    
                </div>
                <div class="form-check">
                    @if($client_inf->currency == 'SEK') 
                    <input class="form-check-input"  type="radio" name="currency" id="currency2" value="SEK" checked>
                    @else
                    <input class="form-check-input"  type="radio" name="currency" id="currency2" value="SEK" required>
                    @endif
                    <label class="form-check-label" for="currency2">
                        SEK
                    </label>
                   
                </div>
                <div class="form-check">
                    @if($client_inf->currency == 'NOK') 
                    <input class="form-check-input"  type="radio" name="currency" id="currency3" value="NOK" checked>
                    @else
                    <input class="form-check-input"  type="radio" name="currency" id="currency3" value="NOK" required>
                    @endif
                    <label class="form-check-label" for="currency3">
                        NOK
                    </label>
                </div>
                <div class="form-check">
                @if($client_inf->currency == 'EUR') 
                    <input class="form-check-input" type="radio" name="currency" id="currency4" value="EUR" checked>
                    @else
                    <input class="form-check-input" type="radio" name="currency" id="currency4" value="EUR">
                    @endif
                    <label class="form-check-label" for="currency4">
                        EUR
                    </label>
                </div>
            </div>

                <div class="form-group">
                    <label for="total_amount_to_pay">Total Amount to Pay</label>
                    <input type="text" class="form-control" id="total_amount_to_pay" name="total_amount_to_pay" value="<?php echo number_format((($client_inf->orig_amount)), 2, ',', '.')  ?>" required>
                </div>
                
                <div class="form-group">
                <label for="additional_fee">Additional Fee: (Check the checkbox to remove the fee) </label>
                <div class="input-group">
                    <div class="input-group-append" style="background-color:none" >
                    <div class="input-group-text"> <br>
                        <input type="checkbox" class="form-check-input" id="additionalFeeCheckbox">
                    </div>
                    </div>
                    <input type="text" class="form-control" id="additional_fee"  name="additional_fee" value="<?php echo number_format(($client_inf->additional_fee * 100 / 100), 2, ',', '.') ; ?>" required>
                </div>
                </div>

                <br>
                <!--<button type="submit" class="btn" style="background-color:#C48B36;">Submit</button>-->
            </form>
            @endforeach
        @endforeach
        </div>
    </div>
</div>

@endsection


