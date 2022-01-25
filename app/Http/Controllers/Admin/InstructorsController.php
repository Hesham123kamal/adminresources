<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Instructors;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class InstructorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.instructors.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $instructors = Instructors::select('instractors.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $instructors = $instructors->where('instractors.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $instructors = $instructors->where('instractors.name', 'LIKE', "%$name%");
        }
        if (isset($data['pic']) && !empty($data['pic'])) {
            $pic = $data['pic'];
            $instructors = $instructors->where('instractors.pic', 'LIKE', "%$pic%");
        }
        if (isset($data['linkedin']) && !empty($data['linkedin'])) {
            $linkedin = $data['linkedin'];
            $instructors = $instructors->where('instractors.linkedin', 'LIKE', "%$linkedin%");
        }
        if (isset($data['facebook']) && !empty($data['facebook'])) {
            $facebook = $data['facebook'];
            $instructors = $instructors->where('instractors.facebook', 'LIKE', "%$facebook%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $instructors = $instructors->where('instractors.url', 'LIKE', "%$url%");
        }


        $iTotalRecords = $instructors->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'instractors.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'instractors.id';
                break;
            case 1:
                $columnName = 'instractors.name';
                break;
            case 2:
                $columnName = 'instractors.pic';
                break;
            case 3:
                $columnName = 'instractors.linkedin';
                break;
            case 4:
                $columnName = 'instractors.facebook';
                break;
            case 5:
                $columnName = 'instractors.url';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $instructors = $instructors->where(function ($q) use ($search) {
                $q->where('instractors.name', 'LIKE', "%$search%")
                    ->orWhere('instractors.pic', 'LIKE', "%$search%")
                    ->orWhere('instractors.linkedin', 'LIKE', "%$search%")
                    ->orWhere('instractors.facebook', 'LIKE', "%$search%")
                    ->orWhere('instractors.url', 'LIKE', "%$search%")
                    ->orWhere('instractors.description', 'LIKE', "%$search%")
                    ->orWhere('instractors.id', '=', $search);
            });
        }

        $instructors = $instructors->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($instructors as $instructor) {
            $instructor=makeDefaultImageGeneral($instructor,'pic');
            $records["data"][] = [
                $instructor->id,
                $instructor->name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img  style="width:100%;"  src="' . assetURL($instructor->pic) . '"></a>',
                '<a href="' . $instructor->linkedin . '" target="_blank">' . $instructor->linkedin . '</a>',
                '<a href="' . $instructor->facebook . '" target="_blank">' . $instructor->facebook . '</a>',
                '<a href="' . e3mURL('instractor/' . $instructor->url) . '" target="_blank">' . $instructor->url . '</a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $instructor->id . '" type="checkbox" ' . ((!PerUser('instructors_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('instructors_publish')) ? 'class="changeStatues"' : '') . ' ' . (($instructor->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $instructor->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $instructor->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('instructors_edit')) ? '<li>
                                            <a href="' . URL('admin/instructors/' . $instructor->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('instructors_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $instructor->id . '" >
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
        return view('auth.instructors.add');
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
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'en_name' => 'required',
                'description' => 'required',
                'en_description' => 'required',
                'pic' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'url' => 'required|unique:mysql2.instractors,url',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $facebook = (isset($data['facebook'])) ? $data['facebook'] : '';
            $linkedin = (isset($data['linkedin'])) ? $data['linkedin'] : '';
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $instructors = new Instructors();
            $instructors->title = $data['title'];
            $instructors->en_title = $data['en_title'];
            $instructors->name = $data['name'];
            $instructors->en_name = $data['en_name'];
            $instructors->description = $data['description'];
            $instructors->en_description = $data['en_description'];
            $instructors->pic = $picName;
            $instructors->linkedin = $linkedin;
            $instructors->facebook = $facebook;
            $instructors->url = str_replace(' ','-',$data['url']);
            $instructors->published = $published;
            if ($published == 'yes') {
                $instructors->published_by = Auth::user()->id;
                $instructors->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $instructors->unpublished_by = Auth::user()->id;
                $instructors->unpublished_date = date("Y-m-d H:i:s");
            }
            $instructors->added_by = Auth::user()->id;
            $instructors->added_date = date("Y-m-d H:i:s");
            $instructors->lastedit_by = Auth::user()->id;
            $instructors->lastedit_date = date("Y-m-d H:i:s");
            if ($instructors->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.instructors'));
                return Redirect::to('admin/instructors/create');
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
        $instructor = Instructors::find($id);
        return view('auth.instructors.edit',compact('instructor'));
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
        $instructor = Instructors::findOrFail($id);
        $data = $request->input();
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules= array(
            'name' => 'required',
            'en_name' => 'required',
            'description' => 'required',
            'en_description' => 'required',
            'url' => "required|unique:mysql2.instractors,url,$id,id",
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
                $instructor->pic = $picName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $facebook = (isset($data['facebook'])) ? $data['facebook'] : '';
            $linkedin = (isset($data['linkedin'])) ? $data['linkedin'] : '';
            $instructor->title = $data['title'];
            $instructor->en_title = $data['en_title'];
            $instructor->name = $data['name'];
            $instructor->en_name = $data['en_name'];
            $instructor->description = $data['description'];
            $instructor->en_description = $data['en_description'];
            $instructor->linkedin = $linkedin;
            $instructor->facebook = $facebook;
            $old_url=$instructor->url;
            $instructor->url = str_replace(' ','-',$data['url']);
            if ($published == 'yes' && $instructor->published=='no') {
                $instructor->published_by = Auth::user()->id;
                $instructor->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $instructor->published=='yes') {
                $instructor->unpublished_by = Auth::user()->id;
                $instructor->unpublished_date = date("Y-m-d H:i:s");
            }
            $instructor->published = $published;
            $instructor->lastedit_by = Auth::user()->id;
            $instructor->lastedit_date = date("Y-m-d H:i:s");
            if ($instructor->save()) {
                if($old_url != $instructor->url){
                    saveOldUrl($id,'instractors',$old_url,$instructor->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.instructors'));
                return Redirect::to("admin/instructors/$instructor->id/edit");
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
        $instructor = Instructors::find($id);
        if (count($instructor)) {
            $instructor->delete();
        }
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $instructor = Instructors::find($id);
            if ($published == 'no') {
                $instructor->published = 'no';
                $instructor->unpublished_by = Auth::user()->id;
                $instructor->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $instructor->published = 'yes';
                $instructor->published_by = Auth::user()->id;
                $instructor->published_date = date("Y-m-d H:i:s");
            }
            $instructor->save();
        } else {
            return redirect(404);
        }
    }
}
