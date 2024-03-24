<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Show the password reset form.
     *
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.passwords.reset', ['token' => $token, 'email' => $email]);
    }

    /**
     * Handle the password reset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        // Validate the reset request
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $_input = $request->input();

        $user_where = [
            ['deprecated', '=', 0],
            ['email', '=', $_input['email']]
        ];
        $user = User::where($user_where)->first();

        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        // Check the password reset status
        if ($status == Password::PASSWORD_RESET) {
            return redirect('https://mh-subdivision.web.app/')->with('success', 'Password has been reset successfully.');
        } else {
            // Validation errors occurred; return with errors
            return redirect()->back()->with('error', 'Invalid reset request.');
        }
    }
}