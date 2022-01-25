<?php
namespace App\Http\Controllers\Admin;
use App\CoursesSections;
use App\Webinars;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\WebinarResource;
use Carbon\Carbon;
class WebinarResourcesController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        return view('auth.webinar_resources.view');
    }
    function search(Request $request)
    {
        $webinar_resources = DB::connection('mysql2')->table('webinar_resources')
            ->leftJoin('webinar', 'webinar_resources.webinar_id', '=', 'webinar.id')
            ->select('webinar_resources.*', 'webinar.name as webinar_name');
        // return response()->json($webinar_resources, 200);
        if(isset($request->webinar_name) &&!empty($request->webinar_name)){
           $webinar_resources=$webinar_resources->where('webinar.name',  'like', '%' . $request->webinar_name . '%'  );
            }
        if(isset($request->create_date_from) &&!empty($request->create_date_from)){
            $from = $request->create_date_from;
            $to = $request->create_date_to;
           $webinar_resources=$webinar_resources->whereBetween('webinar_resources.createdtime', [$from, $to]);
        }
        if(isset($request->description) &&!empty($request->description)){
           $webinar_resources=$webinar_resources->where('webinar_resources.description',  'like', '%' . $request->description . '%'  );
            }
        $iTotalRecords = $webinar_resources->count();
        $iDisplayLength = intval($request['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($request['start']);
        $sEcho = intval($request['draw']);
        $records = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        // return($webinar_resources);
        $webinar_resources = $webinar_resources->skip($iDisplayStart)->take($iDisplayLength)->orderBy('createdtime', 'DESC')->get();
            foreach ($webinar_resources as $resource) {
                $records['data'][]=[
                        '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="1"><span></span></label>',
                        $resource->webinar_name,
                        $resource->createdtime,
                        $resource->description,
                    '<div class="btn-group text-center" id="single-order-' . $resource->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('webinar_resources_edit')) ? '<li>
                                            <a href="' . URL('admin/webinar_resources/' . $resource->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('webinar_resources_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $resource->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '


                                    </ul>
                                </div>',
                ];
            }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return $records;
            
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $webinars = Webinars::get();
        // dd( $webinars);
        return view('auth.webinar_resources.add')->with('webinars',$webinars);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'file' => 'required',
            'webinar_name' => 'required',
           
            'description' => 'required',
            'isfree' => 'required',
            'sort' => 'required',
          
        ]);
        $file=$request->file('file');
        $fileName = uploadFileToE3melbusiness($file);
        $WebinarResource = new WebinarResource;
        $WebinarResource->original_file_name = $request->file->getClientOriginalName();
        $WebinarResource->name = $request->name;
        $WebinarResource->file = $fileName;
        $WebinarResource->webinar_id = $request->webinar_name;
        $WebinarResource->description = $request->description;
        $WebinarResource->active = $request->active;
        $WebinarResource->isfree = $request->isfree;
        $WebinarResource->sort = $request->sort;
        $WebinarResource->createdtime = Carbon::now();
        $saved = $WebinarResource->save();
        if(!$saved){
            return response()->json(['error'=>'we couldnt save data']);
        }
 
        return response()->json(['success'=>'You have successfully upload file.']);
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
        $webinars = DB::connection('mysql2')->table('webinar')->get();
        $webinar_resource = WebinarResource::find($id);
//         dd($webinar_resource);
        return view('auth.webinar_resources.edit',compact('webinars','webinar_resource'));
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
        $WebinarResource = WebinarResource::find($id);
        if ($request->file) {
            $file=$request->file('file');
            $fileName = uploadFileToE3melbusiness($file);
            $WebinarResource->file = $fileName;
            $WebinarResource->original_file_name = $request->file->getClientOriginalName();
        }
        $WebinarResource->name = $request->name;
        if ($request->webinar_name) {
            $WebinarResource->webinar_id = $request->webinar_name;
        }

        $WebinarResource->description = $request->description;
        $WebinarResource->active = $request->active;
        $WebinarResource->isfree = $request->isfree;
        $WebinarResource->sort = $request->sort;
        $WebinarResource->modifiedtime = Carbon::now();
        if ($WebinarResource->save()) {
            $messsage = '<div class="alert alert-success"><ul>';
            $messsage .= '<li>' . Lang::get('main.update') . Lang::get('main.webinar_resources') . '</li>';
            $messsage .= '</ul></div>';
            return response()->json(['message'=>$messsage,'success'=>true]);
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
        $WebinarResource = DB::connection('mysql2')->table('webinar_resources')->where('id', '=', $id)->first();
        if ($WebinarResource->file) {
            unlink('files/'.$WebinarResource->file);
        }
        
        $course_resource = WebinarResource::find($id);
        // $image = \DB::connection('mysql2')->table('files')->where('id', $id)->first();
        // $file= $image->your_file_path;
        // $filename = public_path().'/uploads_folder/'.$file;
        // \File::delete($filename);
        $course_resource->delete();
        // return redirect()->route('courses_resources');
        return response()->json(['done'=>'deleted successfully']);
        
    }
    function get_sections($id)
    {
        $sections = CoursesSections::where("course_id",$id)->pluck("name","id");
        return $sections;
        
    }
    function getUpdate(Request $request, $id)
    {
       $WebinarResource = WebinarResource::find($id);
        if ($request->file) {
            /*$WebinarResource_file = DB::connection('mysql2')->table('webinar_resources')->where('id', '=', $id)->first();
            if ($WebinarResource_file->file) {
                if (file_exists('files/'.$WebinarResource_file->file)) {
                    unlink('files/'.$WebinarResource_file->file);
                }
            }*/
            
        
        // return $WebinarResource->file;
            $file=$request->file('file');
            $fileName = uploadFileToE3melbusiness($file);
        $WebinarResource->file = $fileName;
        $WebinarResource->original_file_name = $request->file->getClientOriginalName();
        }
        $WebinarResource->name = $request->name;
        if ($request->webinar_name) {
            $WebinarResource->webinar_id = $request->webinar_name;
        }
        
        $WebinarResource->description = $request->description;
        $WebinarResource->active = $request->active;
        $WebinarResource->isfree = $request->isfree;
        $WebinarResource->sort = $request->sort;
        $WebinarResource->modifiedtime = Carbon::now();
        Session::put([
        'success' => 'successfully edited'
        ]);
    
        $saved = $WebinarResource->save();
        if(!$saved){
            return response()->json(['error'=>'we couldnt save data']);
        }
 
        return response()->json(['success'=>'You have successfully updated the data.']);
    }

}
