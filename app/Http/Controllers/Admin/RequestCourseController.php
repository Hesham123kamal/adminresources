<?php

namespace App\Http\Controllers\Admin;

use App\RequestCourse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class RequestCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = RequestCourse::get();
        return view('auth.request_courses.view', compact('courses'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $courses = RequestCourse::join('country','country.id','=','request_courses.country')
                    ->select('request_courses.*','country.arab_name as country_name');

        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $courses = $courses->where('request_courses.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $courses = $courses->where('request_courses.name', 'LIKE', "%$name%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $courses = $courses->where('request_courses.phone', 'LIKE', "%$phone%");
        }
        if (isset($data['expert']) && !empty($data['expert'])) {
            $expert = $data['expert'];
            $courses = $courses->where('request_courses.expert', 'LIKE', "%$expert%");
        }
        if (isset($data['country']) && !empty($data['country'])) {
            $country = $data['country'];
            $courses = $courses->where('country.arab_name', 'LIKE', "%$country%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $courses = $courses->where('request_courses.email', 'LIKE', "%$email%");
        }
        if (isset($data['courses_names']) && !empty($data['courses_names'])) {
            $courses_names = $data['courses_names'];
            $courses = $courses->where('request_courses.courses_names', 'LIKE', "%$courses_names%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $courses = $courses->whereBetween('request_courses.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'request_courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'request_courses.id';
                break;
            case 1:
                $columnName = 'request_courses.name';
                break;
            case 2:
                $columnName = 'request_courses.email';
                break;
            case 3:
                $columnName = 'request_courses.courses_names';
                break;
            case 4:
                $columnName = 'request_courses.phone';
                break;
            case 5:
                $columnName = 'country.arab_name';
                break;
            case 6:
                $columnName = 'request_courses.expert';
                break;
            case 7:
                $columnName = 'request_courses.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $courses = $courses->where(function ($q) use ($search) {
                $q->where('request_courses.name', 'LIKE', "%$search%")
                    ->orWhere('request_courses.email', 'LIKE', "%$search%")
                    ->orWhere('request_courses.courses_names', 'LIKE', "%$search%")
                    ->orWhere('request_courses.phone', 'LIKE', "%$search%")
                    ->orWhere('country.arab_name', 'LIKE', "%$search%")
                    ->orWhere('request_courses.expert', 'LIKE', "%$search%")
                    ->orWhere('request_courses.id', '=', $search);
            });
        }

        $courses = $courses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($courses as $course) {
            $records["data"][] = [
                $course->id,
                $course->name,
                $course->email,
                $course->courses_names,
                $course->phone,
                $course->country_name,
                $course->expert,
                $course->createtime,''
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
        return response()->json($records)->setCallback($request->input('callback'));

    }

}
