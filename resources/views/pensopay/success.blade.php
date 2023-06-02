@extends('layouts.app')

@section('content')
<div class="container">
    <?php
        echo ($status == 'Un-Paid' ? 
        '<div class="alert alert-success" role="alert">
            Your Payment '. ($status == 'Un-Paid' ? 'is' : 'Has Been') . ' ' . $status .'
        </div>'
        : $cif_table);
    ?>
</div>
@stop