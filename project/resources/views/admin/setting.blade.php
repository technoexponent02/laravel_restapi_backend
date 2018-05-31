@extends('admin.layouts.app')



@section('pageTitle', 'Dashboard')



@section('content')



<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

			Settings

		</h1>

		<ol class="breadcrumb">

			<li><a href="javascript:void(0);"><i class="fa fa-home"></i> Home</a></li>

			<li><a href="{!! admin_url('') !!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>

			<li class="active">Settings</li>

		</ol>

	</section>

	<!-- Main content -->

	<section class="content">

		<div class="row">

			<div class="col-md-12">

				<div class="box box-info">

					<div class="box-header with-border">

						<h3 class="box-title">Update Settings</h3>

					</div>

					@if($errors->any())

						<div class="alert alert-danger">

							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

							@foreach($errors->all() as $error)

								<p>{!! $error !!}</p>

							@endforeach

						</div>

					@endif

					@if(session('success'))

					<div class="alert alert-success">

						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

						{!! session('success') !!}

					</div>

					@endif

					<!-- form start -->

					<form class="form-horizontal" name="settings_form" action="{!! admin_url('settings') !!}" method="post" enctype="multipart/form-data">

						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="box-body">

							<div class="form-group">

								<label for="admin_email" class="col-sm-2 control-label">Admin Email</label>

								<div class="col-sm-6">

									<input type="email" class="form-control" id="admin_email" name="admin_email" value="{!! $admin_user->email !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="firstName" class="col-sm-2 control-label">Admin Password</label>

								<div class="col-sm-6">

									<input type="password" class="form-control" id="admin_pass" name="admin_pass" autocomplete="new-password" />

								</div>

							</div>

							<div class="form-group">

								<label for="firstName" class="col-sm-2 control-label">Site Title</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="site_title" name="site_title" value="{!! $settings->site_title !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="contact_email" class="col-sm-2 control-label">Contact Email</label>

								<div class="col-sm-6">

									<input type="email" class="form-control" id="contact_email" name="contact_email" value="{!! $settings->contact_email !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="contact_name" class="col-sm-2 control-label">Contact Email Name</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="contact_name" name="contact_name" value="{!! $settings->contact_name !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="contact_phone" class="col-sm-2 control-label">Contact Phone</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{!! $settings->contact_phone !!}" />

								</div>

							</div>



							<div class="form-group">

								<label for="site_logo" class="col-sm-2 control-label">Logo Image</label>

								<div class="col-sm-6">

								<span class="btn btn-default btn-file">

									Browse <input type="file"  id="site_logo" name="site_logo" />

								</span>

									<p class="help-block" id="thumb_image_help">Current site logo</p>

									<img class="list_table_img" src="{!! asset('uploads/site_logo/'.$settings->site_logo) !!}" alt="No Logo">

								</div>

							</div>

							<?php /*

							<div class="form-group">

								<label for="facebook_link" class="col-sm-2 control-label">Facebook Link</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="facebook_link" name="facebook_link" value="{!! $settings->site_fb_link !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="twitter_link" class="col-sm-2 control-label">Twitter Link</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="twitter_link" name="twitter_link" value="{!! $settings->site_twitter_link !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="youtube_link" class="col-sm-2 control-label">Youtube Link</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="youtube_link" name="youtube_link" value="{!! $settings->site_youtube_link !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="linkedin_link" class="col-sm-2 control-label">Linkedin Link</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="linkedin_link" name="linkedin_link" value="{!! $settings->site_linkedin_link !!}" />

								</div>

							</div>

							*/?>



						</div><!-- /.box-body -->

						<div class="box-footer">

							<button type="submit" class="btn btn-info pull-right">Save</button>

						</div><!-- /.box-footer -->

					</form>

				</div><!-- /.box -->

			</div>

		</div>

	</section>

	<!-- /.content -->

</div><!-- /.content-wrapper -->



@endsection