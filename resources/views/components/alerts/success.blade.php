@if (session('success'))
    <div class="alert alert-success" role="alert" >
        <span class="pe-2">
            <i class="bi bi-check2-circle"></i>
        </span>
        <span class="flex-fill">
            {{ session('success') }}
        </span>
    </div>
@endif
