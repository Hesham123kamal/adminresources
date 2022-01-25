<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\InitiativeSections;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class InitiativeSectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.initiative_sections.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $initiative_sections = InitiativeSections::select('initiative_sections.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $initiative_sections = $initiative_sections->where('initiative_sections.id', '=', "$id");
        }
        if (isset($data['title']) && !empty($data['title'])) {
            $title = $data['title'];
            $initiative_sections = $initiative_sections->where('initiative_sections.title', 'LIKE', "%$title%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $initiative_sections = $initiative_sections->whereBetween('initiative_sections.added_date', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $initiative_sections->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'initiative_sections.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'initiative_sections.id';
                break;
            case 1:
                $columnName = 'initiative_sections.title';
                break;
            case 3:
                $columnName = 'initiative_sections.added_date';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $initiative_sections = $initiative_sections->where(function ($q) use ($search) {
                $q->where('initiative_sections.title', 'LIKE', "%$search%")
                    ->orWhere('initiative_sections.id', '=', $search);
            });
        }

        $initiative_sections = $initiative_sections->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($initiative_sections as $initiative_section) {
            $initiative_section=makeDefaultImageGeneral($initiative_section,'image');
            $records["data"][] = [
                $initiative_section->id,
                $initiative_section->title,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img  style="width:100%;"  src="' . assetURL($initiative_section->image) . '"></a>',
                $initiative_section->added_date,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $initiative_section->id . '" type="checkbox" ' . ((!PerUser('initiative_sections_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('initiative_sections_publish')) ? 'class="changeStatues"' : '') . ' ' . (($initiative_section->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $initiative_section->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $initiative_section->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('initiative_sections_edit')) ? '<li>
                                            <a href="' . URL('admin/initiative_sections/' . $initiative_section->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('initiative_sections_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $initiative_section->id . '" >
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
        return view('auth.initiative_sections.add');
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
                'title' => 'required',
                'meta_title' => 'required',
                'meta_description' => 'required',
                'description' => 'required',
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $image = $request->file('image');
            $imageName = uploadFileToE3melbusiness($image);
            $initiative_section = new InitiativeSections();
            $initiative_section->title = $data['title'];
            $initiative_section->description = $data['description'];
            $initiative_section->meta_title = $data['meta_title'];
            $initiative_section->meta_description = $data['meta_description'];
            $initiative_section->image = $imageName;
            $initiative_section->published = $published;
            if ($published == 'yes') {
                $initiative_section->published_by = Auth::user()->id;
                $initiative_section->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $initiative_section->unpublished_by = Auth::user()->id;
                $initiative_section->unpublished_date = date("Y-m-d H:i:s");
            }
            $initiative_section->added_by = Auth::user()->id;
            $initiative_section->added_date = date("Y-m-d H:i:s");
            $initiative_section->lastedit_by = Auth::user()->id;
            $initiative_section->lastedit_date = date("Y-m-d H:i:s");
            if ($initiative_section->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.initiative_section'));
                return Redirect::to('admin/initiative_sections/create');
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
        $initiative_section = InitiativeSections::findOrFail($id);
        return view('auth.initiative_sections.edit',compact('initiative_section'));
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
        $initiative_section = InitiativeSections::findOrFail($id);
        $data = $request->input();
        $rules= array(
            'title' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'description' => 'required',
        );
        if ( $request->file('image')){
            $rules['image']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if ( $request->file('image')){
                $image = $request->file('image');
                $imageName = uploadFileToE3melbusiness($image);
                $initiative_section->image = $imageName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $initiative_section->title = $data['title'];
            $initiative_section->description = $data['description'];
            $initiative_section->meta_title = $data['meta_title'];
            $initiative_section->meta_description = $data['meta_description'];
            if ($published == 'yes' && $initiative_section->published=='no') {
                $initiative_section->published_by = Auth::user()->id;
                $initiative_section->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $initiative_section->published=='yes') {
                $initiative_section->unpublished_by = Auth::user()->id;
                $initiative_section->unpublished_date = date("Y-m-d H:i:s");
            }
            $initiative_section->published = $published;
            $initiative_section->lastedit_by = Auth::user()->id;
            $initiative_section->lastedit_date = date("Y-m-d H:i:s");
            if ($initiative_section->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.initiative_section'));
                return Redirect::to("admin/initiative_sections/$initiative_section->id/edit");
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
        $initiative_section = InitiativeSections::findOrFail($id);
        if ($initiative_section) {
            $initiative_section->deleted_at=date("Y-m-d H:i:s");
            $initiative_section->deleted_by = Auth::user()->id;
            $initiative_section->save();
        }
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $initiative_section = InitiativeSections::findOrFail($id);
            if ($published == 'no') {
                $initiative_section->published = 'no';
                $initiative_section->unpublished_by = Auth::user()->id;
                $initiative_section->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $initiative_section->published = 'yes';
                $initiative_section->published_by = Auth::user()->id;
                $initiative_section->published_date = date("Y-m-d H:i:s");
            }
            $initiative_section->save();
        } else {
            return redirect(404);
        }
    }
}
