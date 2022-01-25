<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class StaticPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.static_pages.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $static_pages = StaticPage::select('static_pages_for_app.*', 'admin_users.username AS added_by_username', 'admin_users.id AS added_by_id')->leftJoin('admin_users', 'static_pages_for_app.added_by', '=', 'admin_users.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $static_pages = $static_pages->where('static_pages_for_app.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $static_pages = $static_pages->where('static_pages_for_app.name', 'LIKE', "%$name%");
        }
        if (isset($data['link']) && !empty($data['link'])) {
            $link = $data['link'];
            $static_pages = $static_pages->where('static_pages_for_app.link', 'LIKE', "%$link%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $static_pages = $static_pages->whereBetween('static_pages_for_app.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $static_pages->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'static_pages_for_app.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'static_pages_for_app.id';
                break;
            case 1:
                $columnName = 'static_pages_for_app.name';
                break;
            case 2:
                $columnName = 'static_pages_for_app.link';
                break;
            case 3:
                $columnName = 'static_pages_for_app.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $static_pages = $static_pages->where(function ($q) use ($search) {
                $q->where('static_pages_for_app.name', 'LIKE', "%$search%")
                    ->orWhere('static_pages_for_app.link', 'LIKE', "%$search%")
                    ->orWhere('static_pages_for_app.id', '=', $search);
            });
        }

        $static_pages = $static_pages->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($static_pages as $static_page) {
            $records["data"][] = [
                $static_page->id,
                $static_page->name,
                '<a href="' . $static_page->link . '" target="_blank">' . $static_page->link . '</a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $static_page->id . '" type="checkbox" ' . ((!PerUser('static_pages_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('static_pages_publish')) ? 'class="changeStatues"' : '') . ' ' . (($static_page->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $static_page->id . '">
                                    </label>
                                </div>
                            </td>',
                $static_page->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $static_page->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('static_pages_edit')) ? '<li>
                                            <a href="' . URL('admin/static_pages/' . $static_page->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('static_pages_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $static_page->id . '" >
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
        return view('auth.static_pages.add');
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
                'link' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $static_page = new StaticPage();
            $static_page->name = $data['name'];
            $static_page->link = $data['link'];
            $static_page->published = $published;
            $static_page->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $static_page->published_by = Auth::user()->id;
                $static_page->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $static_page->unpublished_by = Auth::user()->id;
                $static_page->unpublished_date = date("Y-m-d H:i:s");
            }
            $static_page->lastedit_by = Auth::user()->id;
            $static_page->added_by = Auth::user()->id;
            $static_page->lastedit_date = date("Y-m-d H:i:s");
            $static_page->added_date = date("Y-m-d H:i:s");
            if ($static_page->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.static_page'));
                return Redirect::to('admin/static_pages/create');
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
        $static_page = StaticPage::findOrFail($id);
        return view('auth.static_pages.edit', compact('static_page'));
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
        $static_page = StaticPage::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'link' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $static_page->name = $data['name'];
            $static_page->link = $data['link'];
            if ($published == 'yes' && $static_page->published=='no') {
                $static_page->published_by = Auth::user()->id;
                $static_page->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $static_page->published=='yes') {
                $static_page->unpublished_by = Auth::user()->id;
                $static_page->unpublished_date = date("Y-m-d H:i:s");
            }
            $static_page->published = $published;
            $static_page->lastedit_by = Auth::user()->id;
            $static_page->lastedit_date = date("Y-m-d H:i:s");
            if ($static_page->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.static_pages'));
                return Redirect::to("admin/static_pages/$static_page->id/edit");
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
        $static_page = StaticPage::findOrFail($id);
        $static_page->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $static_page = StaticPage::findOrFail($id);
            if ($published == 'no') {
                $static_page->published = 'no';
                $static_page->unpublished_by = Auth::user()->id;
                $static_page->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $static_page->published = 'yes';
                $static_page->published_by = Auth::user()->id;
                $static_page->published_date = date("Y-m-d H:i:s");
            }
            $static_page->save();
        } else {
            return redirect(404);
        }
    }
}
