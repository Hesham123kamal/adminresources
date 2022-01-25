<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.tags.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $tags = Tags::select('tags.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $tags = $tags->where('tags.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $tags = $tags->where('tags.name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $tags = $tags->whereBetween('tags.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $tags->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'tags.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'tags.id';
                break;
            case 1:
                $columnName = 'tags.name';
                break;
            case 2:
                $columnName = 'tags.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $tags = $tags->where(function ($q) use ($search) {
                $q->where('tags.name', 'LIKE', "%$search%")
                    ->orWhere('tags.id', '=', $search);
            });
        }
        $tags = $tags->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($tags as $tag) {
            $records["data"][] = [
                $tag->id,
                $tag->name,
                $tag->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $tag->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('tags_edit')) ? '<li>
                                            <a href="' . URL('admin/tags/' . $tag->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('tags_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $tag->id . '" >
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
        return view('auth.tags.add');
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
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $tag = new Tags();
            $tag->name = $data['name'];
            $tag->createdtime = date("Y-m-d H:i:s");
            $tag->lastedit_by = Auth::user()->id;
            $tag->added_by = Auth::user()->id;
            $tag->lastedit_date = date("Y-m-d H:i:s");
            $tag->added_date = date("Y-m-d H:i:s");
            if ($tag->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.tag'));
                return Redirect::to('admin/tags/create');
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
        $tag = Tags::findOrFail($id);
        return view('auth.tags.edit',compact('tag'));
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
        $tag = Tags::findOrFail($id);
        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $tag->name = $data['name'];
            $tag->lastedit_by = Auth::user()->id;
            $tag->lastedit_date = date("Y-m-d H:i:s");
            if ($tag->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.tag'));
                return Redirect::to("admin/tags/$tag->id/edit");
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
        $tag = Tags::findOrFail($id);
        $tag->deleted_at = date("Y-m-d H:i:s");
        $tag->deleted_by = Auth::user()->id;
        $tag->save();
    }
}
