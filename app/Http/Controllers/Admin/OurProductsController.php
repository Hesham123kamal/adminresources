<?php

namespace App\Http\Controllers\Admin;

use App\Profiles;
use App\OurProducts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Builder\Use_;

class OurProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return view('auth.our_products.view');

    }
    public function getOurProductsAJAX(Request $request){
        $data=$request->input();
        $our_products=OurProducts::select('our_products.*');
        if(isset($data['name'])&&!empty($data['name'])){
            $name=$data['name'];
            $our_products=$our_products->where('name','LIKE',"%$name%");
        }
        if(isset($data['url'])&&!empty($data['url'])){
            $url=$data['url'];
            $our_products=$our_products->where('url','LIKE',"%$url%");
        }
        if(isset($data['sort'])&&!empty($data['sort'])){
            $sort=$data['sort'];
            $our_products=$our_products->where('sort','=',$sort);
        }
        $iTotalRecords=$our_products->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'our_products.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'our_products.id';
                break;
            case 1:
                $columnName = 'our_products.name';
                break;
            case 3:
                $columnName = 'our_products.url';
                break;
            case 4:
                $columnName = 'our_products.sort';
                break;
        }
        $search = $data['search']['value'];
        if($search){
            $our_products=$our_products->where(function($q)use($search){
                $q->where('name','LIKE',"%$search%")
                    ->orWhere('url','LIKE',"%$search%")
                    ->orWhere('description','LIKE',"%$search%")
                    ->orWhere('sort','LIKE',"%$search%");
            });
        }
        $our_products=$our_products->orderBy($columnName,$data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($our_products as $product) {
            $product=makeDefaultImageGeneral($product,'image');
            $records["data"][] = [
                $product->id,
                $product->name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img style="width:50%;" src="'.assetURL($product->image).'"></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $product->id . '" type="checkbox" ' . ((!PerUser('our_products_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('our_products_publish')) ? 'class="changeStatues"' : '') . ' ' . (($product->active==1) ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $product->id . '">
                                    </label>
                                </div>
                            </td>',
                $product->url,
                $product->sort,
                '<div class="btn-group text-center">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">'.Lang::get('main.action').'
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    '.((Peruser('our_products_edit'))?'<li>
                                            <a href="'.URL('admin/our_products/'.$product->id.'/edit').'">
                                                <i class="fa fa-comments-o"></i> '.Lang::get('main.edit').' 
                                            </a>
                                        </li>':'').'
                                    '.((Peruser('our_products_delete'))?'<li>
                                            <a class="delete_this" data-id="'.$product->id.'" >
                                                <i class="fa fa-comments-o"></i> '.Lang::get('main.delete').' 
                                            </a>
                                        </li>':'').'
                                        
                                        
                                    </ul>
                                </div>'
            ];
        }
        if (isset($data["customActionType"]) && $data["customActionType"] == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records['postData']=$data;
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
        //
        return view('auth.our_products.add');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data=$request->input();
        $valid=array(
            'name'=>'required',
            'description'=>'required',
            'sort'=>'required|unique:mysql2.our_products,sort',
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000'
        );
        if(PerUser('our_products_url')){
            $valid['url']='required|unique:mysql2.our_products,url';
        }
        $validator = Validator::make($request->all(),$valid);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 1 : 0;
            $our_products=new OurProducts();
            $our_products->name=$data['name'];
            $our_products->description=$data['description'];
            if(PerUser('our_products_url')){
                $our_products->url=$data['url'];
            }
            $our_products->sort=$data['sort'];
            $file=$request->file('image');
            $fileName = uploadFileToE3melbusiness($file);
            $our_products->image=$fileName;
            $our_products->active = $published;
            if ($published == 1) {
                $our_products->active_by = Auth::user()->id;
                $our_products->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 0) {
                $our_products->unactive_by = Auth::user()->id;
                $our_products->unactive_date = date("Y-m-d H:i:s");
            }
            $our_products->added_by=Auth::user()->id;
            $our_products->added_date=date("Y-m-d H:i:s");
            $our_products->lastedit_by=Auth::user()->id;
            $our_products->lastedit_date=date("Y-m-d H:i:s");
            if($our_products->save()){
                Session::flash('success', Lang::get('main.insert').Lang::get('main.our_products'));
                return Redirect::to('admin/our_products/create');
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
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product=OurProducts::findOrFail($id);
        return view('auth.our_products.edit',compact('product'));
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
        //
        $data=$request->input();
        $our_products= OurProducts::findOrFail($id);
        $valid=array(
            'name'=>'required',
            'description'=>'required',
            'sort'=>'required|unique:mysql2.our_products,sort,'.$id,
        );
        if(PerUser('our_products_url')){
            $valid['url']='required|unique:mysql2.our_products,url,'.$id;
        }
        if ( $request->file('image')){
            $valid['image']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$valid);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            if(Input::hasFile('image')){
                $file=$request->file('image');
                $fileName = uploadFileToE3melbusiness($file);
                $our_products->image=$fileName;
            }
            $published = (isset($data['published'])) ? 1 : 0;
            $our_products->name=$data['name'];
            $our_products->description=$data['description'];
            if(PerUser('our_products_url')){
                $old_url=$our_products->url;
                $our_products->url=$data['url'];
            }
            $our_products->sort=$data['sort'];
            if ($published == 1 && $our_products->active==0) {
                $our_products->active_by = Auth::user()->id;
                $our_products->active_date = date("Y-m-d H:i:s");
            }
            if ($published == 0 && $our_products->active==1) {
                $our_products->unactive_by = Auth::user()->id;
                $our_products->unactive_date = date("Y-m-d H:i:s");
            }
            $our_products->active=$published;
            $our_products->lastedit_by=Auth::user()->id;
            $our_products->lastedit_date=date("Y-m-d H:i:s");
            if($our_products->save()){
                if(PerUser('our_products_url')) {
                    if ($old_url != $our_products->url) {
                        saveOldUrl($id, 'our_products', $old_url, $our_products->url, Auth::user()->id, date("Y-m-d H:i:s"));
                    }
                }
                Session::flash('success', Lang::get('main.update').Lang::get('main.our_products'));
                return Redirect::to('admin/our_products/'.$id.'/edit');
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
        //
        $our_products=OurProducts::findOrFail($id);
        $our_products->delete();

    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $our_products = OurProducts::findOrFail($id);
            if ($published == 'no') {
                $our_products->active = 0;
                $our_products->unactive_by = Auth::user()->id;
                $our_products->unactive_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $our_products->active = 1;
                $our_products->active_by = Auth::user()->id;
                $our_products->active_date = date("Y-m-d H:i:s");
            }
            $our_products->save();
        } else {
            return redirect(404);
        }
    }
}
