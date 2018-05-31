
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Emails</title>
</head>
	
<body style="background-color:#eeeeee; padding:0; margin:0;">
	<table style="width:510px; max-width:100%; margin:0 auto; font-family:Arial; margin:0 auto; font-size:16px; color:#29353b; background-color:#fff; border:1px solid #d6d6d6;" cellpadding="12" cellspacing="0">
		<tr>
			<td style="text-align:center; vertical-align:top; padding:24px 16px 24px 16px; background-color:#ffffff; border-bottom:1px solid #d6d6d6;">
				<a href="{{ env('SITE_URL') }}" target="_blank"><img src="{{asset('uploads/site_logo/1522220330DciOShKwTJ.png')}}" alt="" style="display:inline-block; height:38px; width:auto;"/></a>
			</td>
		</tr>
		

		 @yield('content')	
		<tr>
			<td height="10px"></td>
		</tr>
		<tr>
			<td style="padding:0;">
				<table style="width:100%;padding:0; margin:0;" cellpadding="12" cellspacing="0">
					<tr>
						<td style="text-align:center; font-size:12px; line-height:20px; vertical-align:top; padding-top:0; color:#727e84; padding:22px 16px; background-color:#ffffff; border-top:1px solid #d6d6d6;"><a href="{{ env('SITE_URL') }}" target="_blank">{{ env('APP_NAME') }}</a> &copy;All rights reserved</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>