<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\Faq;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        return view('auth.faq.view',compact('courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $faqs = Faq::leftjoin('courses','courses.id','=','faq.course_id')
                    ->select('faq.*','courses.name as course_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $faqs = $faqs->where('faq.id', '=', $id);
        }
        if (isset($data['question']) && !empty($data['question'])) {
            $question = $data['question'];
            $faqs = $faqs->where('faq.question', 'LIKE', "%$question%");
        }
        if (isset($data['answer']) && !empty($data['answer'])) {
            $answer = $data['answer'];
            $faqs = $faqs->where('faq.answer', 'LIKE', "%$answer%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $faqs = $faqs->where('courses.id', '=', $course);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $faqs = $faqs->whereBetween('faq.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $faqs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'faq.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'faq.id';
                break;
            case 1:
                $columnName = 'faq.question';
                break;
            case 2:
                $columnName = 'faq.answer';
                break;
            case 3:
                $columnName = 'faq.createdtime';
                break;
            case 4:
                $columnName = 'courses.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $faqs = $faqs->where(function ($q) use ($search) {
                $q->where('faq.question', 'LIKE', "%$search%")
                    ->orWhere('faq.answer', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('faq.id', '=', $search);
            });
        }

        $faqs = $faqs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($faqs as $faq) {
            $course_name = $faq->course_name;
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $faq->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            $records["data"][] = [
                $faq->id,
                $faq->question,
                $faq->answer,
                $faq->createdtime,
                $course_name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $faq->id . '" type="checkbox" ' . ((!PerUser('faq_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('faq_publish')) ? 'class="changeStatues"' : '') . ' ' . (($faq->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $faq->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $faq->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('faq_edit')) ? '<li>
                                            <a href="' . URL('admin/faq/' . $faq->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('faq_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $faq->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses = Courses::pluck('name', 'id');
        return view('auth.faq.add',compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $validator = Validator::make($request->all(),
            array(
                'course' =>'required|exists:mysql2.courses,id',
                'question' => 'required',
                'answer' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $faq = new Faq();
            $faq->question = $data['question'];
            $faq->answer = $data['answer'];
            $faq->course_id = $data['course'];
            $faq->published = $published;
            $faq->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $faq->published_by = Auth::user()->id;
                $faq->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $faq->unpublished_by = Auth::user()->id;
                $faq->unpublished_date = date("Y-m-d H:i:s");
            }
            $faq->lastedit_by = Auth::user()->id;
            $faq->added_by = Auth::user()->id;
            $faq->lastedit_date = date("Y-m-d H:i:s");
            $faq->added_date = date("Y-m-d H:i:s");
            if ($faq->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.faq'));
                return Redirect::to('admin/faq/create');
            }
        }
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
        $faq = Faq::findOrFail($id);
        $courses = Courses::pluck('name', 'id');
        return view('auth.faq.edit', compact('courses','faq'));
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
        $faq = Faq::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'course' =>'required|exists:mysql2.courses,id',
                'question' => 'required',
                'answer' => 'required',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $faq->question = $data['question'];
            $faq->answer = $data['answer'];
            $faq->course_id = $data['course'];
            if ($published == 'yes' && $faq->published=='no') {
                $faq->published_by = Auth::user()->id;
                $faq->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $faq->published=='yes') {
                $faq->unpublished_by = Auth::user()->id;
                $faq->unpublished_date = date("Y-m-d H:i:s");
            }
            $faq->published = $published;
            $faq->lastedit_by = Auth::user()->id;
            $faq->lastedit_date = date("Y-m-d H:i:s");
            if ($faq->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.faq'));
                return Redirect::to("admin/faq/$faq->id/edit");
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
        $faq = Faq::findOrFail($id);
        $faq->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $faq = Faq::findOrFail($id);
            if ($published == 'no') {
                $faq->published = 'no';
                $faq->unpublished_by = Auth::user()->id;
                $faq->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $faq->published = 'yes';
                $faq->published_by = Auth::user()->id;
                $faq->published_date = date("Y-m-d H:i:s");
            }
            $faq->save();
        } else {
            return redirect(404);
        }
    }
}
