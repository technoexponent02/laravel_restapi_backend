@extends('admin.layouts.app')

@section('pageTitle', 'Dashboard')

@section('customStyles')
    {{-- You can write styles or other scripts here, it will be added to <head> --}}
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0);"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">

        <!-- Welcome to admin panel -->
        <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">


        </div>




      </div>
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->
@endsection

@section('customScript')
    {{-- You can write scripts here, it will be added before </body> --}}
@endsection
