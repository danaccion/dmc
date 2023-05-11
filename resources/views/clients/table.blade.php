@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row card col-md-12 shadow-sm border-0">
        <div class="card-body">
        <form action="" method="GET">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
                <?php echo $cif_table; ?>
        </div>
    </div>
</div>

@endsection
