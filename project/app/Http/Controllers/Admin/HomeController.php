<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Image;
use App\Setting;
use App\EmailTemplate;

use Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $admin_user = Auth::guard('admins')->user();
        // dd($admin_user);
        //return view('admin.home');
        return view('admin.dashboard');
    }

    /**
     * General site settings.
     */
    public function settings()
    {
        $settings = Setting::find(1);
        $admin_user = Auth::guard('admins')->user();
        return view('admin.setting', compact('settings', 'admin_user'));
    }

    public function updateSettings(Request $request)
    {
        $this->validate($request, [
            'admin_email' => 'required|email|max:255',
            'site_title' => 'required|max:255',
            'contact_email' => 'required|email',
            'contact_name' => 'required|max:255',
            // 'contact_phone' => 'required',
            'site_logo' => 'mimes:jpg,jpeg,png,bmp|max:10000',
        ],
            [
                'site_logo.mimes' => 'Please upload site logo of type jpg,jpeg,png,bmp',
                'site_logo.max' => 'Maximum of 10 MB is allowed for site logo'
            ]);
        $input = $request->all();
        $admin_user = Auth::guard('admins')->user();
        $admin_user->email = $input['admin_email'];
        if($input['admin_pass'] != '')
        {
            $admin_user->password = bcrypt($input['admin_pass']);
        }
        $admin_user->save();

        $time = time();
        $settings = Setting::find(1);
        $settings->fill($input);

        /* Logo Image upload */
        if($request->hasFile('site_logo')){
            $old_image = 'uploads/site_logo/'.$settings->site_logo;
            \File::delete($old_image);

            $path   = public_path().'/uploads/site_logo/';
            $image  = $request->file('site_logo');
            $save_name = $time.str_random(10).'.'.$image->getClientOriginalExtension();
            Image::make($image->getRealPath())->save($path . $save_name, 100);
            $settings->site_logo = $save_name;

        }

        $settings->save();
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function viewConfirmationEmailTemplate()
    {
        $email_template = EmailTemplate::find(1);
        return view('admin.email_templates.activation', compact('email_template'));
    }

    public function updateConfirmationEmailTemplate(Request $request)
    {
        //dd($request->all());
        $data = $request->all();
        $rules = [
            'email_subject' => 'required',
            'email_content' => 'required'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            //return $validator->errors();
            return redirect()->back()->with('errors', $validator->errors());
        }

        EmailTemplate::updateOrCreate(
            ['id' => 1],
            [   'email_content' => $data['email_content'], 
                'email_subject' => $data['email_subject']
            ]
        );
        

        return redirect()->back()->with('success', 'Email template updated successfully.');
    }

    public function viewResetEmailTemplate()
    {
        $email_template = EmailTemplate::find(2);
        return view('admin.email_templates.reset', compact('email_template'));
    }

    public function updateResetEmailTemplate(Request $request)
    {
        //dd($request->all());
        $data = $request->all();
        $rules = [
            'email_subject' => 'required',
            'email_content' => 'required'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            //return $validator->errors();
            return redirect()->back()->with('errors', $validator->errors());
        }

        EmailTemplate::updateOrCreate(
            ['id' => 2],
            [   'email_content' => $data['email_content'], 
                'email_subject' => $data['email_subject']
            ]
        );
        

        return redirect()->back()->with('success', 'Email template updated successfully.');
    }

    public function viewWelcomeEmailTemplate()
    {
        $email_template = EmailTemplate::find(3);
        return view('admin.email_templates.welcome_mail', compact('email_template'));
    }

    public function updateWelcomeEmailTemplate(Request $request)
    {
        //dd($request->all());
        $data = $request->all();
        $rules = [
            'email_subject' => 'required',
            'email_content' => 'required'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            //return $validator->errors();
            return redirect()->back()->with('errors', $validator->errors());
        }

        EmailTemplate::updateOrCreate(
            ['id' => 3],
            [   'email_content' => $data['email_content'], 
                'email_subject' => $data['email_subject']
            ]
        );
        

        return redirect()->back()->with('success', 'Email template updated successfully.');
    }
}
