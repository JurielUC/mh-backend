<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\HouseType;

use App\Http\Resources\HouseTypeResource;
use App\Http\Resources\HouseTypesResource;

class HouseTypesController extends Controller
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

        $house_type = HouseType::where($where)->orderBy('id', $order)->paginate(50);

        return new HouseTypesResource($house_type);
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
        $rules = [];

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

            $house_type = new HouseType($_input);
            $house_type->code = $house_type->generate_code();

            $house_type->save();

            DB::commit();

            $house_type_resource = new HouseTypeResource($house_type);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $house_type->id,
                    'house_type' => $house_type_resource
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
        $house_type = HouseType::where($where)->first();

        if ($house_type) {
            return new HouseTypeResource($house_type);
        } else {
            $errors = [
                'House Type does not exist!'
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
            $house_type = HouseType::where($where)->first();

            if ($house_type) {
                $house_type->fill($_input);
                $house_type->save();

                DB::commit();

                $house_type_resource = new HouseTypeResource($house_type);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $house_type->id,
                        'house_type' => $house_type_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'House type does not exists'
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
        $house_type = HouseType::where($where)->first();

        if ($house_type) {
            $house_type->deprecated = 1;
            $house_type->save();

            $house_type_resource = new HouseTypeResource($house_type);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $house_type->id,
                    'house_type' => $house_type_resource
                ]
			];
        } else {
            $errors = [
                'House type does not exist'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }
}
