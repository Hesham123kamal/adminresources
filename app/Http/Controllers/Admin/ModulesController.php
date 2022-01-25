<?php

namespace App\Http\Controllers\Admin;

use App\Mba;
use App\Modules;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.modules.view');
    }


    function search(Request $request)
    {

        $data = $request->input();
        $modules = Mba::select('mba.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $modules = $modules->where('mba.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $modules = $modules->where('mba.name', 'LIKE', "%$name%");
        }
        if (isset($data['en_name']) && !empty($data['en_name'])) {
            $en_name = $data['en_name'];
            $modules = $modules->where('mba.en_name', 'LIKE', "%$en_name%");
        }
        if (isset($data['code']) && !empty($data['code'])) {
            $code = $data['code'];
            $modules = $modules->where('mba.code', 'LIKE', "%$code%");
        }
        if (isset($data['questions_numbers']) && !empty($data['questions_numbers'])) {
            $questions_numbers = $data['questions_numbers'];
            $modules = $modules->where('mba.questions_numbers', 'LIKE', "%$questions_numbers%");
        }
        if (isset($data['pic']) && !empty($data['pic'])) {
            $pic = $data['pic'];
            $modules = $modules->where('modules.image', 'LIKE', "%$pic%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $modules = $modules->where('mba.url', 'LIKE', "%$url%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $modules = $modules->whereBetween('mba.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }


        $iTotalRecords = $modules->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'mba.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mba.id';
                break;
            case 1:
                $columnName = 'mba.name';
                break;
            case 2:
                $columnName = 'mba.en_name';
                break;
            case 3:
                $columnName = 'mba.code';
                break;
            case 4:
                $columnName = 'mba.questions_numbers';
                break;
            case 5:
                $columnName = 'mba.pic';
                break;
            case 6:
                $columnName = 'mba.url';
                break;
            case 7:
                $columnName = 'mba.sort';
                break;
            case 8:
                $columnName = 'mba.createdtime';
                break;

        }
        $search = $data['search']['value'];
        if ($search) {
            $modules = $modules->where(function ($q) use ($search) {
                $q->where('mba.name', 'LIKE', "%$search%")
                    ->orWhere('mba.en_name', 'LIKE', "%$search%")
                    ->orWhere('mba.code', 'LIKE', "%$search%")
                    ->orWhere('mba.questions_numbers', 'LIKE', "%$search%")
                    ->orWhere('mba.pic', 'LIKE', "%$search%")
                    ->orWhere('mba.description', 'LIKE', "%$search%")
                    ->orWhere('mba.url', 'LIKE', "%$search%")
                    ->orWhere('mba.id', '=', $search);
            });
        }

        $modules = $modules->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($modules as $module) {
            $module=makeDefaultImageGeneral($module,'image');
            $records["data"][] = [
                $module->id,
                $module->name,
                $module->en_name,
                $module->code,
                $module->questions_numbers,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img  style="width:100%;"  src="' . assetURL($module->image) . '"></a>',
                '<a href="' . e3mURL('mba/' . $module->url) . '" target="_blank">' . $module->url . '</a>',
                $module->sort,
                $module->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $module->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($module->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $module->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $module->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('modules_edit')) ? '<li>
                                            <a href="' . URL('admin/modules/' . $module->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('modules_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $module->id . '" >
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
        return view('auth.modules.add');
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
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'en_name' => 'required',
                'code' => 'required',
                'pic' => 'required',
                'url' => 'required',
                'description' => 'required',
                'egy_sale_price' => 'required',
                'ksa_sale_price' => 'required',
                'question_time' => 'required|numeric',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $active = (isset($data['active'])) ? 1 : 0;
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $modules = new Modules();
            $modules->name = $data['name'];
            $modules->en_name = $data['en_name'];
            $modules->code = $data['code'];
            $modules->questions_numbers = $data['questions_numbers'];
            $modules->egy_price = $data['egy_price'];
            $modules->ksa_price = $data['ksa_price'];
            $modules->question_time = $data['question_time'];
            $modules->short_description = $data['short_description'];
            $modules->description = $data['description'];
            $modules->sent = $data['sent'];
            $modules->certificate_increment = $data['certificate_increment'];
            $modules->egy_sale_price = $data['egy_sale_price'];
            $modules->ksa_sale_price = $data['ksa_sale_price'];
            $modules->tool_eg_price = $data['tool_eg_price'];
            $modules->tool_ksa_price = $data['tool_ksa_price'];
            $modules->sort = $data['sort'];
            $modules->intro_video = $data['intro_video'];
            $modules->image = $picName;
            $modules->url = str_replace(' ','-',$data['url']);
            $modules->published = $active;
            if ($active == 'yes') {
                $modules->published_by = Auth::user()->id;
                $modules->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $modules->unpublished_by = Auth::user()->id;
                $modules->unpublished_date = date("Y-m-d H:i:s");
            }
            $modules->added_by = Auth::user()->id;
            $modules->added_date = date("Y-m-d H:i:s");
            $modules->lastedit_by = Auth::user()->id;
            $modules->lastedit_date = date("Y-m-d H:i:s");
            if ($modules->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules'));
                return Redirect::to('admin/modules/create');
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
        $module = Mba::find($id);
        return view('auth.modules.edit',compact('module'));
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
        $module = Mba::find($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'en_name' => 'required',
                'code' => 'required',
                'url' => 'required',
                'description' => 'required',
                'egy_sale_price' => 'required',
                'ksa_sale_price' => 'required',
                'question_time' => 'required|numeric',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            if ( $request->file('pic')){
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $module->image = $picName;
            }
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $module->name = $data['name'];
            $module->en_name = $data['en_name'];
            $module->code = $data['code'];
            $module->questions_numbers = $data['questions_numbers'];
            $module->egy_price = $data['egy_price'];
            $module->ksa_price = $data['ksa_price'];
            $module->short_description = $data['short_description'];
            $module->description = $data['description'];
            $module->sent = $data['sent'];
            $module->certificate_increment = $data['certificate_increment'];
            $module->egy_sale_price = $data['egy_sale_price'];
            $module->ksa_sale_price = $data['ksa_sale_price'];
            $module->tool_eg_price = $data['tool_eg_price'];
            $module->tool_ksa_price = $data['tool_ksa_price'];
            $module->sort = $data['sort'];
            $module->intro_video = $data['intro_video'];
            $module->question_time = $data['question_time'];
            $old_url=$module->url;
            $module->url = str_replace(' ','-',$data['url']);
            if ($active == 'yes' && $module->published=='no') {
                $module->published_by = Auth::user()->id;
                $module->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no' && $module->published=='yes') {
                $module->unpublished_by = Auth::user()->id;
                $module->unpublished_date = date("Y-m-d H:i:s");
            }
            $module->published = $active;
            $module->lastedit_by = Auth::user()->id;
            $module->lastedit_date = date("Y-m-d H:i:s");
            if ($module->save()) {
                if($old_url != $module->url){
                    saveOldUrl($id,'mba',$old_url,$module->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.modules'));
                return Redirect::to("admin/modules/$module->id/edit");
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
        $module = Mba::find($id);
        if (count($module)) {
            $module->delete();
            $module->deleted_by = Auth::user()->id;
            $module->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $module = Mba::find($id);
            if ($active == 'no') {
                $module->published = 'no';
                $module->unpublished_by = Auth::user()->id;
                $module->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $module->published = 'yes';
                $module->published_by = Auth::user()->id;
                $module->published_date = date("Y-m-d H:i:s");
            }
            $module->save();
        } else {
            return redirect(404);
        }
    }
}
