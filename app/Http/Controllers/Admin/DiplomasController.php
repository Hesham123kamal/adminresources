<?php

namespace App\Http\Controllers\Admin;

use App\Diplomas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DiplomasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.diplomas.view');
    }


    function search(Request $request)
    {

        $data = $request->input();
        $diplomas = Diplomas::select('diplomas.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $diplomas = $diplomas->where('diplomas.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $diplomas = $diplomas->where('diplomas.name', 'LIKE', "%$name%");
        }
        if (isset($data['en_name']) && !empty($data['en_name'])) {
            $en_name = $data['en_name'];
            $diplomas = $diplomas->where('diplomas.en_name', 'LIKE', "%$en_name%");
        }
        if (isset($data['code']) && !empty($data['code'])) {
            $code = $data['code'];
            $diplomas = $diplomas->where('diplomas.code', 'LIKE', "%$code%");
        }
        if (isset($data['pic']) && !empty($data['pic'])) {
            $pic = $data['pic'];
            $diplomas = $diplomas->where('diplomas.image', 'LIKE', "%$pic%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $diplomas = $diplomas->where('diplomas.url', 'LIKE', "%$url%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $diplomas = $diplomas->whereBetween('diplomas.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }


        $iTotalRecords = $diplomas->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'diplomas.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'diplomas.id';
                break;
            case 1:
                $columnName = 'diplomas.name';
                break;
            case 2:
                $columnName = 'diplomas.en_name';
                break;
            case 3:
                $columnName = 'diplomas.code';
                break;
            case 4:
                $columnName = 'diplomas.pic';
                break;
            case 5:
                $columnName = 'diplomas.url';
                break;
            case 6:
                $columnName = 'diplomas.createdtime';
                break;

        }
        $search = $data['search']['value'];
        if ($search) {
            $diplomas = $diplomas->where(function ($q) use ($search) {
                $q->where('diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('diplomas.en_name', 'LIKE', "%$search%")
                    ->orWhere('diplomas.code', 'LIKE', "%$search%")
                    ->orWhere('diplomas.pic', 'LIKE', "%$search%")
                    ->orWhere('diplomas.description', 'LIKE', "%$search%")
                    ->orWhere('diplomas.url', 'LIKE', "%$search%")
                    ->orWhere('diplomas.id', '=', $search);
            });
        }

        $diplomas = $diplomas->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($diplomas as $diploma) {
            $diploma=makeDefaultImageGeneral($diploma,'image');

            $records["data"][] = [
                $diploma->id,
                $diploma->name,
                $diploma->en_name,
                $diploma->code,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img  style="width:70%;"  src="' . assetURL($diploma->image) . '"></a>',
                '<a href="' . e3mURL('diplomas/' . $diploma->url) . '" target="_blank">' . $diploma->url . '</a>',
                $diploma->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $diploma->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($diploma->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $diploma->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $diploma->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('diplomas_edit')) ? '<li>
                                            <a href="' . URL('admin/diplomas/' . $diploma->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diplomas_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $diploma->id . '" >
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
        return view('auth.diplomas.add');
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
                'pic' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'url' => 'required',
                'description' => 'required',
                'egy_sale_price' => 'required',
                'ksa_sale_price' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $diplomas = new Diplomas();
            $diplomas->name = $data['name'];
            $diplomas->en_name = $data['en_name'];
            $diplomas->code = $data['code'];
            $diplomas->egy_price = $data['egy_price'];
            $diplomas->ksa_price = $data['ksa_price'];
            $diplomas->description = $data['description'];
            $diplomas->sent = $data['sent'];
//            $diplomas->certificate_increment = $data['certificate_increment'];
            $diplomas->egy_sale_price = $data['egy_sale_price'];
            $diplomas->ksa_sale_price = $data['ksa_sale_price'];
            $diplomas->tool_eg_price = $data['tool_eg_price'];
            $diplomas->tool_ksa_price = $data['tool_ksa_price'];
            $diplomas->sort = $data['sort'];
            $diplomas->image = $picName;
            $diplomas->url = str_replace(' ','-',$data['url']);
            $diplomas->direction = $data['direction'];
            $diplomas->published = $active;
            if ($active == 'yes') {
                $diplomas->published_by = Auth::user()->id;
                $diplomas->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $diplomas->unpublished_by = Auth::user()->id;
                $diplomas->unpublished_date = date("Y-m-d H:i:s");
            }
            $diplomas->added_by = Auth::user()->id;
            $diplomas->added_date = date("Y-m-d H:i:s");
            $diplomas->lastedit_by = Auth::user()->id;
            $diplomas->lastedit_date = date("Y-m-d H:i:s");
            if ($diplomas->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.diplomas'));
                return Redirect::to('admin/diplomas/create');
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
        $diploma = Diplomas::find($id);
        return view('auth.diplomas.edit',compact('diploma'));
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
        $diploma = Diplomas::find($id);
        $rules=array(
            'name' => 'required',
            'en_name' => 'required',
            'code' => 'required',
            'url' => 'required',
            'description' => 'required',
            'egy_sale_price' => 'required',
            'ksa_sale_price' => 'required',
        );
        if ( $request->file('pic')){
            $rules['pic']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            if ( $request->file('pic')){
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $diploma->image = $picName;
            }
            $published = (isset($data['active'])) ? 'yes' : 'no';
            $diploma->name = $data['name'];
            $diploma->en_name = $data['en_name'];
            $diploma->code = $data['code'];
            $diploma->egy_price = $data['egy_price'];
            $diploma->ksa_price = $data['ksa_price'];
            $diploma->description = $data['description'];
            $diploma->sent = $data['sent'];
//            $diploma->certificate_increment = $data['certificate_increment'];
            $diploma->egy_sale_price = $data['egy_sale_price'];
            $diploma->ksa_sale_price = $data['ksa_sale_price'];
            $diploma->tool_eg_price = $data['tool_eg_price'];
            $diploma->tool_ksa_price = $data['tool_ksa_price'];
            $diploma->sort = $data['sort'];
            $old_url=$diploma->url;
            $diploma->url = str_replace(' ','-',$data['url']);
            $diploma->direction = $data['direction'];
            if ($published == 'yes' && $diploma->published=='no') {
                $diploma->published_by = Auth::user()->id;
                $diploma->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $diploma->published=='yes') {
                $diploma->unpublished_by = Auth::user()->id;
                $diploma->unpublished_date = date("Y-m-d H:i:s");
            }
            $diploma->published = $published;
            $diploma->lastedit_by = Auth::user()->id;
            $diploma->lastedit_date = date("Y-m-d H:i:s");
            if ($diploma->save()) {
                if($old_url != $diploma->url){
                    saveOldUrl($id,'diplomas',$old_url,$diploma->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.diplomas'));
                return Redirect::to("admin/diplomas/$diploma->id/edit");
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
        $diploma = Diplomas::find($id);
        if (count($diploma)) {
            $diploma->delete();
            $diploma->deleted_by = Auth::user()->id;
            $diploma->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $diploma = Diplomas::find($id);
            if ($active == 'no') {
                $diploma->published = 'no';
                $diploma->unpublished_by = Auth::user()->id;
                $diploma->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $diploma->published = 'yes';
                $diploma->published_by = Auth::user()->id;
                $diploma->published_date = date("Y-m-d H:i:s");
            }
            $diploma->save();
        } else {
            return redirect(404);
        }
    }
}
