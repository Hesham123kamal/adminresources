<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Instructors;
use App\Live;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $parent_lives = Live::get();
//        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.live.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
//        $lives = Live::select('live.*', 'parent.name AS parent_name','instractors.name AS instructor_name','instractors.url AS instructor_url')->leftJoin('live AS parent', 'parent.id', '=', 'live.parent_id')->leftJoin('instractors','live.instractor','=','instractors.id');
        $lives = Live::select('live.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $lives = $lives->where('live.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $lives = $lives->where('live.name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $lives = $lives->whereBetween('live.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
//        if (isset($data['instructors']) && !empty($data['instructors'])) {
//            $lives = $data['instructors'];
//            $lives = $lives->where('live.instractor', 'LIKE', "%$lives%");
//        }
        if (isset($data['location']) && !empty($data['location'])) {
            $location = $data['location'];
            $lives = $lives->where('live.location', 'LIKE', "%$location%");
        }
//        if (isset($data['type']) && !empty($data['type'])) {
//            $type = $data['type'];
//            $lives = $lives->where('live.type', 'LIKE', "%$type%");
//        }
//        if (isset($data['parent_id']) && !empty($data['parent_id'])) {
//            $parent_id = $data['parent_id'];
//            $lives = $lives->where('parent.name', 'LIKE', "%$parent_id%");
//        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $lives = $lives->where('live.url', 'LIKE', "%$url%");
        }


        $iTotalRecords = $lives->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'live.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'live.id';
                break;
            case 1:
                $columnName = 'live.name';
                break;
//            case 2:
//                $columnName = 'parent.name';
//                break;
//            case 3:
//                $columnName = 'instractors.name';
//                break;
            case 4:
                $columnName = 'live.location';
                break;
            case 5:
                $columnName = 'live.url';
                break;
//            case 6:
//                $columnName = 'live.type';
//                break;
            case 7:
                $columnName = 'live.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $lives = $lives->where(function ($q) use ($search) {
                $q->where('live.name', 'LIKE', "%$search%")
//                    ->orWhere('live.instractor', 'LIKE', "%$search%")
                    ->orWhere('live.location', 'LIKE', "%$search%")
//                    ->orWhere('live.type', 'LIKE', "%$search%")
//                    ->orWhere('parent.parent_name', 'LIKE', "%$search%")
                    ->orWhere('live.meta_description', 'LIKE', "%$search%")
                    ->orWhere('live.description', 'LIKE', "%$search%")
                    ->orWhere('live.id', '=', $search);
            });
        }

        $lives = $lives->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($lives as $live) {
            $records["data"][] = [
                $live->id,
                $live->name,
//                $live->parent_name,
//                '<a href="' . e3mURL('instractor/' . $live->instructor_url) . '" target="_blank">' . $live->instructor_name . '</a>',
                $live->location,
                $live->url,
//                $live->type,
                $live->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $live->id . '" type="checkbox" ' . ((!PerUser('live_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('live_active')) ? 'class="changeStatues"' : '') . ' ' . (($live->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $live->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $live->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('live_edit')) ? '<li>
                                            <a href="' . URL('admin/live/' . $live->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('live_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $live->id . '" >
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
//        $parent_lives = Live::get();
//        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.live.add');
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
                'description' => 'required',
                'pic' => 'required',
                'url' => 'required',
//                'type' => 'required',
                'meta_description' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $lives = new Live();
            $lives->name = $data['name'];
            $lives->short_description = $data['short_description'];
            $lives->description = $data['description'];
            $lives->link = $data['v_url'];
//            $lives->audio_link = $data['audio_link'];
            $lives->image = $picName;
            $lives->duetime = $data['duetime'];
//            $lives->instractor = $data['instructors'];
            $lives->location = $data['location'];
            $lives->url = str_replace(' ','-',$data['url']);
//            $lives->type = $data['type'];
//            $lives->parent_id = isset($data['parent_id'])?$data['parent_id']:0;
            $lives->isfree = $data['isfree'];
            $lives->meta_description = $data['meta_description'];
            $lives->published = $active;
            if ($active == 'yes') {
                $lives->published_by = Auth::user()->id;
                $lives->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $lives->unpublished_by = Auth::user()->id;
                $lives->unpublished_date = date("Y-m-d H:i:s");
            }
            $lives->added_by = Auth::user()->id;
            $lives->added_date = date("Y-m-d H:i:s");
            $lives->createdtime = date("Y-m-d H:i:s");
            $lives->lastedit_by = Auth::user()->id;
            $lives->lastedit_date = date("Y-m-d H:i:s");
            if ($lives->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.live'));
                return Redirect::to('admin/live/create');
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
        $live = Live::find($id);
//        $parent_lives = Live::get();
//        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.live.edit', compact('live'));
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
        $live = Live::find($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'description' => 'required',
                'url' => 'required',
//                'type' => 'required',
                'meta_description' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if ( $request->file('pic')){
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $live->image = $picName;
            }
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $live->name = $data['name'];
            $live->short_description = $data['short_description'];
            $live->description = $data['description'];
            $live->link = $data['v_url'];
//            $live->audio_link = $data['audio_link'];
            $live->duetime = $data['duetime'];
//            $live->instractor = $data['instructors'];
            $live->location = $data['location'];
            $old_url=$live->url;
            $live->url = str_replace(' ','-',$data['url']);
//            $live->type = $data['type'];
//            $live->parent_id = isset($data['parent_id'])?$data['parent_id']:0;
            $live->isfree = $data['isfree'];
            $live->meta_description = $data['meta_description'];
            if ($active == 'yes' && $live->published=='no') {
                $live->published_by = Auth::user()->id;
                $live->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no' && $live->published=='yes') {
                $live->unpublished_by = Auth::user()->id;
                $live->unpublished_date = date("Y-m-d H:i:s");
            }
            $live->published = $active;
            $live->lastedit_by = Auth::user()->id;
            $live->lastedit_date = date("Y-m-d H:i:s");
            if ($live->save()) {
                if($old_url != $live->url){
                    saveOldUrl($id,'live',$old_url,$live->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.live'));
                return Redirect::to("admin/live/$live->id/edit");
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
        $live = Live::find($id);
        if (count($live)) {
            $live->delete();
            $live->deleted_by = Auth::user()->id;
            $live->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $live = Live::find($id);
            if ($active == 'no') {
                $live->published = 'no';
                $live->unpublished_by = Auth::user()->id;
                $live->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $live->published = 'yes';
                $live->published_by = Auth::user()->id;
                $live->published_date = date("Y-m-d H:i:s");
            }
            $live->save();
        } else {
            return redirect(404);
        }
    }
}
