<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\User;
use App\Models\Bill;

use App\Http\Resources\BillResource;
use App\Http\Resources\BillsResource;

class BillsController extends Controller
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

        $bills = Bill::where($where);

		if ($request->input('search') AND $request->input('search') != null AND $request->input('search') != '') {

			$search = $request->input('search');
	
			$bills = $bills->where('deprecated', 0)->where('name', 'LIKE', '%'.$search.'%')
			->orWhere('price', 'LIKE', '%'.$search.'%')
            ->orWhere('due', 'LIKE', '%'.$search.'%');
		}

        $bills = $bills->orderBy('id', $order)->paginate(10);

        return new BillsResource($bills);
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
            'admin_id' => 'required'
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

            $admin_where = [
                ['deprecated', '=', 0],
                ['id', '=', $_input['admin_id']]
            ];
            $admin = User::where($admin_where)->first();

            $bill = new Bill($_input);
            $bill->code = $bill->generate_code();
            $bill->status = 'Unpaid';

            $bill->user()->associate($user);
            $bill->admin()->associate($admin);

            $bill->save();

            DB::commit();

            $bill_resource = new BillResource($bill);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $bill->id,
                    'bill' => $bill_resource
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
        $bill = Bill::where($where)->first();

        if ($bill) {
            return new BillResource($bill);
        } else {
            $errors = [
                'Bill does not exist!'
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
            $bill = Bill::where($where)->first();

            if ($bill) {
                if (isset($_input['user_id'])) {
                    $user_where = [
                        ['deprecated', '=', 0],
                        ['id', '=', $_input['user_id']]
                    ];
                    $user = User::where($user_where)->first();

                    $bill->user()->associate($user);
                }

                $bill->fill($_input);
                $bill->save();

                DB::commit();

                $bill_resource = new BillResource($bill);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $bill->id,
                        'bill' => $bill_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'Bill does not exists'
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
        $bill = Bill::where($where)->first();

        if ($bill) {
            $bill->deprecated = 1;
            $bill->save();

            $bill_resource = new BillResource($bill);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $bill->id,
                    'bill' => $bill_resource
                ]
			];
        } else {
            $errors = [
                'Bill does not exist'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }
}
