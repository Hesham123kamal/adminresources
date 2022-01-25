<?php

namespace App\Http\Controllers\Admin;

use App\Event;
use App\Gallery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.gallery.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $galleries = Gallery::leftjoin('events','events.id','=','gallery.event_id')
                    ->select('gallery.*','events.name as event_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $galleries = $galleries->where('gallery.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $galleries = $galleries->where('gallery.name', 'LIKE', "%$name%");
        }
        if (isset($data['event']) && !empty($data['event'])) {
            $event = $data['event'];
            $galleries = $galleries->where('events.name', 'LIKE', "%$event%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $galleries = $galleries->whereBetween('gallery.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $galleries->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'gallery.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'gallery.id';
                break;
            case 1:
                $columnName = 'gallery.name';
                break;
            case 2:
                $columnName = 'gallery.createdtime';
                break;
            case 3:
                $columnName = 'events.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $galleries = $galleries->where(function ($q) use ($search) {
                $q->where('gallery.name', 'LIKE', "%$search%")
                    ->orWhere('gallery.id', '=', $search)
                    ->orWhere('events.name', 'LIKE', "%$search%");
            });
        }

        $galleries = $galleries->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($galleries as $gallery) {
            $gallery=makeDefaultImageGeneral($gallery,'image');
            $event_name = $gallery->event_name;
            if(PerUser('events_edit') && $event_name !=''){
                $event_name= '<a target="_blank" href="' . URL('admin/events/' . $gallery->event_id . '/edit') . '">' . $event_name . '</a>';
            }
            $records["data"][] = [
                $gallery->id,
                $gallery->name,
                $gallery->createdtime,
                $event_name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($gallery->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $gallery->id . '" type="checkbox" ' . ((!PerUser('gallery_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('gallery_publish')) ? 'class="changeStatues"' : '') . ' ' . (($gallery->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $gallery->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $gallery->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('gallery_edit')) ? '<li>
                                            <a href="' . URL('admin/gallery/' . $gallery->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('gallery_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $gallery->id . '" >
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
        $events = Event::pluck('name', 'id');
        return view('auth.gallery.add',compact('events'));
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
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'event' =>'required|exists:mysql2.events,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $gallery = new Gallery();
            $gallery->name = $data['name'];
            $gallery->event_id = $data['event'];
            $gallery->published = $published;
            $gallery->image = $picName;
            $gallery->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $gallery->published_by = Auth::user()->id;
                $gallery->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $gallery->unpublished_by = Auth::user()->id;
                $gallery->unpublished_date = date("Y-m-d H:i:s");
            }
            $gallery->lastedit_by = Auth::user()->id;
            $gallery->added_by = Auth::user()->id;
            $gallery->lastedit_date = date("Y-m-d H:i:s");
            if ($gallery->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.gallery'));
                return Redirect::to('admin/gallery/create');
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
        $gallery = Gallery::findOrFail($id);
        $events = Event::pluck('name', 'id');
        return view('auth.gallery.edit', compact('events','gallery'));
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
        $gallery = Gallery::findOrFail($id);
        $rules=array(
            'name' => 'required',
            'event' =>'required|exists:mysql2.events,id',
        );

        if ( $request->file('image')){
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $gallery->name = $data['name'];
            $gallery->event_id =  $data['event'];
            if ( $request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $gallery->image = $picName;
            }
            if ($published == 'yes' && $gallery->published=='no') {
                $gallery->published_by = Auth::user()->id;
                $gallery->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $gallery->published=='yes') {
                $gallery->unpublished_by = Auth::user()->id;
                $gallery->unpublished_date = date("Y-m-d H:i:s");
            }
            $gallery->published = $published;
            $gallery->lastedit_by = Auth::user()->id;
            $gallery->lastedit_date = date("Y-m-d H:i:s");
            if ($gallery->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.gallery'));
                return Redirect::to("admin/gallery/$gallery->id/edit");
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
        $gallery = Gallery::findOrFail($id);
        $gallery->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $gallery = Gallery::findOrFail($id);
            if ($published == 'no') {
                $gallery->published = 'no';
                $gallery->unpublished_by = Auth::user()->id;
                $gallery->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $gallery->published = 'yes';
                $gallery->published_by = Auth::user()->id;
                $gallery->published_date = date("Y-m-d H:i:s");
            }
            $gallery->save();
        } else {
            return redirect(404);
        }
    }
}
