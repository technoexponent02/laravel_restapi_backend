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

			<li class="active">Account Activation Mail</li>

		</ol>

	</section>

	<!-- Main content -->

	<section class="content">

		<div class="row">

			<div class="col-md-12">

				<div class="box box-info">

					<div class="box-header with-border">

						<h3 class="box-title">Update Account Activation Mail</h3>

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

					<form class="form-horizontal" name="confirmation-email-template" action="{!! admin_url('confirmation-email-template') !!}" method="post" enctype="multipart/form-data">

						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="box-body">

							<div class="form-group">

								<label for="email_subject" class="col-sm-2 control-label">Email Subject</label>

								<div class="col-sm-6">

									<input type="text" class="form-control" id="email_subject" name="email_subject" value="{!! $email_template->email_subject ?? '' !!}" />

								</div>

							</div>

							<div class="form-group">

								<label for="email_content" class="col-sm-2 control-label">Email Content</label>

								<div class="col-sm-6">

									<textarea id="email_content" name="email_content" rows="10" cols="80">{!! $email_template->email_content ?? '' !!}</textarea>

								</div>

							</div>







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

@section('customScript')
<!-- ckeditor -->
    <script src="{!! asset('assets/admin/ckeditor/ckeditor.js') !!}"></script>
    <script>
	  $(function () {
	    // Replace the <textarea id="editor1"> with a CKEditor
	    // instance, using default configuration.
	    CKEDITOR.replace('email_content');
	  });
</script>
@endsection