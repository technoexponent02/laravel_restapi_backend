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
		<tr>
			<td height="20px"></td>
		</tr>
		<tr>
			<td style="text-align:center; font-size:24px; line-height:30px; vertical-align:top;  color:#2f2f2f; padding:10px 0;">
				<span style="display:inline-block; padding:0 5px; background-color:#fff;">
					<a href="{{ $reset_url }}" style="text-align:center; display:inline-block; font-size:16px; line-height:30px; vertical-align:top; color:#ffffff; padding:5px 24px 5px 24px; background-color:#22b14c; text-decoration:none; cursor:pointer; border-radius:4px;">Reset Password link</a>
				</span>			
			</td>
		</tr>
@endsection
