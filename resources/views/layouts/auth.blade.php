<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
</head>

<body class="page-header-fixed">

    <div sdf="SF" style="margin-top: 5%;margin-bottom:2%;text-align: center;">
        <img src="{{url('adminlte/img/LOGOF.png')}}"
                                 alt="Jumbo-Warranty" />

    </div>

    <div class="container-fluid">
        @yield('content')
    </div>

    <div class="scroll-to-top"
         style="display: none;">
        <i class="fa fa-arrow-up"></i>
    </div>

    @include('partials.javascripts')

</body>
</html>