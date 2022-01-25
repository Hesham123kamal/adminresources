<?php

namespace App\Http\Controllers\Admin;

use App\Authors;
use App\BecomeInstructor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class BecomeInstructorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.become_instructor.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $bis = BecomeInstructor::select('become_instructor.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $bis = $bis->where('become_instructor.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $bis = $bis->where('become_instructor.name', 'LIKE', "%$name%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $bis = $bis->where('become_instructor.phone', 'LIKE', "%$phone%");
        }
        if (isset($data['cv']) && !empty($data['cv'])) {
            $cv = $data['cv'];
            $bis = $bis->where('become_instructor.cv', 'LIKE', "%$cv%");
        }
        if (isset($data['linkedin']) && !empty($data['linkedin'])) {
            $linkedin = $data['linkedin'];
            $bis = $bis->where('become_instructor.linkedin', 'LIKE', "%$linkedin%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $bis = $bis->whereBetween('become_instructor.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $bis->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'books.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'become_instructor.id';
                break;
            case 1:
                $columnName = 'become_instructor.name';
                break;
            case 2:
                $columnName = 'become_instructor.phone';
                break;
            case 3:
                $columnName = 'become_instructor.id';
                break;
            case 4:
                $columnName = 'become_instructor.id';
                break;
            case 5:
                $columnName = 'become_instructor.linkedin';
                break;
            case 6:
                $columnName = 'become_instructor.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $bis = $bis->where(function ($q) use ($search) {
                $q->where('become_instructor.name', 'LIKE', "%$search%")
                    ->orWhere('become_instructor.phone', 'LIKE', "%$search%")
                    ->orWhere('become_instructor.cv', 'LIKE', "%$search%")
                    ->orWhere('become_instructor.linkedin', 'LIKE', "%$search%")
                    ->orWhere('become_instructor.id', '=', $search);
            });
        }

        $bis = $bis->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($bis as $bi) {
            $records["data"][] = [
                $bi->id,
                $bi->name,
                $bi->phone,
                //'<a href="' . assetURL($bi->cv) . '">' . $bi->cv . '</a>',
                '<a href="' . URL('admin/become_instructor/download_file/'.$bi->id.'/cv') . '">' . $bi->cv . '</a>',
//                '<a href="' . URL('admin/become_instructor/download_file/'.$bi->id.'/video') . '">' . $bi->video . '</a>',
                '<a target="_blank" href="' . $bi->video . '">' . $bi->video . '</a>',

                '<a target="_blank" href="' . $bi->linkedin . '">' . $bi->linkedin . '</a>',
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $bi->id . '" type="checkbox" ' . ((!PerUser('become_instructor_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('become_instructor_publish')) ? 'class="changeStatues"' : '') . ' ' . (($bi->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $bi->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                $bi->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $bi->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('become_instructor_edit')) ? '<li>
                                            <a href="' . URL('admin/become_instructor/' . $bi->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('become_instructor_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $bi->id . '" >
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
        return view('auth.become_instructor.add');
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
                'name' => 'required',
                'phone' => 'required',
                'linkedin' => 'required',
                'courses' => 'required',
                'bio' => 'required',
                'cv' => 'required|mimes:pdf,doc,docx',
                'video'  => 'required',
            ));
        if ($validator->fails()){
            //return redirect()->back()->withErrors($validator->errors())->withInput();
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json($messsage);
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $cv = $request->file('cv');
            $cvname = uploadFileToE3melbusiness($cv);
            $bi = new BecomeInstructor();
            $bi->name = $data['name'];
            $bi->phone = $data['phone'];
            $bi->courses = $data['courses'];
            $bi->linkedin = $data['linkedin'];
            $bi->bio = $data['bio'];
            $bi->cv = $cvname;
            $bi->video = $data['video'];
            //$bi->published = $published;
            $bi->added_by = Auth::user()->id;
//            if ($published == 'yes') {
//                $bi->published_by = Auth::user()->id;
//                $bi->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $bi->unpublished_by = Auth::user()->id;
//                $bi->unpublished_date = date("Y-m-d H:i:s");
//            }
            $bi->lastedit_by = Auth::user()->id;
            $bi->lastedit_date = date("Y-m-d H:i:s");
            $bi->added_date = date("Y-m-d H:i:s");
            if ($bi->save()) {
                $messsage = '<div class="alert alert-success"><ul>';
                    $messsage .= '<li>' . Lang::get('main.insert') . Lang::get('main.become_instructor') ." record" . '</li>';
                $messsage .= '</ul></div>';
                return response()->json($messsage);
//                return Redirect::to('admin/webinars/create');
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
        $bi = BecomeInstructor::findOrFail($id);
        return view('auth.become_instructor.edit', compact('bi'));
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
        $bi = BecomeInstructor::findOrFail($id);
        $rules_array=array(
            'name' => 'required',
            'phone' => 'required',
            'linkedin' => 'required',
            'courses' => 'required',
            'bio' => 'required',
            'video' => 'required',
        );
        if ( $request->file('cv')){
            $rules_array['cv']='required|mimes:pdf,doc,docx';
        }
        $validator = Validator::make($request->all(),$rules_array);
        if ($validator->fails()) {
            //return redirect()->back()->withErrors($validator->errors())->withInput();
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json($messsage);
        }else {
            if ( $request->file('cv')){
                $cv = $request->file('cv');
                $cv_name = uploadFileToE3melbusiness($cv);
                $bi->cv = $cv_name;
            }
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $bi->name = $data['name'];
            $bi->phone = $data['phone'];
            $bi->linkedin = $data['linkedin'];
            $bi->courses = $data['courses'];
            $bi->bio = $data['bio'];
            $bi->video = $data['video'];
//            if ($published == 'yes' && $bi->published=='no') {
//                $bi->published_by = Auth::user()->id;
//                $bi->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $bi->published=='yes') {
//                $bi->unpublished_by = Auth::user()->id;
//                $bi->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $bi->published = $published;
            $bi->lastedit_by = Auth::user()->id;
            $bi->lastedit_date = date("Y-m-d H:i:s");
            if ($bi->save()) {
//                Session::flash('success', Lang::get('main.update') . Lang::get('main.become_instructor'));
//                return Redirect::to("admin/become_instructor/$bi->id/edit");
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.update') . Lang::get('main.become_instructor') ." record" . '</li>';
                $messsage .= '</ul></div>';
                return response()->json($messsage);
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
        $bi = BecomeInstructor::findOrFail($id);
        $bi->delete();
    }
    public function downloadFile($id,$type){
        $bi = BecomeInstructor::find($id);
        if(count($bi)){
            return Response::download(filePath().$bi->$type,$bi->$type);
        }
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $bi = BecomeInstructor::findOrFail($id);
//            if ($published == 'no') {
//                $bi->published = 'no';
//                $bi->unpublished_by = Auth::user()->id;
//                $bi->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $bi->published = 'yes';
//                $bi->published_by = Auth::user()->id;
//                $bi->published_date = date("Y-m-d H:i:s");
//            }
//            $bi->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
