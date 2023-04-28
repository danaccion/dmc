@if (session('single-error'))
    <div class="alert alert-danger" role="alert" >
        <span class="pe-2">
            <i class="bi bi-info-circle"></i>
        </span>
        <span class="flex-fill">
            {{ session('single-error') }}
        </span>
    </div>
@endif
