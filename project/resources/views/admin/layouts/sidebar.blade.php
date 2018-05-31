<section class="sidebar">
	<!-- Sidebar user panel -->
	{{--<div class="user-panel">
		<div class="pull-left image">
		 @php
                    $site_logo = asset('uploads/site_logo')."/".get_general_settings('site_logo');
                    @endphp
                        <img src="{{ $site_logo }}" class="img-circle" alt="User Image"/>
			<!-- <img src="{!! asset('assets/admin/dist/img/avatar5.png') !!}" class="img-circle" alt="User Image"> -->
		</div>
		<div class="pull-left info">
			<p>Admin</p>
			<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
		</div>
	</div>--}}
	<ul class="sidebar-menu">
		<li class="header">MAIN NAVIGATION</li>
		<li>
			<a href="{!! admin_url() !!}">
				<i class="fa fa-dashboard"></i> <span>Dashboard</span>
			</a>
		</li>

		<li class="{!! (Request::segment(2) == 'settings')?'treeview active':'treeview' !!}">
			<a href="#">
				<i class="fa fa-cog"></i>
				<span>Manage Site Settings</span>
				<i class="fa fa-angle-left pull-right"></i>
			</a>
			<ul class="treeview-menu">
				{{--<li class="{!! Request::segment(2) == 'settings' && Request::segment(3) == ''?'active':'' !!}">
					<a href="{!! admin_url('settings') !!}">
						<i class="fa fa-cog"></i> <span>Settings</span>
					</a>
				</li>--}}
				<li class="{!! Request::segment(2) == 'confirmation-email-template' && Request::segment(3) == ''?'active':'' !!}">
					<a href="{!! admin_url('confirmation-email-template') !!}">
						<i class="fa fa-envelope-square"></i> <span>Account Activation Mail</span>
					</a>
				</li>
				<li class="{!! Request::segment(2) == 'reset-email-template' && Request::segment(3) == ''?'active':'' !!}">
					<a href="{!! admin_url('reset-email-template') !!}">
						<i class="fa fa-envelope-square"></i> <span>Reset Password Mail</span>
					</a>
				</li>

				<li class="{!! Request::segment(2) == 'welcome-email-template' && Request::segment(3) == ''?'active':'' !!}">
					<a href="{!! admin_url('welcome-email-template') !!}">
						<i class="fa fa-envelope-square"></i> <span>Welcome Mail</span>
					</a>
				</li>

			</ul>
		</li>

	</ul>
</section>
