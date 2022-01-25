<?php

namespace App\Http\Controllers\Admin;

use App\DiplomaCertificate;
use App\Diplomas;
use App\DiplomaUserCourse;
use App\Http\Controllers\Controller;
use App\NormalUser;
use App\User;
use App\UsersCurriculumAnswers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class DiplomaCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $diplomas = Diplomas::pluck('name', 'id');
        return view('auth.diploma_certificates.view',compact('diplomas'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $certificates = DiplomaCertificate::leftjoin('diplomas','diplomas.id','=','diploma_certificates.diploma_id')
        ->select('diploma_certificates.*','diplomas.name as diploma_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $certificates = $certificates->where('diploma_certificates.id', '=', "$id");
        }
        if (isset($data['serial_number']) && !empty($data['serial_number'])) {
            $serial_number = $data['serial_number'];
            $certificates = $certificates->where('diploma_certificates.serial_number', 'LIKE', "%$serial_number%");
        }
        if (isset($data['user_name']) && !empty($data['user_name'])) {
            $user_name = $data['user_name'];
            $certificates = $certificates->where('diploma_certificates.user_name', 'LIKE', "%$user_name%");
        }
        if (isset($data['user_name_en']) && !empty($data['user_name_en'])) {
            $user_name_en = $data['user_name_en'];
            $certificates = $certificates->where('diploma_certificates.user_name_en', 'LIKE', "%$user_name_en%");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $certificates = $certificates->where('diploma_certificates.diploma_id','=', $diploma);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $certificates = $certificates->whereBetween('diploma_certificates.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'diploma_certificates.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'diploma_certificates.id';
                break;
            case 1:
                $columnName = 'diploma_certificates.serial_number';
                break;
            case 2:
                $columnName = 'diploma_certificates.user_name';
                break;
            case 3:
                $columnName = 'diploma_certificates.user_name_en';
                break;
            case 4:
                $columnName = 'diploma_certificates.createdtime';
                break;
            case 5:
                $columnName = 'diplomas.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $certificates = $certificates->where(function ($q) use ($search) {
                $q->where('diploma_certificates.serial_number', 'LIKE', "%$search%")
                    ->orWhere('diploma_certificates.user_name', 'LIKE', "%$search%")
                    ->orWhere('diploma_certificates.user_name_en', 'LIKE', "%$search%")
                    ->orWhere('diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('diploma_certificates.id', '=', $search);
            });
        }

        $certificates = $certificates->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($certificates as $certificate) {
            $diploma_name = $certificate->diploma_name;
            $user_name = $certificate->user_name;
            $user_name_en = $certificate->user_name_en;
            if(PerUser('diplomas_edit') && $diploma_name !=''){
                $diploma_name= '<a target="_blank" href="' . URL('admin/diplomas/' . $certificate->diploma_id . '/edit') . '">' . $diploma_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_name !='' && $certificate->user_id){
                $user_name= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_name_en !='' && $certificate->user_id){
                $user_name_en= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user_name_en . '</a>';
            }
            $records["data"][] = [
                $certificate->id,
                $certificate->serial_number,
                $user_name,
                $user_name_en,
                $certificate->createdtime,
                $diploma_name,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $certificate->id . '" type="checkbox" ' . ((!PerUser('diploma_certificates_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('diploma_certificates_publish')) ? 'class="changeStatues"' : '') . ' ' . (($certificate->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $certificate->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $certificate->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('diploma_certificates_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $certificate->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                        ' . ((PerUser('diploma_certificates_copy')) ? '<li>
                                            <a href="'.URL('admin/diploma_certificates/copy/'.$certificate->id).'" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.copy') . '
                                            </a>
                                        </li>' : '') . '
                                        ' . ((PerUser('diploma_certificates_view_exams_result')) ? '<li>
                                            <a target="_blank" href="'.URL('admin/diploma_certificates/'.$certificate->id.'/view_exams_result').'" >
                                                <i class="fa fa-eye"></i> ' . Lang::get('main.view_exams_result') . '
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

    public function destroy($id)
    {
        $certificate = DiplomaCertificate::findOrFail($id);
        $certificate->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $certificate = DiplomaCertificate::findOrFail($id);
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

    public function copy($id)
    {
        $certificate = DiplomaCertificate::findOrFail($id);
        $certificate->createdtime = date("Y-m-d H:i:s");
        $certificate->replicate()->save();
        return Redirect::to('admin/diploma_certificates');
    }
    public function viewExamsResult($id){

        $certificate = DiplomaCertificate::select('diploma_certificates.*','diplomas.name AS diploma_name')->join('diplomas','diplomas.id','=','diploma_certificates.diploma_id')->findOrFail($id);
        $user=NormalUser::find($certificate->user_id);
        //SELECT users_curriculum_answers.id,MAX(right_answers),users_curriculum_answers.right_answers,users_curriculum_answers.createdtime,courses.name FROM users_curriculum_answers
        //INNER JOIN cources_curriculum ON cources_curriculum.id=users_curriculum_answers.curriculum_id
        //INNER JOIN courses ON courses.id=cources_curriculum.course_id
        //WHERE user_id=5733 AND users_curriculum_answers.curriculum_type='exam' GROUP BY user_id,users_curriculum_answers.curriculum_id
        $curriculum_answers=UsersCurriculumAnswers::select('users_curriculum_answers.id','users_curriculum_answers.curriculum_id',DB::raw("MAX(right_answers) AS right_answers"),'users_curriculum_answers.quetions_numbers','users_curriculum_answers.wrong_answers','users_curriculum_answers.duration_time','users_curriculum_answers.createdtime','courses.name')
        ->join('cources_curriculum','cources_curriculum.id','=','users_curriculum_answers.curriculum_id')
        ->join('courses','courses.id','=','users_curriculum_answers.course_id')
            ->where('user_id',$certificate->user_id)->whereIn('courses.id',DiplomaUserCourse::where('diploma_id',$certificate->diploma_id)->pluck('course_id')->toArray())->where('users_curriculum_answers.curriculum_type','exam')
            ->groupBy('users_curriculum_answers.user_id','courses.id')
            ->get();
        ;
        //dd($curriculum_answers);
        return view('auth.diploma_certificates.view_result',compact('certificate','curriculum_answers','user'));
    }

}
