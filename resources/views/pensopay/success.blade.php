@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-success" role="alert">
    Your Payment {{ $status == 'Unpaid' ? 'is' : 'Has Been' }}  {{ $status }}
    </div>
    <?php echo $cif_table ?>
</div>
@stop