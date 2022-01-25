<?php

namespace App\Http\Controllers\Admin;

use App\AllCategory;
use App\SubCategory;
use App\Courses;
use App\CourseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        return view('auth.courses_categories.view',compact('courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $courses_categories = CourseCategory::join('categories','categories.id','=','courses_categories.category_id')
                                ->leftjoin('sup_categories','sup_categories.id','=','courses_categories.sup_category_id')
                                ->join('courses','courses.id','=','courses_categories.course_id')
                                ->select('courses_categories.*','categories.name as category_name','sup_categories.name as sub_category_name','courses.name as course_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $courses_categories = $courses_categories->where('courses_categories.id', '=', $id);
        }
        if (isset($data['category']) && !empty($data['category'])) {
            $category = $data['category'];
            $courses_categories = $courses_categories->where('categories.name','LIKE', "%$category%");
        }
        if (isset($data['sub_category']) && !empty($data['sub_category'])) {
            $sub_category = $data['sub_category'];
            $courses_categories = $courses_categories->where('sup_categories.name','LIKE', "%$sub_category%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $courses_categories = $courses_categories->where('courses.id','=', $course);
        }
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sort = $data['sort'];
            $courses_categories = $courses_categories->where('courses_categories.sort', '=', $sort);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $courses_categories = $courses_categories->whereBetween('courses_categories.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $courses_categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'courses_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'courses_categories.id';
                break;
            case 1:
                $columnName = 'categories.name';
                break;
            case 2:
                $columnName = 'sup_categories.name';
                break;
            case 3:
                $columnName = 'courses.name';
                break;
            case 4:
                $columnName = 'courses_categories.createtime';
                break;
            case 5:
                $columnName = 'courses_categories.sort';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $courses_categories = $courses_categories->where(function ($q) use ($search) {
                $q->where('courses_categories.id', '=', $search)
                    ->orWhere('categories.name', 'LIKE', "%$search%")
                    ->orWhere('sup_categories.name', 'LIKE', "%$search%")
                    ->orWhere('courses_categories.sort', '=', $search)
                    ->orWhere('courses.name', 'LIKE', "%$search%");
            });
        }

        $courses_categories = $courses_categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($courses_categories as $course_category) {
            $category_name = $course_category->category_name;
            $sub_category_name = $course_category->sub_category_name;
            $course_name = $course_category->course_name;
            if(PerUser('all_categories_edit') && $category_name !=''){
                $category_name= '<a target="_blank" href="' . URL('admin/all_categories/' . $course_category->category_id . '/edit') . '">' . $category_name . '</a>';
            }
            if(PerUser('sub_categories_edit') && $sub_category_name !=''){
                $sub_category_name= '<a target="_blank" href="' . URL('admin/sub_categories/' . $course_category->sup_category_id . '/edit') . '">' . $sub_category_name . '</a>';
            }
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $course_category->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            $records["data"][] = [
                $course_category->id,
                $category_name,
                $sub_category_name,
                $course_name,
                $course_category->createtime,
                $course_category->sort,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $course_category->id . '" type="checkbox" ' . ((!PerUser('courses_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('courses_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($course_category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $course_category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $course_category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/courses_categories/' . $course_category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_categories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $course_category->id . '" >
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
        $categories = AllCategory::pluck('name', 'id');
        $sub_categories = SubCategory::pluck('name', 'id');
        $courses = Courses::pluck('name', 'id');
        return view('auth.courses_categories.add',compact('categories','sub_categories','courses'));
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
                'category' =>'required|exists:mysql2.categories,id',
                'sub_category' =>'required|exists:mysql2.sup_categories,id',
                'course' =>'required|exists:mysql2.courses,id',
                'sort' =>'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $course_category = new CourseCategory();
            $course_category->published = $published;
            $course_category->category_id = $data['category'];
            $course_category->sup_category_id = $data['sub_category'];
            $course_category->course_id = $data['course'];
            $course_category->sort = $data['sort'];
            $course_category->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $course_category->published_by = Auth::user()->id;
                $course_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $course_category->unpublished_by = Auth::user()->id;
                $course_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $course_category->lastedit_by = Auth::user()->id;
            $course_category->added_by = Auth::user()->id;
            $course_category->added_date = date("Y-m-d H:i:s");
            $course_category->lastedit_date = date("Y-m-d H:i:s");
            if ($course_category->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.course_category'));
                return Redirect::to('admin/courses_categories/create');
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
        $course_category = CourseCategory::findOrFail($id);
        $categories = AllCategory::pluck('name', 'id');
        $sub_categories = SubCategory::where('category_id','=',$course_category->category_id)->pluck('name', 'id');
        $courses = Courses::pluck('name', 'id');
        return view('auth.courses_categories.edit', compact('course_category','categories','sub_categories','courses'));
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
        $course_category = CourseCategory::findOrFail($id);
        $rules=array(
            'category' =>'required|exists:mysql2.categories,id',
            'sub_category' =>'required|exists:mysql2.sup_categories,id',
            'course' =>'required|exists:mysql2.courses,id',
            'sort' =>'required',
        );
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $course_category->category_id = $data['category'];
            $course_category->sup_category_id = $data['sub_category'];
            $course_category->course_id = $data['course'];
            $course_category->sort = $data['sort'];
            if ($published == 'yes' && $course_category->published=='no') {
                $course_category->published_by = Auth::user()->id;
                $course_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $course_category->published=='yes') {
                $course_category->unpublished_by = Auth::user()->id;
                $course_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $course_category->published = $published;
            $course_category->lastedit_by = Auth::user()->id;
            $course_category->lastedit_date = date("Y-m-d H:i:s");
            if ($course_category->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.course_category'));
                return Redirect::to("admin/courses_categories/$course_category->id/edit");
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
        $course_category = CourseCategory::findOrFail($id);
        $course_category->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $course_category = CourseCategory::findOrFail($id);
            if ($published == 'no') {
                $course_category->published = 'no';
                $course_category->unpublished_by = Auth::user()->id;
                $course_category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $course_category->published = 'yes';
                $course_category->published_by = Auth::user()->id;
                $course_category->published_date = date("Y-m-d H:i:s");
            }
            $course_category->save();
        } else {
            return redirect(404);
        }
    }

}
