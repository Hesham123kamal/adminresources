<?php

namespace App\Http\Controllers\Admin;

use App\CoursesCurriculumCertificates;
use App\NormalUser;
use App\Courses;
use App\CoursesCurriculum;
use App\Http\Controllers\Controller;
use App\UsersCurriculumAnswers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoursesCurriculumCertificatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        return view('auth.courses_curriculum_certificates.view',compact('courses'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $certificates=CoursesCurriculumCertificates::leftjoin('users', 'users.id', '=', 'courses_curriculum_certificates.user_id')
            ->leftjoin('courses', 'courses.id', '=', 'courses_curriculum_certificates.course_id')
            ->select('courses_curriculum_certificates.*', 'users.Email','courses.name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $certificates = $certificates->where('courses_curriculum_certificates.id', '=', $id);
        }
        if (isset($data['serial_number']) && !empty($data['serial_number'])) {
            $serial_number = $data['serial_number'];
            $certificates = $certificates->where('courses_curriculum_certificates.serial_number', 'LIKE', "%$serial_number%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $certificates = $certificates->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $certificates = $certificates->where('courses.id', '=', $course);
        }
        if (isset($data['user_name']) && !empty($data['user_name'])) {
            $user_name = $data['user_name'];
            $certificates = $certificates->where('courses_curriculum_certificates.user_name', 'LIKE', "%$user_name%");
        }
        if (isset($data['user_name_en']) && !empty($data['user_name_en'])) {
            $user_name_en = $data['user_name_en'];
            $certificates = $certificates->where('courses_curriculum_certificates.user_name_en', 'LIKE', "%$user_name_en%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $certificates = $certificates->whereBetween('courses_curriculum_certificates.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $certificates->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'courses_curriculum_certificates.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'courses_curriculum_certificates.id';
                break;
            case 1:
                $columnName = 'courses_curriculum_certificates.serial_number';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'courses.name';
                break;
            case 4:
                $columnName = 'courses_curriculum_certificates.user_name';
                break;
            case 5:
                $columnName = 'courses_curriculum_certificates.user_name_en';
                break;
            case 6:
                $columnName = 'courses_curriculum_certificates.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $certificates = $certificates->where(function ($q) use ($search) {
                $q->where('courses_curriculum_certificates.serial_number', 'LIKE', "%$search%")
                    ->orWhere('courses_curriculum_certificates.user_name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('courses_curriculum_certificates.id', '=', $search);
            });
        }

        $certificates = $certificates->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($certificates as $certificate) {
            $user = $certificate->Email;
            $course = $certificate->name;
            $user_name=$certificate->user_name;
            $user_name_en=$certificate->user_name_en;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user . '</a>';
                $user_name= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user_name . '</a>';
                $user_name_en= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user_name_en . '</a>';
            }
            if(PerUser('courses_edit') && $course !=''){
                $course= '<a target="_blank" href="' . URL('admin/courses/' . $certificate->course_id . '/edit') . '">' . $course . '</a>';
            }
            $records["data"][] = [
                $certificate->id,
                $certificate->serial_number,
                $user,
                $course,
                $user_name,
                $user_name_en,
                $certificate->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $certificate->id . '" type="checkbox" ' . ((!PerUser('courses_curriculum_certificates_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('courses_curriculum_certificates_publish')) ? 'class="changeStatues"' : '') . ' ' . (($certificate->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $certificate->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $certificate->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_curriculum_certificates_edit')) ? '<li>
                                            <a href="' . URL('admin/courses_curriculum_certificates/' . $certificate->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_curriculum_certificates_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $certificate->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_curriculum_certificates_copy')) ? '<li>
                                            <a href="'.URL('admin/courses_curriculum_certificates/copy/'.$certificate->id).'" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.copy') . '
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
        $courses=Courses::pluck('name', 'id');
        $curriculums_ids=CoursesCurriculum::pluck('id');
        $users_curriculum_answer_ids=UsersCurriculumAnswers::pluck('id');
        return view('auth.courses_curriculum_certificates.add',compact('courses','curriculums_ids','users_curriculum_answer_ids'));
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
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'course' => 'required|exists:mysql2.courses,id',
            'curriculum_id' => 'required|exists:mysql2.cources_curriculum,id',
            'users_curriculum_answer_id' => 'required|exists:mysql2.users_curriculum_answers,id',
            'serial_number' => 'required',
            'user_name' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $session_user_id = (isset($data['session_user_id'])) ? $data['session_user_id'] : 0;
            $certificate = new CoursesCurriculumCertificates();
            $certificate->user_id = $user_id;
            $certificate->course_id = $data['course'];
            $certificate->serial_number = $data['serial_number'];
            $certificate->user_name = $data['user_name'];
            $certificate->user_name_en = $data['user_name_en'];
            $certificate->session_user_id = $session_user_id;
            $certificate->curriculum_id = $data['curriculum_id'];
            $certificate->users_curriculum_answer_id = $data['users_curriculum_answer_id'];
//            $certificate->published = $published;
            $certificate->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $certificate->published_by = Auth::user()->id;
//                $certificate->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $certificate->unpublished_by = Auth::user()->id;
//                $certificate->unpublished_date = date("Y-m-d H:i:s");
//            }
            $certificate->lastedit_by = Auth::user()->id;
            $certificate->added_by = Auth::user()->id;
            $certificate->lastedit_date = date("Y-m-d H:i:s");
            $certificate->added_date = date("Y-m-d H:i:s");
            if ($certificate->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.course_curriculum_certificate'));
                return Redirect::to('admin/courses_curriculum_certificates/create');
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
        $certificate=CoursesCurriculumCertificates::findOrFail($id);
        $courses=Courses::pluck('name', 'id');
        $user=isset($certificate->user)?$certificate->user->Email:'';
        $curriculums_ids=CoursesCurriculum::pluck('id');
        $users_curriculum_answer_ids=UsersCurriculumAnswers::pluck('id');
        return view('auth.courses_curriculum_certificates.edit',compact('certificate','courses','curriculums_ids','users_curriculum_answer_ids','user'));
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
        $certificate = CoursesCurriculumCertificates::findOrFail($id);
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'course' => 'required|exists:mysql2.courses,id',
            'serial_number' => 'required',
            'user_name' => 'required',
            'curriculum_id' => 'required|exists:mysql2.cources_curriculum,id',
            'users_curriculum_answer_id' => 'required|exists:mysql2.users_curriculum_answers,id',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $session_user_id = (isset($data['session_user_id'])) ? $data['session_user_id'] : 0;
            $certificate->user_id = $user_id;
            $certificate->course_id = $data['course'];
            $certificate->serial_number = $data['serial_number'];
            $certificate->user_name = $data['user_name'];
            $certificate->user_name_en = $data['user_name_en'];
            $certificate->session_user_id = $session_user_id;
            $certificate->curriculum_id = $data['curriculum_id'];
            $certificate->users_curriculum_answer_id = $data['users_curriculum_answer_id'];
//            if ($published == 'yes' && $certificate->published=='no') {
//                $certificate->published_by = Auth::user()->id;
//                $certificate->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $certificate->published=='yes') {
//                $certificate->unpublished_by = Auth::user()->id;
//                $certificate->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $certificate->published = $published;
            $certificate->lastedit_by = Auth::user()->id;
            $certificate->lastedit_date = date("Y-m-d H:i:s");
            if ($certificate->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.course_curriculum_certificate'));
                return Redirect::to("admin/courses_curriculum_certificates/$certificate->id/edit");
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
        $certificate = CoursesCurriculumCertificates::findOrFail($id);
        $certificate->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $certificate = CoursesCurriculumCertificates::findOrFail($id);
//            if ($published == 'no') {
//                $certificate->published = 'no';
//                $certificate->unpublished_by = Auth::user()->id;
//                $certificate->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $certificate->published = 'yes';
//                $certificate->published_by = Auth::user()->id;
//                $certificate->published_date = date("Y-m-d H:i:s");
//            }
//            $certificate->save();
//        } else {
//            return redirect(404);
//        }
//    }

    public function autoCompleteAnswersIds(Request $request)
    {
        if($request->get('query')){
            $query=$request->get('query');
            $data = UsersCurriculumAnswers::where('id','LIKE', "%$query%")->get();
            $output='<ul id="answers-ids" class="dropdown-menu"
                    style="display:block; position:relative">';
            foreach ($data as $row){
                $output.='<li><a href="#">'.$row->id.'</a></li>';
            }
            $output.='</ul>';
            echo $output;
        }
    }

    public function copy($id)
    {
        $certificate = CoursesCurriculumCertificates::findOrFail($id);
        $certificate->createdtime = date("Y-m-d H:i:s");
        $certificate->replicate()->save();
        return Redirect::to('admin/courses_curriculum_certificates/'.$certificate->id.'/edit');
    }
}
