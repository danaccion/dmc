@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row card col-md-12 shadow-sm border-0">
    <div class="card-body">
        <form action="" method="GET">
            <div style="float: right;"> 
                <input type="text" name="search" placeholder="Search">
                <button type="submit">Search</button>
            </div>
        </form>
        <?php echo $cif_table; ?>
    </div>

    </div>
</div>

@endsection
