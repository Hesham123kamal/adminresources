<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\InitiativeSections;
use App\InitiativeVideos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class InitiativeVideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections=InitiativeSections::pluck('title', 'id');
        return view('auth.initiative_videos.view',compact('sections'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $initiative_videos = InitiativeVideos::join('initiative_sections','initiative_sections.id','=','initiative_videos.section_id')
            ->select('initiative_videos.*','initiative_sections.title as section_title');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $initiative_videos = $initiative_videos->where('initiative_videos.id', '=', "$id");
        }
        if (isset($data['title']) && !empty($data['title'])) {
            $title = $data['title'];
            $initiative_videos = $initiative_videos->where('initiative_videos.title', 'LIKE', "%$title%");
        }
        if (isset($data['link']) && !empty($data['link'])) {
            $link = $data['link'];
            $initiative_videos = $initiative_videos->where('initiative_videos.link', 'LIKE', "%$link%");
        }
        if (isset($data['section']) && !empty($data['section'])) {
            $section = $data['section'];
            $initiative_videos = $initiative_videos->where('initiative_videos.section_id', '=', "$section");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $initiative_videos = $initiative_videos->whereBetween('initiative_videos.added_date', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $initiative_videos->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'initiative_videos.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'initiative_videos.id';
                break;
            case 1:
                $columnName = 'initiative_videos.title';
                break;
            case 2:
                $columnName = 'initiative_videos.link';
                break;
            case 3:
                $columnName = 'initiative_sections.title';
                break;
            case 4:
                $columnName = 'initiative_videos.added_date';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $initiative_videos = $initiative_videos->where(function ($q) use ($search) {
                $q->where('initiative_videos.title', 'LIKE', "%$search%")
                    ->orWhere('initiative_videos.link', 'LIKE', "%$search%")
                    ->orWhere('initiative_sections.title', 'LIKE', "%$search%")
                    ->orWhere('initiative_videos.id', '=', $search);
            });
        }

        $initiative_videos = $initiative_videos->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($initiative_videos as $initiative_video) {
            $section=$initiative_video->section_title;
            if(PerUser('initiative_sections_edit') && $section !=''){
                $section= '<a target="_blank" href="' . URL('admin/initiative_sections/' . $initiative_video->section_id . '/edit') . '">' . $section . '</a>';
            }
            $records["data"][] = [
                $initiative_video->id,
                $initiative_video->title,
                $initiative_video->link,
                $section,
                $initiative_video->added_date,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $initiative_video->id . '" type="checkbox" ' . ((!PerUser('initiative_videos_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('initiative_videos_publish')) ? 'class="changeStatues"' : '') . ' ' . (($initiative_video->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $initiative_video->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $initiative_video->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('initiative_videos_edit')) ? '<li>
                                            <a href="' . URL('admin/initiative_videos/' . $initiative_video->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('initiative_videos_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $initiative_video->id . '" >
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
        $sections=InitiativeSections::pluck('title', 'id');
        return view('auth.initiative_videos.add',compact('sections'));
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
                'link' => 'required',
                'section' => 'required|exists:mysql2.initiative_sections,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $initiative_video = new InitiativeVideos();
            $initiative_video->title = $data['title'];
            $initiative_video->link = $data['link'];
            $initiative_video->section_id = $data['section'];
            $initiative_video->published = $published;
            if ($published == 'yes') {
                $initiative_video->published_by = Auth::user()->id;
                $initiative_video->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $initiative_video->unpublished_by = Auth::user()->id;
                $initiative_video->unpublished_date = date("Y-m-d H:i:s");
            }
            $initiative_video->added_by = Auth::user()->id;
            $initiative_video->added_date = date("Y-m-d H:i:s");
            $initiative_video->lastedit_by = Auth::user()->id;
            $initiative_video->lastedit_date = date("Y-m-d H:i:s");
            if ($initiative_video->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.initiative_video'));
                return Redirect::to('admin/initiative_videos/create');
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
        $initiative_video = InitiativeVideos::findOrFail($id);
        $sections=InitiativeSections::pluck('title', 'id');
        return view('auth.initiative_videos.edit',compact('initiative_video','sections'));
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
        $initiative_video = InitiativeVideos::findOrFail($id);
        $data = $request->input();
        $validator = Validator::make($request->all(),array(
            'title' => 'required',
            'link' => 'required',
            'section' => 'required|exists:mysql2.initiative_sections,id',
        ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $initiative_video->title = $data['title'];
            $initiative_video->link = $data['link'];
            $initiative_video->section_id = $data['section'];
            if ($published == 'yes' && $initiative_video->published=='no') {
                $initiative_video->published_by = Auth::user()->id;
                $initiative_video->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $initiative_video->published=='yes') {
                $initiative_video->unpublished_by = Auth::user()->id;
                $initiative_video->unpublished_date = date("Y-m-d H:i:s");
            }
            $initiative_video->published = $published;
            $initiative_video->lastedit_by = Auth::user()->id;
            $initiative_video->lastedit_date = date("Y-m-d H:i:s");
            if ($initiative_video->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.initiative_video'));
                return Redirect::to("admin/initiative_videos/$initiative_video->id/edit");
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
        $initiative_video = InitiativeVideos::findOrFail($id);
        if ($initiative_video) {
            $initiative_video->deleted_at=date("Y-m-d H:i:s");
            $initiative_video->deleted_by = Auth::user()->id;
            $initiative_video->save();
        }
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $initiative_video = InitiativeVideos::findOrFail($id);
            if ($published == 'no') {
                $initiative_video->published = 'no';
                $initiative_video->unpublished_by = Auth::user()->id;
                $initiative_video->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $initiative_video->published = 'yes';
                $initiative_video->published_by = Auth::user()->id;
                $initiative_video->published_date = date("Y-m-d H:i:s");
            }
            $initiative_video->save();
        } else {
            return redirect(404);
        }
    }
}
