<?php

namespace App\Http\Controllers\Admin;

use App\OurProductsCourses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;


class OurProductsCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return view('auth.our_products_courses.view');

    }
    public function getOurProductsCoursesAJAX(Request $request){
        $data=$request->input();
        $our_products_courses=OurProductsCourses::select('our_products_courses.*');
        if(isset($data['name'])&&!empty($data['name'])){
            $name=$data['name'];
            $our_products_courses=$our_products_courses->where('name','LIKE',"%$name%");
        }
        if(isset($data['url'])&&!empty($data['url'])){
            $url=$data['url'];
            $our_products_courses=$our_products_courses->where('url','LIKE',"%$url%");
        }
        if(isset($data['sort'])&&!empty($data['sort'])){
            $sort=$data['sort'];
            $our_products_courses=$our_products_courses->where('sort','=',$sort);
        }
        $iTotalRecords=$our_products_courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'our_products_courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'our_products_courses.id';
                break;
            case 1:
                $columnName = 'our_products_courses.name';
                break;
            case 3:
                $columnName = 'our_products_courses.url';
                break;
            case 4:
                $columnName = 'our_products_courses.sort';
                break;
        }
        $search = $data['search']['value'];
        if($search){
            $our_products_courses=$our_products_courses->where(function($q)use($search){
                $q->where('name','LIKE',"%$search%")
                    ->orWhere('url','LIKE',"%$search%")
                    ->orWhere('description','LIKE',"%$search%")
                    ->orWhere('sort','LIKE',"%$search%");
            });
        }
        $our_products_courses=$our_products_courses->orderBy($columnName,$data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($our_products_courses as $question) {
            $question=makeDefaultImageGeneral($question,'image');
            $records["data"][] = [
                $question->id,
                $question->name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img style="width:50px;height:50px;" src="'.assetURL($question->image).'"></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="'.$question->id.'" type="checkbox" '.((!PerUser('our_products_courses_active'))?'disabled="disabled"':'').' '.((PerUser('our_products_courses_active'))?'class="changeStatues"':'').' '.(($question->active)?'checked="checked"':'').'  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-'.$question->id.'">
                                    </label>
                                </div>
                            </td>',
                $question->url,
                $question->sort,
                '<div class="btn-group text-center">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">'.Lang::get('main.action').'
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    '.((Peruser('our_products_courses_edit'))?'<li>
                                            <a href="'.URL('admin/our_products_courses/'.$question->id.'/edit').'">
                                                <i class="fa fa-comments-o"></i> '.Lang::get('main.edit').' 
                                            </a>
                                        </li>':'').'
                                    '.((Peruser('our_products_courses_delete'))?'<li>
                                            <a class="delete_this" data-id="'.$question->id.'" >
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
        return view('auth.our_products_courses.add');

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
            'sort'=>'required|unique:mysql2.our_products_courses,sort',
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000'
        );
        if(PerUser('our_products_courses_url')){
            $valid['url']='required|unique:mysql2.our_products_courses,url';
        }
        $validator = Validator::make($request->all(),$valid);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $active=(isset($data['active']))?1:0;
            $our_products_courses=new OurProductsCourses();
            $our_products_courses->name=$data['name'];
            $our_products_courses->description=$data['description'];
            if(PerUser('our_products_courses_url')){
                $our_products_courses->url=$data['url'];
            }
            $our_products_courses->sort=$data['sort'];
            $file=$request->file('image');
            $fileName = uploadFileToE3melbusiness($file);
            $our_products_courses->image=$fileName;
            $our_products_courses->active=$active;
            if($active==1){
                $our_products_courses->active_by=Auth::user()->id;
                $our_products_courses->active_date=date("Y-m-d H:i:s");
            }
            if($active==0){
                $our_products_courses->unactive_by=Auth::user()->id;
                $our_products_courses->unactive_date=date("Y-m-d H:i:s");
            }
            $our_products_courses->added_by=Auth::user()->id;
            $our_products_courses->added_date=date("Y-m-d H:i:s");
            $our_products_courses->lastedit_by=Auth::user()->id;
            $our_products_courses->lastedit_date=date("Y-m-d H:i:s");
            if($our_products_courses->save()){
                Session::flash('success', Lang::get('main.insert').Lang::get('main.our_products_courses'));
                return Redirect::to('admin/our_products_courses/create');
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
        //
        $post=OurProductsCourses::find($id);
        if(count($post)){
            return view('auth.our_products_courses.edit',compact('post'));
        }else{
            return abort(404);
        }

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
        $our_products_courses= OurProductsCourses::find($id);
        if(count($our_products_courses)){
            $valid=array(
                'name'=>'required',
                'description'=>'required',
                'sort'=>'required|unique:mysql2.our_products_courses,sort,'.$id,
            );
            if(PerUser('our_products_courses_url')){
                $valid['url']='required|unique:mysql2.our_products_courses,url,'.$id;
            }
            if ( $request->file('image')){
                $valid['image']='mimes:jpeg,jpg,png,gif|required|max:5000';
            }
            $validator = Validator::make($request->all(),$valid);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }else {
                if(Input::hasFile('image')){
                    $validator = Validator::make($request->all(),array(
                        'image' => 'required|mimes:jpeg,bmp,png'
                    ));
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator->errors())->withInput();
                    }else{
                        $file=$request->file('image');
                        $fileName = uploadFileToE3melbusiness($file);
                        $our_products_courses->image=$fileName;
                    }
                }
                $active=(isset($data['active']))?1:0;
                $our_products_courses->name=$data['name'];
                $our_products_courses->description=$data['description'];
                if(PerUser('our_products_courses_url')){
                    $old_url=$our_products_courses->url;
                    $our_products_courses->url=$data['url'];
                }
                $our_products_courses->sort=$data['sort'];
                if($active==1&&$our_products_courses->active==0){
                    $our_products_courses->active_by=Auth::user()->id;
                    $our_products_courses->active_date=date("Y-m-d H:i:s");
                }
                if($active==0&&$our_products_courses->active==1){
                    $our_products_courses->unactive_by=Auth::user()->id;
                    $our_products_courses->unactive_date=date("Y-m-d H:i:s");
                }

                $our_products_courses->active=$active;
                $our_products_courses->lastedit_by=Auth::user()->id;
                $our_products_courses->lastedit_date=date("Y-m-d H:i:s");
                if($our_products_courses->save()){
                    if(PerUser('our_products_courses_url')) {
                        if ($old_url != $our_products_courses->url) {
                            saveOldUrl($id, 'our_products_courses', $old_url, $our_products_courses->url, Auth::user()->id, date("Y-m-d H:i:s"));
                        }
                    }
                    Session::flash('success', Lang::get('main.update').Lang::get('main.our_products_courses'));
                    return Redirect::to('admin/our_products_courses/'.$id.'/edit');
                }
            }
        }else{
            return abort(404);
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
        $our_products_courses=OurProductsCourses::find($id);
        if(count($our_products_courses)){
            if($our_products_courses->delete()){

            }
        }
    }
    public function activation(Request $request){
        if($request->ajax()){
            $id=$request->input('id');
            $active=$request->input('active');
            $our_products_courses=OurProductsCourses::find($id);
            if ($active == 0) {
                $our_products_courses->active = 0;
                $our_products_courses->unactive_by = Auth::user()->id;
                $our_products_courses->unactive_date = date("Y-m-d H:i:s");
            } elseif ($active == 1) {
                $our_products_courses->active = 1;
                $our_products_courses->active_by = Auth::user()->id;
                $our_products_courses->active_date = date("Y-m-d H:i:s");
            }
            $our_products_courses->save();
        }else{
            return redirect(404);
        }
    }
}
