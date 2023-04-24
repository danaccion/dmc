@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-success" role="alert">
        Payment action was successful.
    </div>
</div>

<!--
TIP: Append "/framed" to the action URL if you want to show the payment window in an iframe:
<form method="POST" action="https://payment.quickpay.net/framed">
-->
<form method="POST" action="https://payment.quickpay.net">
  <input type="hidden" name="version" value="v10">
  <input type="hidden" name="merchant_id" value="150863">
  <input type="hidden" name="agreement_id" value="632923">
  <input type="hidden" name="order_id" value="0001">
  <input type="hidden" name="amount" value="100">
  <input type="hidden" name="currency" value="DKK">
  <input type="hidden" name="continueurl" value="http://shop.domain.tld/continue">
  <input type="hidden" name="cancelurl" value="http://shop.domain.tld/cancel">
  <input type="hidden" name="callbackurl" value="http://shop.domain.tld/callback">
  <input type="hidden" name="checksum" value="ed93f788f699c42aefa8a6713794b4d347ff493ecce1aca660581fb1511a1816">
  <input type="submit" value="Continue to payment...">
</form>
@endsection
