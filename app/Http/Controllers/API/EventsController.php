<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\User;
use App\Models\Event;
use App\Models\Facility;

use App\Http\Resources\EventResource;
use App\Http\Resources\EventsResource;

use App\Notifications\AdminPostNewEventNotification;

class EventsController extends Controller
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

        $events = Event::where($where);

		if ($request->input('search') AND $request->input('search') != null AND $request->input('search') != '') {

			$search = $request->input('search');
	
			$events = $events->where('deprecated', 0)
                ->where(function($query) use ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%')
                          ->orWhere('date', 'LIKE', '%' . $search . '%')
                          ->orWhere('description', 'LIKE', '%' . $search . '%');
                });

		}

        $events = $events->orderBy('id', $order)->paginate(10);

        return new EventsResource($events);
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

            $facility_where = [
                ['deprecated', '=', 0],
                ['id', '=', $_input['facility_id']]
            ];
            $facility = Facility::where($facility_where)->first();

            $event = new Event($_input);
            $event->code = $event->generate_code();
            $event->status = 'Active';

            $event->facility()->associate($facility);
            $event->user()->associate($user);

            $event->save();

            DB::commit();

            $event_resource = new EventResource($event);

            $users_where = [
                ['deprecated', '=', 0],
                ['role', '=', 'User']
            ];
            $users = User::where($users_where)->get();

            if ($users) {
                foreach ($users AS $user) {
                    $_data = [
                        'user' => [
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name
                        ],
                        'event' => $event,
                        'facility' => [
                            'name' => $facility->name
                        ]
                    ];

                    $user->notify(new AdminPostNewEventNotification($_data));
                }
            }

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $event->id,
                    'event' => $event_resource
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
        $event = Event::where($where)->first();

        if ($event) {
            return new EventResource($event);
        } else {
            $errors = [
                'Event does not exist!'
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
            $event = Event::where($where)->first();

            if ($event) {
                $event->fill($_input);
                $event->save();

                DB::commit();

                $event_resource = new EventResource($event);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $event->id,
                        'event' => $event_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'Event does not exists'
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
        $event = Event::where($where)->first();

        if ($event) {
            $event->deprecated = 1;
            $event->save();

            $event_resource = new EventResource($event);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $event->id,
                    'event' => $event_resource
                ]
			];
        } else {
            $errors = [
                'Event does not exist'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }
}
