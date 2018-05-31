@extends('layouts.mail_layout')
@section('content')
<tr>
			<td style="text-align:center; font-size:24px; line-height:28px; vertical-align:top; padding:30px 16px 0px 16px;">
				Hi {{ $user->first_name }}!
			</td>
		</tr>
		<tr>
			<td style="text-align:center; font-size:14px; line-height:24px; vertical-align:top;  color:#525252; padding:20px 16px 0 16px;">
				{!! $email_template->email_content !!}
			</td>
		</tr>
		
		
@endsection
