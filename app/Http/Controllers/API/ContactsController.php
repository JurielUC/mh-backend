<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\Contact;

use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactsResource;

class ContactsController extends Controller
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

        if ($request->input('type') AND $request->input('type') != null AND $request->input('type')!='') {
            $where[] = [
                'type', '=', $request->input('type')
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

        $contacts = Contact::where($where);

		if ($request->input('search') AND $request->input('search') != null AND $request->input('search') != '') {

			$search = $request->input('search');
	
			$contacts = $contacts->where('name', 'LIKE', '%'.$search.'%')
            ->orWhere('facebook', 'LIKE', '%'.$search.'%')
			->orWhere('email', 'LIKE', '%'.$search.'%') 
			->orWhere('phone', 'LIKE', '%'.$search.'%');
		}

        $contacts = $contacts->orderBy('id', $order)->paginate(10);

        return new ContactsResource($contacts);
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

            $contact = new Contact($_input);
            $contact->code = $contact->generate_code();

            $contact->save();

            DB::commit();

            $contact_resource = new ContactResource($contact);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $contact->id,
                    'contact' => $contact_resource
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
        $contact = Contact::where($where)->first();

        if ($contact) {
            return new ContactResource($contact);
        } else {
            $errors = [
                'Contact does not exist!'
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
            $contact = Contact::where($where)->first();

            if ($contact) {
                $contact->fill($_input);
                $contact->save();

                DB::commit();

                $contact_resource = new ContactResource($contact);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $contact->id,
                        'contact' => $contact_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'Contact does not exists'
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
        $contact = Contact::where($where)->first();

        if ($contact) {
            $contact->deprecated = 1;
            $contact->save();

            $contact_resource = new ContactResource($contact);

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $contact->id,
                    'contact' => $contact_resource
                ]
			];
        } else {
            $errors = [
                'Contact does not exist'
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
			$directory = 'contact';
			$extension = strtolower($file->getClientOriginalExtension());
			$filename = 'CT-' . rand(1000, 9999) . '-' . time() . '.png';

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
