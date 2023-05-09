@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-success" role="alert">
    Your Payment Has Been Successfully Processed.
    </div>
    <?php echo $cif_table ?>
</div>
@stop