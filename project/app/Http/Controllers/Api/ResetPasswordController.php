<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;

use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
      /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use ResetsPasswords;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }    
     /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $user = null;
        $token = request()->get('token', null);
        if ($token != null)
        {
            $user = User::where('reset_verification_token', '=', $token)->first();
            if ($user != null)
            {
                $request->merge(['email' => $user->email]);
            }
            else
            {
               $response = [
                    'errors' => 'Token has expired.'
                ];
                return response()->json($response, 400); 
            }
        }
        $input = $request->all();

        $validator = Validator::make($input, $this->rules(), $this->validationErrorMessages());
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($response == Password::PASSWORD_RESET) {
            if ($user != null)
            {
                $user->reset_verification_token = null;
                $user->save();
            }
            return response()->json([
                'res' => 0
            ] , 200);
        }
        else
        {
            $response = [
                'errors' => $this->sendResetFailedResponse($request, $response)
            ];
            return response()->json($response, 400);
            // return $response == Password::RESET_LINK_SENT
            // ? $this->sendResetLinkResponse($response)
            // : $this->sendResetLinkFailedResponse($request, $response);
        }
    }



}