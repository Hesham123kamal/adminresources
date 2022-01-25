<?php

namespace App\Http\Controllers\Admin;

use App\OldUrls;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class OldUrlsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.old_urls.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $urls = OldUrls::select('old_urls.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $urls = $urls->where('old_urls.id', '=', $id);
        }
        if (isset($data['table_name']) && !empty($data['table_name'])) {
            $table_name = $data['table_name'];
            $urls = $urls->where('old_urls.table_name', 'LIKE', "%$table_name%");
        }
        if (isset($data['old_url']) && !empty($data['old_url'])) {
            $url_url = $data['old_url'];
            $urls = $urls->where('old_urls.old_url', 'LIKE', "%$url_url%");
        }
        if (isset($data['new_url']) && !empty($data['new_url'])) {
            $new_url = $data['new_url'];
            $urls = $urls->where('old_urls.new_url', 'LIKE', "%$new_url%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $urls = $urls->whereBetween('old_urls.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $urls->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'old_urls.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'old_urls.id';
                break;
            case 1:
                $columnName = 'old_urls.table_name';
                break;
            case 2:
                $columnName = 'old_urls.old_url';
                break;
            case 3:
                $columnName = 'old_urls.new_url';
                break;
            case 4:
                $columnName = 'old_urls.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $urls = $urls->where(function ($q) use ($search) {
                $q->where('old_urls.table_name', 'LIKE', "%$search%")
                    ->orWhere('old_urls.id', '=', $search)
                    ->orWhere('old_urls.old_url', 'LIKE', "%$search%")
                    ->orWhere('old_urls.new_url', 'LIKE', "%$search%");
            });
        }

        $urls = $urls->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($urls as $url) {
            $records["data"][] = [
                $url->id,
                $url->table_name,
                $url->old_url,
                $url->new_url,
                $url->createtime,
                ($url->can_delete?'<div class="btn-group text-center" id="single-order-' . $url->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('old_urls_edit')) ? '<li>
                                            <a href="' . URL('admin/old_urls/' . $url->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('old_urls_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $url->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '


                                    </ul>
                                </div>':''),
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
        return view('auth.old_urls.add');
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
                'table_name' => 'required',
                'old_url' => 'required',
                'new_url' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $url = new OldUrls();
            $url->table_name = $data['table_name'];
            $url->old_url = str_replace(' ', '-', $data['old_url']);
            $url->new_url = str_replace(' ', '-', $data['new_url']);
            $url->can_delete = 1;
            $url->createtime = date("Y-m-d H:i:s");
            $url->lastedit_date = date("Y-m-d H:i:s");
            $url->add_by = Auth::user()->id;
            $url->lastedit_by = Auth::user()->id;
            if ($url->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.old_url'));
                return Redirect::to('admin/old_urls/create');
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
        $url = OldUrls::where('can_delete', 1)->findOrFail($id);
        return view('auth.old_urls.edit',compact('url'));
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
        $url = OldUrls::where('can_delete', 1)->findOrFail($id);
        $rules=array(
            'table_name' => 'required',
            'old_url' => 'required',
            'new_url' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $url->table_name = $data['table_name'];
            $url->old_url = str_replace(' ', '-', $data['old_url']);
            $url->new_url = str_replace(' ', '-', $data['new_url']);
            $url->lastedit_by = Auth::user()->id;
            $url->lastedit_date = date("Y-m-d H:i:s");
            if ($url->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.url'));
                return Redirect::to("admin/old_urls/$url->id/edit");
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
        $url = OldUrls::where('can_delete', 1)->findOrFail($id);
        $url->delete();
    }
}
