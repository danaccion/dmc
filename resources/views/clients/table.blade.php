@extends('layouts.app')

@section('content')

<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $(document).on('click', '.showdetails', function() {
    var id = $(this).data('id');
    var url = '/get-details/' + id;
    window.location.href = url;
});

$(document).on('click', '.receipt', function() {
    var id = $(this).data('id');
    var url = '/success/' + id;
    window.location.href = url;
});

    // Add this JavaScript code to your page or include it in a separate JavaScript file
$(document).on('click', '.delete', function() {
    var id = $(this).data('id');
    var confirmed = confirm("Are you sure you want to delete this item?");
    if (confirmed) {
    var url = '/deletehistory/' + id; // Replace '/delete/' with the actual delete endpoint URL

    $.ajax({
        url: url,
        type: 'DELETE',
        data: { id: id },
        headers: {
        'X-CSRF-TOKEN': csrfToken
    },
        success: function(response) {
            // Handle the success response
            // You can update the table or perform any other necessary actions
            console.log('Data deleted successfully.');
            location.reload();
        },
        error: function(xhr) {
            // Handle the error response
            console.log('Error deleting data:', xhr.responseText);
        }
    });
}
});
</script>
<div class="container">
    <div class="row card col-md-16 shadow-sm border-0">
    <div class="card-body" style="font-size:15px;">
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
