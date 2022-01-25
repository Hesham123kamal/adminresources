<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\RatingPopup;
use Illuminate\Http\Request;

class RatingPopupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.rating_popup.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $rating = RatingPopup::join('users','users.id','=','popup_rating.user_id')
            ->select('popup_rating.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $rating = $rating->where('popup_rating.id', '=', $id);
        }
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $user_id = $data['user_id'];
            $rating = $rating->where('popup_rating.user_id', 'LIKE', "%$user_id%");
        }
        if (isset($data['user_email']) && !empty($data['user_email'])) {
            $user_email = $data['user_email'];
            $rating = $rating->where('users.Email', 'LIKE', "%$user_email%");
        }
        if (isset($data['rating']) && !empty($data['rating'])) {
            $rating_msg = $data['rating'];
            $rating = $rating->where('popup_rating.rating', 'LIKE', "%$rating_msg%");
        }
        if (isset($data['comment']) && !empty($data['comment'])) {
            $comment = $data['comment'];
            $rating = $rating->where('popup_rating.comment', 'LIKE', "%$comment%");
        }
        if (isset($data['page']) && !empty($data['page'])) {
            $page = $data['page'];
            $rating = $rating->where('popup_rating.page', 'LIKE', "%$page%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $rating = $rating->whereBetween('popup_rating.createtime', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
        }

        $iTotalRecords = $rating->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'popup_rating.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'popup_rating.id';
                break;
            case 1:
                $columnName = 'popup_rating.user_id';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'popup_rating.rating';
                break;
            case 4:
                $columnName = 'popup_rating.comment';
                break;
            case 5:
                $columnName = 'popup_rating.page';
                break;
            case 6;
                $columnName = 'popup_rating.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $rating = $rating->where(function ($q) use ($search) {
                $q->where('popup_rating.id', '=', $search)
                    ->orWhere('popup_rating.user_id', 'Like', "%$search%")
                    ->orWhere('popup_rating.page', 'Like', "%$search%")
                    ->orWhere('popup_rating.comment', 'Like', "%$search%")
                    ->orWhere('popup_rating.rating', 'Like', "%$search%")
                    ->orWhere('users.Email', 'Like', "%$search%");

            });
        }

        $rating = $rating->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($rating as $comment) {
            $user_email = $comment->user_email;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $comment->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $comment->id,
                $comment->user_id,
                $user_email,
                $comment->rating . (getUserSystem('backend_lang')=='ar'?' من 5':' out of 5'),
                $comment->comment,
                '<a target="_blank" href="' . $comment->page . '">' . $comment->page . '</a>',
                $comment->createtime,
                ''
            ];
        }
        if (isset($data["customActionType"]) && $data["customActionType"] == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records['postData'] = $data;
        //return response()->json($data)->setCallback($request->input('callback'));
        return response()->json($records)->setCallback($request->input('callback'));

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
