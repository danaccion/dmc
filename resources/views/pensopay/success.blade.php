@extends('layouts.app')

@section('content')
<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $(document).on('click', '.download', function() {
    //var url = 'https://web.facebook.com/';
   // window.location.href = url;
   var anchor = document.createElement('a');
anchor.href = data.downloadUrl;
anchor.target = '_blank';
anchor.download = data.fileName;
anchor.click();
});
</script>

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