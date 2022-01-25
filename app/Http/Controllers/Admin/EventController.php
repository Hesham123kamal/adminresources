<?php

namespace App\Http\Controllers\Admin;

use App\Event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.events.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $events = Event::select('events.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $events = $events->where('events.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $events = $events->where('events.name', 'LIKE', "%$name%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $events = $events->where('events.url', 'LIKE', "%$url%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $events = $events->whereBetween('events.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $events->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'events.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'events.id';
                break;
            case 1:
                $columnName = 'events.name';
                break;
            case 2:
                $columnName = 'events.url';
                break;
            case 3:
                $columnName = 'events.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $events = $events->where(function ($q) use ($search) {
                $q->where('events.name', 'LIKE', "%$search%")
                    ->orWhere('events.url', 'LIKE', "%$search%")
                    ->orWhere('events.id', '=', $search);
            });
        }

        $events = $events->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($events as $event) {
            $event=makeDefaultImageGeneral($event,'image');
            $records["data"][] = [
                $event->id,
                $event->name,
                '<a href="' . e3mURL('events/' . $event->url) . '" target="_blank">' . $event->url . '</a>',
                $event->createdtime,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($event->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $event->id . '" type="checkbox" ' . ((!PerUser('events_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('events_publish')) ? 'class="changeStatues"' : '') . ' ' . (($event->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $event->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $event->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('events_edit')) ? '<li>
                                            <a href="' . URL('admin/events/' . $event->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('events_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $event->id . '" >
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
        return view('auth.events.add');
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
                'description' => 'required',
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'url' => 'required|unique:mysql2.events,url',
                'eventdate' => 'required|date_format:"Y-m-d"'
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $event = new Event();
            $event->name = $data['name'];
            $event->description = $data['description'];
            $event->url = str_replace(' ','-',$data['url']);
            $event->published = $published;
            $event->image = $picName;
            $event->eventdate = $data['eventdate'];
            $event->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $event->published_by = Auth::user()->id;
                $event->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $event->unpublished_by = Auth::user()->id;
                $event->unpublished_date = date("Y-m-d H:i:s");
            }
            $event->lastedit_by = Auth::user()->id;
            $event->added_by = Auth::user()->id;
            $event->lastedit_date = date("Y-m-d H:i:s");
            $event->added_date = date("Y-m-d H:i:s");
            if ($event->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.event'));
                return Redirect::to('admin/events/create');
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
        $event = Event::findOrFail($id);
        $event->eventdate = date("Y-m-d", strtotime($event->eventdate));
        return view('auth.events.edit', compact('event'));
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
        $event = Event::findOrFail($id);
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules = array(
            'name' => 'required',
            'description' => 'required',
            'url' => "required|unique:mysql2.events,url,$id,id",
            'eventdate' => 'required|date_format:"Y-m-d"'
        );

        if ($request->file('image')) {
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $event->name = $data['name'];
            $old_url=$event->url;
            $event->url = str_replace(' ', '-', $data['url']);
            $event->description = $data['description'];
            $event->eventdate = $data['eventdate'];
            if ($request->file('image')) {
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $event->image = $picName;
            }
            if ($published == 'yes' && $event->published=='no') {
                $event->published_by = Auth::user()->id;
                $event->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $event->published=='yes') {
                $event->unpublished_by = Auth::user()->id;
                $event->unpublished_date = date("Y-m-d H:i:s");
            }
            $event->published = $published;
            $event->lastedit_by = Auth::user()->id;
            $event->lastedit_date = date("Y-m-d H:i:s");
            if ($event->save()) {
                if($old_url != $event->url){
                    saveOldUrl($id,'events',$old_url,$event->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.event'));
                return Redirect::to("admin/events/$event->id/edit");
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
        $event = Event::findOrFail($id);
        $event->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $event = Event::findOrFail($id);
            if ($published == 'no') {
                $event->published = 'no';
                $event->unpublished_by = Auth::user()->id;
                $event->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $event->published = 'yes';
                $event->published_by = Auth::user()->id;
                $event->published_date = date("Y-m-d H:i:s");
            }
            $event->save();
        } else {
            return redirect(404);
        }
    }
}
