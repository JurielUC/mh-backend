<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use Validator;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;

use App\Http\Resources\UsersResource;
use App\Http\Resources\UserResource;

class PasswordsController extends Controller
{
	public function forgot(Request $request)
	{
		$credentials = $request->validate(['email' => 'required|email']);

		Password::sendResetLink($credentials);
	
		$data = [
			'status' => 'Success',
			'data' => []
		];
	
		return response()->json($data);
	}

	public function change(Request $request) // USERS //
    {
        $rules = [
			'user_id' => 'required',
			'token' => 'required',

			'current_password' => 'required',
			'new_password' => 'required',
			'confirm_password' => 'required'
		];

        $_input = $request->input();

        $validator = Validator::make($_input, $rules);

        if ($validator->fails()) {
			$errors = $validator->errors()->toArray();

			$data = [
				'status' => 'Fail',
				'errors' => $errors
			];
        } else {
            DB::beginTransaction();

			$where = [
				['deprecated', '=', 0],
				['id', '=', $_input['user_id']],
				['remember_token', '=', $_input['token']]
			];
			$user = User::where($where)->first();
			
			if ($user) {
				if ($_input['new_password'] != $_input['confirm_password']) {
					DB::rollback();
					
					$errors = [
						'Passwords do not match!'
					];				
					
					$data = [
						'status' => 'Fail',
						'errors' => $errors
					];
				} else {
					if (!Hash::check($_input['current_password'], $user->password)) {
						DB::rollback();
						
						$errors = [
							'Current password is incorrect!'
						];				
						
						$data = [
							'status' => 'Fail',
							'errors' => $errors
						];
					} else {
						$user->password = Hash::make($_input['new_password']);
						$user->save();
						
						DB::commit();
						
						$user_resource = new UserResource($user);

						$data = [
							'status' => 'Success',
							'data' => [
								'id' => $user->id,
								'user' => $user_resource
							]
						];
					}
				}
			} else {
				DB::rollback();
				
				$errors = [
					'User does not exist!'
				];				
				
				$data = [
					'status' => 'Fail',
					'errors' => $errors
				];
			}
        }
		
		return response()->json($data);
    }
}