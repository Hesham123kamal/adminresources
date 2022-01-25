<?php

namespace App\Http\Controllers\Admin;

use App\Instructors;
use App\Successstories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SuccessstoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.successstories.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $successstories = Successstories::select('successtories.*','instractors.name AS instructor_name','instractors.url AS instructor_url')->leftJoin('instractors','successtories.instractor','=','instractors.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $successstories = $successstories->where('successtories.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $successstories = $successstories->where('successtories.name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $successstories = $successstories->whereBetween('successtories.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        if (isset($data['instructor']) && !empty($data['instructor'])) {
            $instructor = $data['instructor'];
            $successstories = $successstories->where('instractors.name', 'LIKE', "%$instructor%");
        }
        if (isset($data['location']) && !empty($data['location'])) {
            $location = $data['location'];
            $successstories = $successstories->where('successtories.location', 'LIKE', "%$location%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $successstories = $successstories->where('successtories.url', 'LIKE', "%$url%");
        }


        $iTotalRecords = $successstories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'successtories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'successtories.id';
                break;
            case 1:
                $columnName = 'successtories.name';
                break;
            case 2:
                $columnName = 'instractors.name';
                break;
            case 3:
                $columnName = 'successtories.location';
                break;
            case 4:
                $columnName = 'successtories.url';
                break;
            case 5:
                $columnName = 'successtories.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $successstories = $successstories->where(function ($q) use ($search) {
                $q->where('successtories.name', 'LIKE', "%$search%")
                    ->orWhere('instractors.name', 'LIKE', "%$search%")
                    ->orWhere('successtories.location', 'LIKE', "%$search%")
                    ->orWhere('successtories.meta_description', 'LIKE', "%$search%")
                    ->orWhere('successtories.description', 'LIKE', "%$search%")
                    ->orWhere('successtories.id', '=', $search);
            });
        }

        $successstories = $successstories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($successstories as $successstory) {
            $instructor_name = $successstory->instructor_name;
            if(PerUser('instructors_edit') && $instructor_name !=''){
                $instructor_name= '<a target="_blank" href="' . URL('admin/instructors/' . $successstory->instractor . '/edit') . '">' . $instructor_name . '</a>';
            }
            $records["data"][] = [
                $successstory->id,
                $successstory->name,
                $instructor_name,
                $successstory->location,
                '<a target="_blank" href="' . e3mURL('successtories/' . $successstory->url) . '">' . $successstory->url . '</a>',
                $successstory->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $successstory->id . '" type="checkbox" ' . ((!PerUser('successstories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('successstories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($successstory->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $successstory->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $successstory->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('successstories_edit')) ? '<li>
                                            <a href="' . URL('admin/successstories/' . $successstory->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('successstories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $successstory->id . '" >
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
        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.successstories.add',compact('instructors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'instructor' => 'required|exists:mysql2.instractors,id',
                'short_description' => 'required',
                'description' => 'required',
                'pic' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'url' => 'required|unique:mysql2.successtories,url',
                'meta_description' => 'required',
                'location' => 'required|in:egy,ksa',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $successstories = new Successstories();
            $successstories->name = $data['name'];
            $successstories->short_description = $data['short_description'];
            $successstories->description = $data['description'];
            $successstories->link = $data['v_url'];
            $successstories->image = $picName;
            $successstories->duetime = $data['duetime'];
            $successstories->instractor = $data['instructor'];
            $successstories->level = $data['level'];
            $successstories->location = $data['location'];
            $successstories->url = str_replace(' ','-',$data['url']);
            $successstories->ispublic = $data['ispublic'];
            $successstories->meta_description = $data['meta_description'];
            $successstories->createdtime = date("Y-m-d H:i:s");
            $successstories->published = $published;
            if ($published == 'yes') {
                $successstories->published_by = Auth::user()->id;
                $successstories->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $successstories->unpublished_by = Auth::user()->id;
                $successstories->unpublished_date = date("Y-m-d H:i:s");
            }
            $successstories->lastedit_by = Auth::user()->id;
            $successstories->lastedit_date = date("Y-m-d H:i:s");
            $successstories->added_by = Auth::user()->id;
            $successstories->added_date = date("Y-m-d H:i:s");
            if ($successstories->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.successtory'));
                return Redirect::to('admin/successstories/create');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $successstory = Successstories::find($id);
        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.successstories.edit', compact('instructors', 'successstory'));
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
        $data = $request->input();
        $successstory = Successstories::find($id);
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules=array(
            'name' => 'required',
            'description' => 'required',
            'instructor' => 'required|exists:mysql2.instractors,id',
            'url' => "required|unique:mysql2.successtories,url,$id,id",
            'short_description' => 'required',
            'meta_description' => 'required',
            'location' => 'required|in:egy,ksa',
        );
        if ( $request->file('pic')){
            $rules['pic']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if ( $request->file('pic')){
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $successstory->image = $picName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $successstory->name = $data['name'];
            $successstory->short_description = $data['short_description'];
            $successstory->description = $data['description'];
            $successstory->link = $data['v_url'];
            $successstory->duetime = $data['duetime'];
            $successstory->instractor = $data['instructor'];
            $successstory->level = $data['level'];
            $successstory->location = $data['location'];
            $old_url=$successstory->url;
            $successstory->url = str_replace(' ','-',$data['url']);
            $successstory->ispublic = $data['ispublic'];
            $successstory->meta_description = $data['meta_description'];
            if ($published == 'yes' && $successstory->published=='no') {
                $successstory->published_by = Auth::user()->id;
                $successstory->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $successstory->published=='yes') {
                $successstory->unpublished_by = Auth::user()->id;
                $successstory->unpublished_date = date("Y-m-d H:i:s");
            }
            $successstory->published = $published;
            $successstory->lastedit_by = Auth::user()->id;
            $successstory->lastedit_date = date("Y-m-d H:i:s");
            $successstory->modifiedtime = date("Y-m-d H:i:s");
            if ($successstory->save()) {
                if($old_url != $successstory->url){
                    saveOldUrl($id,'successtories',$old_url,$successstory->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.successtory'));
                return Redirect::to("admin/successstories/$successstory->id/edit");
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $successstory = Successstories::find($id);
        if (count($successstory)) {
            $successstory->delete();
            $successstory->deleted_by = Auth::user()->id;
            $successstory->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $successstory = Successstories::find($id);
            if ($published == 'no') {
                $successstory->published = 'no';
                $successstory->unpublished_by = Auth::user()->id;
                $successstory->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $successstory->published = 'yes';
                $successstory->published_by = Auth::user()->id;
                $successstory->published_date = date("Y-m-d H:i:s");
            }
            $successstory->save();
        } else {
            return redirect(404);
        }
    }
}
