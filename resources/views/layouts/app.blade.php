<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo.svg') }}" type="image/x-icon">
    <title>
        @yield('title', 'DMC PAY')
    </title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />

<!-- JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" integrity="sha512-FazN8lL+0QhR7OeDOEq+eM9dlbN2g9p2P4uSnmBmVOvKZixUTkR5iJUN57Sht8GrZ41p0wcpkDp+4YutOJhqtw==" crossorigin="anonymous"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
<style>
  #view{
    background-color:#C48B36;
    border-color: #C48B36;
  }
 .form-control:focus {
  box-shadow: 0 0 0 0.1rem #C48B36;
  border-color: #C48B36;
  }

  .form-check-input[type="radio"]:checked {
  border-color: #C48B36; /* Change the border color when the radio button is checked */
  background-color: #C48B36; /* Change the background color when the radio button is checked */
  box-shadow: 0 0 0 0.1rem #C48B36; 
  }

  .form-check-input[type="radio"]:focus{
  border-color: #C48B36; /* Change the border color when the radio button is checked */
  background-color: #C48B36; /* Change the background color when the radio button is checked */
  box-shadow: 0 0 0 0.1rem #C48B36; 
  }

  .form-check-input[type="checkbox"]:checked {
  border-color: #C48B36; /* Change the border color when the radio button is checked */
  background-color: #C48B36; /* Change the background color when the radio button is checked */
  box-shadow: 0 0 0 0.1rem #C48B36; 
  }

  .form-check-input[type="checkbox"]:focus{
  border-color: #C48B36; /* Change the border color when the radio button is checked */
  background-color: #C48B36; /* Change the background color when the radio button is checked */
  box-shadow: 0 0 0 0.1rem #C48B36; 
  }


</style>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm"
        style="background: linear-gradient(90deg, rgba(50,82,58,1) 0%, rgba(39,104,55,1) 35%, rgba(62,138,81,1) 100%);">
            <div class="container">
                <!--<a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'DMC') }}
                </a>-->
                <img src="{{ asset('img/logo.svg') }}" alt="description of myimage">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                               <!-- <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li> -->
                            @endif

                            @if (Route::has('register'))
                               <!-- <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li> -->
                            @endif
                        @else
                        @if (Auth::user()->is_admin)
                        <li class="nav-item">
                                    <a class="nav-link" href="/admin"><k style="color:white;">Home</k></a>
                           </li>
                           <li class="nav-item">
                                    <a class="nav-link" href="/client/list/table"><k style="color:white;">History</k></a>
                           </li>
                            <!--<li class="nav-item">
                                <a class="nav-link" href="/history"><k style="color:white;">QuickPay Logs</k></a>
                            </li>-->
                        @endif

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle  text text-light" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>

 <script>
      $(document).ready(function() {
        // Call API to get list of clients
        $.ajax({
          url: "https://example.com/api/clients",
          method: "GET",
          success: function(response) {
            // Loop through the clients and add them to the table
            $.each(response, function(index, client) {
              var newRow = $("<tr>");
              newRow.append($("<td>").text(client.name));
              newRow.append($('<td>').html('<button class="btn btn-danger btn-sm" data-client-id="' + client.id + '"><i class="fas fa-trash-alt"></i></button>'));
              $('#clients tbody').append(newRow);
            });
          },
          error: function(error) {
            console.log(error);
          }
        });
      });
    </script>

<script>
 $(document).ready(function() {

    $("#print").on("click", function(){
        $(".alert").hide();
        $("#print").hide();
        $("nav.navbar").hide();
        window.print();
    });

    $("#downloadreceipt").on("click", function(){
        $(".alert").hide();
        $("#downloadreceipt").hide();
        $("nav.navbar").hide();
        window.download();
    });


    $('.search').on('keyup', function() {
    var query = $(this).val();
    $.ajax({
        url: '/clientssearch',
        method: 'GET',
        data: {query: query},
        // Assuming that `response` is an array of objects with `name` and `pay_no` properties
        success: function(response) {
            // Clear the table body before appending new rows
            $('#clients tbody').empty();
            
            // Loop through each object in the response and create a new table row for it
                var currentPage = 1;
                var rowsPerPage = 10;
                var totalRows = response.length;

                function displayTable() {
                // Calculate the starting and ending row indexes for the current page
                var startIndex = (currentPage - 1) * rowsPerPage;
                var endIndex = startIndex + rowsPerPage;
                if (endIndex > totalRows) {
                    endIndex = totalRows;
                }

                // Clear the existing table rows
                $('#clients tbody').empty();

                // Loop through the clients for the current page and add a new table row for each one
                for (var i = startIndex; i < endIndex; i++) {
                    var client = response[i];
                    var newRow = $('<tr id="'+client.id+'">');
                    newRow.append($('<td>').text(client.name));
                    newRow.append($('<td>').html('<button id="delete" class="btn btn-danger btn-sm" data-client-id="'+client.id+'"><i class="fas fa-trash-alt"></i></button>'));
                    $('#clients tbody').append(newRow);
                }

                 // Update the pagination links
                var totalPages = Math.ceil(totalRows / rowsPerPage);
                var paginationHtml = '';
                if (totalPages > 1) {
                    var startPage = Math.max(1, currentPage - 2);
                    var endPage = Math.min(totalPages, startPage + 4);
                    if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                    }

                    paginationHtml += '<ul class="pagination justify-content-left">';
                    if (currentPage > 1) {
                    paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Prev</a></li>';
                    }

                    for (var i = startPage; i <= endPage; i++) {
                    var activeClass = '';
                    if (i === currentPage) {
                        activeClass = ' active';
                    }
                    paginationHtml += '<li class="page-item' + activeClass + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                    }

                    if (currentPage < totalPages) {
                    paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>';
                    }
                    paginationHtml += '</ul>';
                }
                $('#pagination').html(paginationHtml);
                }

                // Handle page link clicks
                $('#pagination').on('click', 'a.page-link', function(event) {
                event.preventDefault();
                currentPage = $(this).data('page');
                displayTable();
                });

                // Initial table display
                displayTable();

                            
                            
    
                        }
                    });
                });



    
     $(".view").on("click", function(){
        var viewId = $(this).val();
        $.ajax({
            url:"{{ route('/getInvoice') }}",
            type: 'GET',
            dataType: 'json',
            data:{
                "_token": "{{ csrf_token() }}",
                viewId : viewId,
            },
            success: function(data) {
               console.log(data);
               window.open("../../pdf/" + data, "_blank");
            },
            error: function(response) {
                    alert('error');
            },
        });
    });

   
});


$(document).on('click', '#delete', function() {
    var clientId = $(this).data('client-id');
    if (confirm('Are you sure you want to delete this client?')) {
        $.ajax({
            url: '/remove',
            method: 'POST',
            data: {
                id: clientId,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                console.log(data.success);
                if(data.success){
                    $('#' + clientId).remove();
                    window.alert('Client successfully deleted!');
                }
                else{
                    console.log("Failed to Delete Client");
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                // Display an error message
            }
        });
    }
});

</script>