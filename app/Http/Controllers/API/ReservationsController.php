<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\User;
use App\Models\Reservation;
use App\Models\Facility;

use App\Http\Resources\ReservationResource;
use App\Http\Resources\ReservationsResource;

class ReservationsController extends Controller
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

        if ($request->input('home_owner_id') AND $request->input('home_owner_id') != null AND $request->input('home_owner_id')!='') {
            $where[] = [
                'user_id', '=', $request->input('home_owner_id')
            ];
        }

        $order = 'asc';
        if ($request->input('order') AND $request->input('order') != null AND $request->input('order') != '') {
            if ($request->input('order')== 'asc' OR $request->input('order')=='desc') {
                $order = $request->input('order');
            }
        }

        $page = 1;
		if ($request->input('page') AND $request->input('page') != null AND $request->input('page') != '') {
			$page = $request->input('page');
		}

        $reservations = Reservation::where($where)->orderBy('id', $order)->paginate(50);

        return new ReservationsResource($reservations);
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
            'user_id' => 'required'
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

            $reservation = new Reservation($_input);
            $reservation->code = $reservation->generate_code();

            $reservation->user()->associate($user);
            $reservation->facility()->associate($facility);

            $reservation->save();

            DB::commit();

            $reservation_resource = new ReservationResource($reservation);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $reservation->id,
                    'reservation' => $reservation_resource
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
        $reservation = Reservation::where($where)->first();

        if ($reservation) {
            return new ReservationResource($reservation);
        } else {
            $errors = [
                'Reservation does not exist!'
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
            $reservation = Reservation::where($where)->first();

            if ($reservation) {
                $reservation->fill($_input);

                if (isset($_input['facility_id'])) {
                    $facility_where = [
                        ['deprecated', '=', 0],
                        ['id', '=', $_input['facility_id']]
                    ];
                    $facility = Facility::where($facility_where)->first();

                    $reservation->facility()->associate($facility);
                }

                $reservation->save();

                DB::commit();

                $reservation_resource = new ReservationResource($reservation);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $reservation->id,
                        'reservation' => $reservation_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'Reservation does not exists'
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
        $reservation = Reservation::where($where)->first();

        if ($reservation) {
            $reservation->deprecated = 1;
            $reservation->save();

            $reservation_resource = new ReservationResource($reservation);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $reservation->id,
                    'reservation' => $reservation_resource
                ]
			];
        } else {
            $errors = [
                'Reservation does not exist'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }
}
