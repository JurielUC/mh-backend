<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\User;
use App\Models\ActivityLog;

use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\ActivityLogsResource;

class ActivityLogsController extends Controller
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

        $activities = ActivityLog::where($where)->orderBy('id', $order)->paginate(50);

        return new ActivityLogsResource($activities);
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
            'user_id' => 'required',
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

            $user_where = [
                ['deprecated', '=', 0],
                ['id', '=', $_input['user_id']]
            ];
            $user = User::where($user_where)->first();

            $activity = new ActivityLog($_input);
            $activity->code = $activity->generate_code();
            $activity->status = 'Active';

            $activity->user()->associate($user);

            $activity->save();

            DB::commit();

            $activity_resource = new ActiviLogResource($activity);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $activity->id,
                    'activity' => $activity_resource
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
        $activity = ActivityLog::where($where)->first();

        if ($activity) {
            return new ActivityLogResource($activity);
        } else {
            $errors = [
                'Activity log does not exist!'
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
            $activity = ActivityLog::where($where)->first();

            if ($activity) {
                $activity->fill($_input);
                $activity->save();

                DB::commit();

                $activity_resource = new ActivityLogResource($activity);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $activity->id,
                        'activity' => $activity_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'Activity does not exists'
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
        $activity = ActivityLog::where($where)->first();

        if ($activity) {
            $activity->deprecated = 1;
            $activity->save();

            $activity_resource = new ActivityResource($activity);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $activity->id,
                    'activity' => $activity_resource
                ]
			];
        } else {
            $errors = [
                'Activity does not exist'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }
}
