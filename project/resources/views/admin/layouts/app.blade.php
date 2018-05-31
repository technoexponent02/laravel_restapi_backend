<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin | @yield('pageTitle')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{!! asset('assets/admin/bootstrap/css/bootstrap.min.css') !!}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{!! asset('assets/admin/font-awesome/css/font-awesome.min.css') !!}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{!! asset('assets/admin/ionicons/css/ionicons.min.css') !!}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{!! asset('assets/admin/dist/css/AdminLTE.min.css') !!}">
    {{--AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load.--}}
    <link rel="stylesheet" href="{!! asset('assets/admin/dist/css/skins/skin-blue.min.css') !!}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{!! asset('assets/admin/plugins/iCheck/flat/blue.css') !!}">

    <!-- Date Picker -->
    <link rel="stylesheet" href="{!! asset('assets/admin/plugins/datepicker/datepicker3.css') !!}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{!! asset('assets/admin/plugins/daterangepicker/daterangepicker-bs3.css') !!}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{!! asset('assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') !!}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{!! asset('assets/admin/plugins/datatables/dataTables.bootstrap.css') !!}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{!! asset('assets/admin/plugins/select2/select2.min.css') !!}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{!! asset('assets/admin/dist/css/style.css') !!}">
    <link rel="stylesheet" href="{!! asset('assets/admin/dist/css/newchat.css') !!}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('customStyles')
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <a href="{!! url('admin/dashboard') !!}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">
                @php
                    $site_logo = asset('uploads/site_logo')."/".get_general_settings('site_logo');
                    @endphp
                    <img src="{{ $site_logo }}" class="img-responsive" alt="User Image" style="margin-top: 10px;"/>
                <!-- <b>A</b>DM --></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">
                <!-- <b>Admin</b> -->
                    @php
                    $site_logo = asset('uploads/site_logo')."/".get_general_settings('site_logo');
                    @endphp
                    <img src="{{ $site_logo }}" class="img-responsive" alt="User Image" style="margin-top: 10px;" />
                </span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            @php
                    $site_logo = asset('uploads/site_logo')."/".get_general_settings('site_logo');
                    @endphp
                        <img src="{{ $site_logo }}" class="user-image" alt="User Image"/>
                                <!-- <img src="{!! asset('assets/admin/dist/img/avatar5.png') !!}" class="user-image" alt="User Image"> -->
                                <span class="hidden-xs">Admin</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                @php
                    $site_logo = asset('uploads/site_logo')."/".get_general_settings('site_logo');
                    @endphp
                        <img src="{{ $site_logo }}" class="img-circle" alt="User Image"/>
                                    <!-- <img src="{!! asset('assets/admin/dist/img/avatar5.png') !!}" class="img-circle" alt="User Image"> -->
                                    <p>Admin</p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="{!! admin_url('logout') !!}" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            @include('admin.layouts.sidebar')
        </aside>

        @yield('content')

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.5.4
            </div>
            <strong>Copyright &copy; {{ date("Y") }}
                <a href="https://www.technoexponent.com">Techno Exponent</a>.
            </strong> All rights reserved.
        </footer>
    </div><!-- ./wrapper -->

    <!-- jQuery 2.1.4 -->
    <script src="{!! asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js') !!}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <!-- ckeditor -->
    <script src="{!! asset('assets/admin/ckeditor/ckeditor.js') !!}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{!! asset('assets/admin/bootstrap/js/bootstrap.min.js') !!}"></script>


    <!-- Slimscroll -->
    <script src="{!! asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js') !!}"></script>

    <script src="{!! asset('assets/admin/dist/js/app.min.js') !!}"></script>

    <script src="{!! asset('assets/admin/dist/js/demo.js') !!}"></script>

    <!-- DataTables -->
    <script src="{!! asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') !!}"></script>
    <script src="{!! asset('assets/admin/plugins/datatables/dataTables.bootstrap.min.js') !!}"></script>

    <!-- Select2 -->
    <script src="{!! asset('assets/admin/plugins/select2/select2.min.js') !!}"></script>

    <!-- FastClick -->
    <script src="{!! asset('assets/admin/plugins/fastclick/fastclick.min.js') !!}"></script>
    
<!--     <script src="{!! asset('assets/admin/bootstrap/js/fastselect.standalone.js') !!}"></script> -->

    <script type="text/javascript">
        window.setTimeout(function() {
            $(".alert-success").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);
        $(".select2").select2();
		
		$(document).ready(function () {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
		});
    </script>

    @yield('customScript')

</body>
</html>
