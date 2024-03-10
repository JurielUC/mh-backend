<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\User;
use App\Models\LostFound;

use App\Http\Resources\LostFoundResource;
use App\Http\Resources\LostsFoundsResource;

class LostsFoundsController extends Controller
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

        $lost_founds = LostFound::where($where);

		if ($request->input('search') AND $request->input('search') != null AND $request->input('search') != '') {

			$search = $request->input('search');
	
			$lost_founds = $lost_founds->where('item_name', 'LIKE', '%'.$search.'%')
			->orWhere('type', 'LIKE', '%'.$search.'%')
            ->orWhere('location', 'LIKE', '%'.$search.'%')
            ->orWhere('finder_name', 'LIKE', '%'.$search.'%');
		}

        $lost_founds = $lost_founds->orderBy('id', $order)->paginate(10);

        return new LostsFoundsResource($lost_founds);
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

            $lost_found = new LostFound($_input);
            $lost_found->code = $lost_found->generate_code();

            if (isset($_input['image_urls'])) {
                $lost_found->image_urls = json_encode($_input['image_urls']);
            }

            $lost_found->user()->associate($user);

            $lost_found->save();

            DB::commit();

            $lost_found_resource = new LostFoundResource($lost_found);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $lost_found->id,
                    'lost_found' => $lost_found_resource
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
        $lost_found = LostFound::where($where)->first();

        if ($lost_found) {
            return new LostFoundResource($lost_found);
        } else {
            $errors = [
                'Lost and found item does not exist!'
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
            $lost_found = LostFound::where($where)->first();

            if ($lost_found) {
                $lost_found->fill($_input);

                if (isset($_input['image_urls'])) {
                    $lost_found->image_urls = json_encode($_input['image_urls']);
                }

                $lost_found->save();

                DB::commit();

                $lost_found_resource = new LostFoundResource($lost_found);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $lost_found->id,
                        'lost_found' => $lost_found_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'Lost and found item does not exist!'
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
        $lost_found = LostFound::where($where)->first();

        if ($lost_found) {
            $lost_found->deprecated = 1;
            $lost_found->save();

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $lost_found->id,
                ]
			];
        } else {
            $errors = [
                'Lost and found item does not exist!'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }

    public function file_upload(Request $request)
	{
		$rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ];

        $_input = $request->all();

        $validator = Validator::make($_input, $rules);

        if ($validator->fails()) {
			$errors = $validator->errors()->toArray();

			$data = [
				'status' => 'Fail',
				'errors' => $errors
			];
        } else {
			$file = $request->file('image');
			$directory = 'lost';
			$extension = strtolower($file->getClientOriginalExtension());
			$filename = 'LF-' . rand(1000, 9999) . '-' . time() . '.png';

			$response = $file->storeAs($directory, $filename, 'public');
            
			if ($response) {
				$data = [
					'status' => 'Success',
					'data' => [
						'image' => $filename,
					]
				];
			} else {
				$errors = [
					'Error uploading the image!'
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
