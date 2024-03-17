<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use DB;

use App\Models\User;
use App\Models\LostFoundComment;
use App\Models\LostFound;

use App\Http\Resources\LostFoundCommentResource;
use App\Http\Resources\LostFoundCommentsResource;

use App\Notifications\AdminLostFoundCommentNotification;;
use App\Notifications\AdminLostFoundCommentResponseNotification;

class LostFoundCommentsController extends Controller
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

        if ($request->input('item_id') AND $request->input('item_id') != null AND $request->input('item_id')!='') {
            $where[] = [
                'lost_found_id', '=', $request->input('item_id')
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

        $lost_founds = LostFoundComment::where($where)->orderBy('id', $order)->paginate(50);

        return new LostFoundCommentsResource($lost_founds);
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
            'lost_found_id' => 'required'
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
                ['id', '=', $request->query('user_id')]
            ];
            $user = User::where($user_where)->first();

            $lost_found_where = [
                ['deprecated', '=', 0],
                ['id', '=', $_input['lost_found_id']]
            ];
            $lost_found = LostFound::where($lost_found_where)->first();

            $lost_found_comment = new LostFoundComment($_input);
            $lost_found_comment->code = $lost_found_comment->generate_code();
            $lost_found_comment->status = 'Pending';

            $lost_found_comment->user()->associate($user);
            $lost_found_comment->lost_found()->associate($lost_found);

            $lost_found_comment->save();

            DB::commit();

            $lost_found_comment_resource = new LostFoundCommentResource($lost_found_comment);

            $admin_where = [
                ['deprecated', '=', 0],
                ['role', '=', 'Admin']
            ];
            $admins = User::where($admin_where)->get();

            if ($admins) {
                foreach ($admins AS $admin) {
                    $_data = [
                        'lost_found' => $lost_found,
                        'user' => [
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                        ],
                        'admin' => [
                            'first_name' => $admin->first_name
                        ],
                        'comment' => $lost_found_comment
                    ];

                    $admin->notify(new AdminLostFoundCommentNotification($_data));
                }
            }

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $lost_found_comment->id,
                    'lost_found_comment' => $lost_found_comment_resource
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
        $lost_found_comment = LostFoundComment::where($where)->first();

        if ($lost_found_comment) {
            return new LostFoundCommentResource($lost_found_comment);
        } else {
            $errors = [
                'Lost and found comment does not exist!'
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
            $lost_found_comment = LostFoundComment::where($where)->first();

            if ($lost_found_comment) {
                $lost_found_comment->fill($_input);

                $lost_found_comment->save();

                DB::commit();

                $lost_found_comment_resource = new LostFoundCommentResource($lost_found_comment);

                $data = [
                    'status' => 'Success',
                    'data' => [
                        'id' => $lost_found_comment->id,
                        'lost_found_comment' => $lost_found_comment_resource
                    ]
                ];
            } else {
                DB::rollback();

                $errors = [
                    'Lost and found comment does not exist!'
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
        $lost_found_comment = LostFoundComment::where($where)->first();

        if ($lost_found_comment) {
            $lost_found_comment->deprecated = 1;
            $lost_found_comment->save();

            $data = [
                'status' => 'Success',
                'data' => [
                    'id' => $lost_found_comment->id,
                ]
			];
        } else {
            $errors = [
                'Lost and found comment does not exist!'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }

    public function comment_response($id)
    {
        $where = [
            ['deprecated', '=', 0],
            ['id', '=', $id]
        ];
        $comment = LostFoundComment::where($where)->first();

        if ($comment) {
            $comment->status = 'Responded';
            $comment->save();

            $_data = [
                'user' => $comment->user,
                'lost_found' => $comment->lost_found
            ];
            $comment->user->notify(new AdminLostFoundCommentResponseNotification($_data));

            $data = [
                'status' => 'Success',
                'data' => $comment
            ];
        } else {
            $errors = [
                'Comment does not exist!'
            ];

            $data = [
                'status' => 'Fail',
                'errors' => $errors
            ];
        }

        return response()->json($data);
    }
}
