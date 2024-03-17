<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use Validator;

use App\Models\User;
use App\Models\Notification;

use App\Http\Resources\NotificationsResource;
use App\Http\Resources\NotificationResource;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		$where = [];
	
		if ($request->input('user_id') AND $request->input('user_id') != null AND $request->input('user_id') != '') {
			$user = User::where('id', $request->input('user_id'))->first();
			
			if ($user) {
				$notifications = collect(); // Initialize an empty collection
				foreach ($user->notifications as $notification) {
					$data = $notification->data;
					
					$unreadonly = 0;
					if ($request->input('status') AND $request->input('status') != null AND $request->input('status') != '') {
						if ($request->input('status') == 'Unread') {
							$unreadonly = 1;
						}
					}
					
					if ($unreadonly == 0 || ($unreadonly == 1 && $notification->read_at == '')) {
						$notifications->push([
							'id' => $notification->id,
							'user_id' => $notification->notifiable_id,
							'url' => $data['url'],
							'subject' => $data['subject'],
							'message' => $data['message'],
							'read_at' => $notification->read_at,
							'created_at' => $notification->created_at,
						]);
					}
				}
				
				// Now you can sort the collection by created_at
				$sorted_notifications = $notifications->sortByDesc('created_at')->values()->all();
				
				$data = [
					'data' => $sorted_notifications,
				];
			} else {
				$data = [
					'data' => []
				];
			}
		} else {
			$data = [
				'data' => []
			];
		}
		
		return response()->json($data);
	}	

	public function record_count(Request $request) // ADMIN //
	{
		$where = [];
		
		if ($request->input('user_id') AND $request->input('user_id') != null AND $request->input('user_id') != '') {
			$user = User::where('id', $request->input('user_id'))->first();
			
			if ($user) {
				$count_all = $user->notifications->count();
				$count_unread = $user->unreadNotifications->count();
				
				$data = [
					'status' => 'Success',
					'data' => [
						'count_all' => $count_all,
						'count_unread' => $count_unread
					]
				];
			} else {
				$errors = [
					'User does not exist!'
				];				
				
				$data = [
					'status' => 'Fail',
					'errors' => $errors
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

		return response()->json($data);
	}
   
	public function read(Request $request, $id)
	{
        $rules = [
			'user_id' => 'required|exists:users,id'
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
			
			$user = User::where('id', $_input['user_id'])->first();
			
			if ($user) {
				$record = $user->unreadNotifications->where('id', $id)->first();

				if ($record) {
					$record->markAsRead();

					DB::commit();

					$data = [
						'status' => 'Success',
						'data' => [
							'id' => $record->id
						]
					];
				} else {
					DB::rollback();
					
					$errors = [
						'Notification does not exist!'
					];				
					
					$data = [
						'status' => 'Fail',
						'errors' => $errors
					];
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