<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\User;

use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where = [
            ['deprecated', '=', 0]
        ];

        if ($request->input('status') AND $request->input('status') != null AND $request->input('status')!='') {
            $where[] = [
                'status', '=', $request->input('status')
            ];
        }

        $order = 'desc';
        if ($request->input('order') AND $request->input('order') != null AND $request->input('order') != '') {
            if ($request->input('order')== 'asc' OR $request->input('order')=='desc') {
                $order = $request->input('order');
            }
        }

        $page = 1;
		if ($request->input('page') AND $request->input('page') != null AND $request->input('page') != '') {
			$page = $request->input('page');
		}

        $users = User::where($where);

		if ($request->input('search') AND $request->input('search') != null AND $request->input('search') != '') {

			$search = $request->input('search');
	
			$users = $users->where('first_name', 'LIKE', '%'.$search.'%')
			->orWhere('last_name', 'LIKE', '%'.$search.'%')
            ->orWhere('middle_name', 'LIKE', '%'.$search.'%')
            ->orWhere('gender', 'LIKE', '%'.$search.'%')
			->orWhere('email', 'LIKE', '%'.$search.'%') 
			->orWhere('phone', 'LIKE', '%'.$search.'%');
		}

        $users = $users->orderBy('id', $order)->paginate(10);

        return new UsersResource($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'password' => 'required',
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
        ];

        $_input = $request->input();

        $validator = Validator::make($_input, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            $data = [
                'status' => 'fail',
                'errors' => $errors
            ];
        } else {
            DB::beginTransaction();

            $user = new User($_input);
            $user->status = "Active";
            $user->role = 'Admin';

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

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $where = [
            ['deprecated', '=', 0],
            ['id', '=', $id]
        ];
        $user = User::where($where)->first();

        if ($user) {
            return new UserResource($user);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [];

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
                ['id', '=', $id],
            ];
            $user = User::where($where)->first();

            if ($user) {
                $user->fill($_input);
                $user->status = 'Active';

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
            } else {
                DB::rollback();

                $errors = [
                    'User does not exists'
                ];

                $data = [
                    'status' => 'Fail',
                    'errors' => $errors
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $where = [
            ['deprecated', '=', 0],
            ['id', '=', $id]
        ];
        $user = User::where($where)->first();

        if ($user) {
            $user->deprecated = 1;
            $user->save();

            $user_resource = new UserResource($user);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $user->id,
                    'user' => $user_resource
                ]
			];
        } else {
            $errors = [
                'User does not exist'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }
}