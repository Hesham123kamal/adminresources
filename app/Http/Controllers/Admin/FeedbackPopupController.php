<?php

namespace App\Http\Controllers\Admin;

use App\FeedbackPopup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FeedbackPopupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $feedback = FeedbackPopup::with('User')->get();
//        dd($feedback);
        return view('auth.feedback_popup.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $feedback = FeedbackPopup::join('users','users.id','=','feedback_rating.user_id')
        ->select('feedback_rating.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $feedback = $feedback->where('feedback_rating.id', '=', $id);
        }
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $user_id = $data['user_id'];
            $feedback = $feedback->where('feedback_rating.user_id', 'LIKE', "%$user_id%");
        }
        if (isset($data['user_email']) && !empty($data['user_email'])) {
            $user_email = $data['user_email'];
            $feedback = $feedback->where('users.Email', 'LIKE', "%$user_email%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $feedback = $feedback->where('feedback_rating.phone', 'LIKE', "%$phone%");
        }
        if (isset($data['feedback']) && !empty($data['feedback'])) {
            $feedback_msg = $data['feedback'];
            $feedback = $feedback->where('feedback_rating.feedback', 'LIKE', "%$feedback_msg%");
        }
        if (isset($data['page']) && !empty($data['page'])) {
            $page = $data['page'];
            $feedback = $feedback->where('feedback_rating.page', 'LIKE', "%$page%");
        }
        if (isset($data['answer']) && !empty($data['answer'])) {
            $answer = $data['answer'];
            $feedback = $feedback->where('feedback_rating.answer', 'LIKE', "%$answer%");
        }
        if (isset($data['answered']) && !empty($data['answered'])) {
            $answered = $data['answer'];
            $feedback = $feedback->where('feedback_rating.feedback_answered', 'LIKE', "%$answered%");
        }
        if (isset($data['displayed']) && !empty($data['displayed'])) {
            $displayed = $data['displayed'];
            $feedback = $feedback->where('feedback_rating.answer_displayed', 'LIKE', "%$displayed%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $feedback = $feedback->whereBetween('feedback_rating.createtime', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
        }

        $iTotalRecords = $feedback->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'feedback_rating.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'feedback_rating.id';
                break;
            case 1:
                $columnName = 'feedback_rating.user_id';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'feedback_rating.phone';
                break;
            case 4:
                $columnName = 'feedback_rating.feedback';
                break;
            case 5:
                $columnName = 'feedback_rating.page';
                break;
            case 6:
                $columnName = 'feedback_rating.answer';
                break;
            case 7:
                $columnName = 'feedback_rating.feedback_answered';
                break;
            case 8:
                $columnName = 'feedback_rating.answer_displayed';
                break;
            case 9:
                $columnName = 'feedback_rating.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $feedback = $feedback->where(function ($q) use ($search) {
                $q->where('feedback_rating.id', '=', $search)
                    ->orWhere('feedback_rating.user_id', 'Like', "%$search%")
                    ->orWhere('feedback_rating.page', 'Like', "%$search%")
                    ->orWhere('feedback_rating.feedback', 'Like', "%$search%")
                    ->orWhere('feedback_rating.answer', 'Like', "%$search%")
                    ->orWhere('feedback_rating.feedback_answered', 'Like', "%$search%")
                    ->orWhere('feedback_rating.answer_displayed', 'Like', "%$search%")
                    ->orWhere('feedback_rating.phone', 'Like', "%$search%")
                    ->orWhere('users.Email', 'Like', "%$search%");
            });
        }

        $feedback = $feedback->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($feedback as $comment) {
            $user_email = $comment->user_email;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $comment->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $comment->id,
                $comment->user_id,
                $user_email,
                $comment->phone,
                $comment->feedback,
                '<a target="_blank" href="'.$comment->page.'">'.$comment->page.'</a>',
                $comment->answer,
                $comment->feedback_answered,
                $comment->answer_displayed,
                $comment->createtime,
                '<div class="btn-group text-center" id="single-order-' . $comment->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('feedback_edit')) ? '<li>
                                            <a href="' . URL('admin/feedback/' . $comment->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                 
                                    </ul>
                                </div>',
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
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $feedback = FeedbackPopup::find($id);
        return view('auth.feedback_popup.edit', compact('feedback'));
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
        $data = $request->input();
        $feedback = FeedbackPopup::find($id);
        $validator = Validator::make($request->all(),
            array());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if ($request->has('answer')){
                $feedback->feedback_answered = '1';
                $feedback->answer = $data['answer'];
            }else{
                $feedback->feedback_answered = '0';
                $feedback->answer = $data['answer'];
            }
            $feedback->lastedit_by = Auth::user()->id;
            $feedback->lastedit_date = date("Y-m-d H:i:s");
            if ($feedback->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.feedback'));
                return Redirect::to("admin/feedback/$feedback->id/edit");
            }
        }
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
