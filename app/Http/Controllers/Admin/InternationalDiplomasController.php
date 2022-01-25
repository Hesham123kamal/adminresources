<?php

namespace App\Http\Controllers\Admin;

use App\InternationalCategories;
use App\InternationalDiplomas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class InternationalDiplomasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories=InternationalCategories::get();
        return view('auth.international_diplomas.view',compact('categories'));
    }


    function search(Request $request)
    {

        $data = $request->input();
        $international_diplomas = InternationalDiplomas::join('international_categories','international_categories.id','=','international_diplomas.category_id')
            ->select('international_diplomas.*','international_categories.name as category_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $international_diplomas = $international_diplomas->where('international_diplomas.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $international_diplomas = $international_diplomas->where('international_diplomas.name', 'LIKE', "%$name%");
        }
        if (isset($data['en_name']) && !empty($data['en_name'])) {
            $en_name = $data['en_name'];
            $international_diplomas = $international_diplomas->where('international_diplomas.en_name', 'LIKE', "%$en_name%");
        }
        if (isset($data['code']) && !empty($data['code'])) {
            $code = $data['code'];
            $international_diplomas = $international_diplomas->where('international_diplomas.code', 'LIKE', "%$code%");
        }
        if (isset($data['pic']) && !empty($data['pic'])) {
            $pic = $data['pic'];
            $international_diplomas = $international_diplomas->where('international_diplomas.image', 'LIKE', "%$pic%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $international_diplomas = $international_diplomas->where('international_diplomas.url', 'LIKE', "%$url%");
        }
        if (isset($data['category']) && !empty($data['category'])) {
            $category = $data['category'];
            $international_diplomas = $international_diplomas->where('international_diplomas.category_id', '=', $category);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $international_diplomas = $international_diplomas->whereBetween('international_diplomas.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }


        $iTotalRecords = $international_diplomas->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'international_diplomas.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'international_diplomas.id';
                break;
            case 1:
                $columnName = 'international_diplomas.name';
                break;
            case 2:
                $columnName = 'international_diplomas.en_name';
                break;
            case 3:
                $columnName = 'international_diplomas.code';
                break;
            case 4:
                $columnName = 'international_diplomas.pic';
                break;
            case 5:
                $columnName = 'international_diplomas.url';
                break;
            case 6:
                $columnName = 'international_diplomas.createdtime';
                break;
            case 7:
                $columnName = 'international_categories.name';
                break;

        }
        $search = $data['search']['value'];
        if ($search) {
            $international_diplomas = $international_diplomas->where(function ($q) use ($search) {
                $q->where('international_diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas.en_name', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas.code', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas.pic', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas.description', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas.url', 'LIKE', "%$search%")
                    ->orWhere('international_categories.name', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas.id', '=', $search);
            });
        }

        $international_diplomas = $international_diplomas->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($international_diplomas as $international_diploma) {
            $international_diploma=makeDefaultImageGeneral($international_diploma,'image');
            $category=$international_diploma->category_name;
            if(PerUser('international_categories_edit') && $category !=''){
                $category= '<a target="_blank" href="' . URL('admin/international_categories/' . $international_diploma->category_id . '/edit') . '">' . $category . '</a>';
            }
            $records["data"][] = [
                $international_diploma->id,
                $international_diploma->name,
                $international_diploma->en_name,
                $international_diploma->code,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img  style="width:70%;"  src="' . assetURL($international_diploma->image) . '"></a>',
                '<a href="' . e3mURL('international_diplomas/' . $international_diploma->url) . '" target="_blank">' . $international_diploma->url . '</a>',
                $international_diploma->createdtime,
                $category,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $international_diploma->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($international_diploma->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $international_diploma->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $international_diploma->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('international_diplomas_edit')) ? '<li>
                                            <a href="' . URL('admin/international_diplomas/' . $international_diploma->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('international_diplomas_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $international_diploma->id . '" >
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
        $categories=InternationalCategories::get();
        return view('auth.international_diplomas.add',compact('categories'));
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
                'category' => 'required|exists:mysql2.international_categories,id',
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
            $international_diplomas = new InternationalDiplomas();
            $international_diplomas->name = $data['name'];
            $international_diplomas->category_id = $data['category'];
            $international_diplomas->en_name = $data['en_name'];
            $international_diplomas->code = $data['code'];
            $international_diplomas->egy_price = $data['egy_price'];
            $international_diplomas->ksa_price = $data['ksa_price'];
            $international_diplomas->description = $data['description'];
            $international_diplomas->sent = $data['sent'];
            $international_diplomas->certificate_increment = $data['certificate_increment'];
            $international_diplomas->egy_sale_price = $data['egy_sale_price'];
            $international_diplomas->ksa_sale_price = $data['ksa_sale_price'];
            $international_diplomas->tool_eg_price = $data['tool_eg_price'];
            $international_diplomas->tool_ksa_price = $data['tool_ksa_price'];
            $international_diplomas->sort = $data['sort'];
            $international_diplomas->image = $picName;
            $international_diplomas->url = str_replace(' ','-',$data['url']);
            $international_diplomas->direction = $data['direction'];
            $international_diplomas->published = $active;
            if ($active == 'yes') {
                $international_diplomas->published_by = Auth::user()->id;
                $international_diplomas->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $international_diplomas->unpublished_by = Auth::user()->id;
                $international_diplomas->unpublished_date = date("Y-m-d H:i:s");
            }
            $international_diplomas->added_by = Auth::user()->id;
            $international_diplomas->added_date = date("Y-m-d H:i:s");
            $international_diplomas->lastedit_by = Auth::user()->id;
            $international_diplomas->lastedit_date = date("Y-m-d H:i:s");
            if ($international_diplomas->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.international_diplomas'));
                return Redirect::to('admin/international_diplomas/create');
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
        $international_diploma = InternationalDiplomas::find($id);
        $categories=InternationalCategories::get();
        return view('auth.international_diplomas.edit',compact('international_diploma','categories'));
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
        $international_diploma = InternationalDiplomas::find($id);
        $rules=array(
            'name' => 'required',
            'category' => 'required|exists:mysql2.international_categories,id',
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
                $international_diploma->image = $picName;
            }
            $published = (isset($data['active'])) ? 'yes' : 'no';
            $international_diploma->name = $data['name'];
            $international_diploma->category_id = $data['category'];
            $international_diploma->en_name = $data['en_name'];
            $international_diploma->code = $data['code'];
            $international_diploma->egy_price = $data['egy_price'];
            $international_diploma->ksa_price = $data['ksa_price'];
            $international_diploma->description = $data['description'];
            $international_diploma->sent = $data['sent'];
            $international_diploma->certificate_increment = $data['certificate_increment'];
            $international_diploma->egy_sale_price = $data['egy_sale_price'];
            $international_diploma->ksa_sale_price = $data['ksa_sale_price'];
            $international_diploma->tool_eg_price = $data['tool_eg_price'];
            $international_diploma->tool_ksa_price = $data['tool_ksa_price'];
            $international_diploma->sort = $data['sort'];
            $old_url=$international_diploma->url;
            $international_diploma->url = str_replace(' ','-',$data['url']);
            $international_diploma->direction = $data['direction'];
            if ($published == 'yes' && $international_diploma->published=='no') {
                $international_diploma->published_by = Auth::user()->id;
                $international_diploma->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $international_diploma->published=='yes') {
                $international_diploma->unpublished_by = Auth::user()->id;
                $international_diploma->unpublished_date = date("Y-m-d H:i:s");
            }
            $international_diploma->published = $published;
            $international_diploma->lastedit_by = Auth::user()->id;
            $international_diploma->lastedit_date = date("Y-m-d H:i:s");
            if ($international_diploma->save()) {
                if($old_url != $international_diploma->url){
                    saveOldUrl($id,'international_diplomas',$old_url,$international_diploma->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.international_diplomas'));
                return Redirect::to("admin/international_diplomas/$international_diploma->id/edit");
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
        $international_diploma = InternationalDiplomas::find($id);
        if (count($international_diploma)) {
            $international_diploma->delete();
            $international_diploma->deleted_by = Auth::user()->id;
            $international_diploma->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $international_diploma = InternationalDiplomas::find($id);
            if ($active == 'no') {
                $international_diploma->published = 'no';
                $international_diploma->unpublished_by = Auth::user()->id;
                $international_diploma->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $international_diploma->published = 'yes';
                $international_diploma->published_by = Auth::user()->id;
                $international_diploma->published_date = date("Y-m-d H:i:s");
            }
            $international_diploma->save();
        } else {
            return redirect(404);
        }
    }
}
