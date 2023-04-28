@extends('layouts.app')
@section('title','Client')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <span class="text-muted h5">
                        {{ $text }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-5">
                        <h3 class="text-muted"> Welcome to DMC Pay</h3>
                    </div>
                    @include('components.alerts.success')
                    <form method="get">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="search"  name="s" value="{{ $s }}" class="form-control rounded-0 mb-2" placeholder="Please enter your given 'Pay No' here">
                            </div>
                            <div class="col-md-3 mb-5">
                                <button type="submit" class="btn btn-primary form-control rounded-0">
                                    View
                                    <i class="bi bi-search float-end mt-1"> </i>
                                </button>
                            </div>
                        </form>
                        <div class="col-md-12 mb-5">
                            <h3 class="text-muted"> Company Name, </h3>
                            <span class="text-muted">
                                <h5 class="fw-bold">
                                    {{ !empty($client) ? $client->name : 'Unknown' }}
                                </h5>
                                <div class="text-muted text-xs">
                                    {{ !empty($client) ? $client->country : 'Unknown' }}
                                </div>
                            </span>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="text-muted"> You can see your invoice here </label>
                            <div class="row">
                                <div class="col-md-5">
                                    <a href=""
                                        class="btn btn-primary form-control rounded-0 mb-2"
                                        target="_blank"
                                        download="{{ $client->client_info->file }}">
                                        Download Invoice
                                        <i class="bi bi-download float-end mt-1"> </i>
                                    </a>
                                </div>
                                <div class="col-md-5">
                                    <a href="{{ !empty($client->client_info->file) ? $client->client_info->file : 'Unknown' }}"
                                        class="btn btn-primary form-control rounded-0 mb-2"
                                        target="_blank">
                                        View Invoice
                                        <i class="bi bi-view-list float-end mt-1"> </i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <span class="text-muted"> Your order no. is
                                <h3 class="fw-bold">
                                    {{ !empty($client->client_info) ? $client->client_info->invoice_no : 'No data'}}
                                </h3>
                            </span>
                            <span class="text-muted"> Total amount
                                <h3 class="fw-bold">
                                    {{ !empty($client->client_info) ? $client->client_info->currency : '' }}
                                    {{ !empty($client->client_info) ? $client->client_info->orig_amount : '0.00' }}
                                </h3>
                            </span>
                        </div>
                        <div class="col-md-12 mb-2">
                            <span class="text-muted fw-bold">
                                Please accept DMC condition
                            </span>
                        </div>
                        <form method="post" action="{{ route('client.payment.pensopay',$client) }}">
                            <div class="col-md-12 mb-2">
                                <input type="radio" class="form-check-input mr-2" name="conditions">
                                <span class="text-muted">
                                    {{ $conditions }}
                                </span>
                                @include('components.alerts.single-error')
                            </div>
                            <div class="col-md-6">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Please confirm transaction.')" class="btn btn-success form-control rounded-0">
                                        Pay
                                        <i class="bi bi-credit-card-2-front float-end mt-1"> </i>
                                    </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    function test()
    {
        alert('aw')
    }
</script>
