<?php

namespace App\Http\Controllers\Admin;

use App\Testimonial;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class  TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.testimonials.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $testimonials = Testimonial::select('testimonials.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $testimonials = $testimonials->where('id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $testimonials = $testimonials->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $testimonials = $testimonials->whereBetween('createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $testimonials->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'id';
                break;
            case 1:
                $columnName = 'name';
                break;
            case 4:
                $columnName = 'createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $testimonials = $testimonials->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $testimonials = $testimonials->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($testimonials as $testimonial) {
            $testimonial=makeDefaultImageGeneral($testimonial,'image');
            $records["data"][] = [
                $testimonial->id,
                $testimonial->name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($testimonial->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $testimonial->id . '" type="checkbox" ' . ((!PerUser('testimonials_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('testimonials_publish')) ? 'class="changeStatues"' : '') . ' ' . (($testimonial->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $testimonial->id . '">
                                    </label>
                                </div>
                            </td>',
                $testimonial->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $testimonial->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('testimonials_edit')) ? '<li>
                                            <a href="' . URL('admin/testimonials/' . $testimonial->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('testimonials_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $testimonial->id . '" >
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
        return view('auth.testimonials.add');
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
                'description' => 'required',
                'image' => 'mimes:jpeg,jpg,png|required|max:5000'
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $testimonial = new Testimonial();
            $testimonial->name = $data['name'];
            $testimonial->description = $data['description'];
            $testimonial->published = $published;
            $testimonial->image = $picName;
            $testimonial->created_time = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $testimonial->published_by = Auth::user()->id;
                $testimonial->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $testimonial->unpublished_by = Auth::user()->id;
                $testimonial->unpublished_date = date("Y-m-d H:i:s");
            }
            $testimonial->lastedit_by = Auth::user()->id;
            $testimonial->added_by = Auth::user()->id;
            $testimonial->lastedit_date = date("Y-m-d H:i:s");
            $testimonial->added_date = date("Y-m-d H:i:s");
            if ($testimonial->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.testimonial'));
                return Redirect::to('admin/testimonials/create');
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
        $testimonial = Testimonial::findOrFail($id);
        return view('auth.testimonials.edit', compact('testimonial'));
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
        $testimonial = Testimonial::findOrFail($id);
        $rules=array(
            'name' => 'required',
            'description' => 'required'
        );
        if ( $request->file('image')){
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $testimonial->name = $data['name'];
            $testimonial->description = $data['description'];
            if ( $request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $testimonial->image = $picName;
            }
            if ($published == 'yes' && $testimonial->published=='no') {
                $testimonial->published_by = Auth::user()->id;
                $testimonial->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $testimonial->published=='yes') {
                $testimonial->unpublished_by = Auth::user()->id;
                $testimonial->unpublished_date = date("Y-m-d H:i:s");
            }
            $testimonial->published = $published;
            $testimonial->lastedit_by = Auth::user()->id;
            $testimonial->lastedit_date = date("Y-m-d H:i:s");
            if ($testimonial->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.testimonial'));
                return Redirect::to("admin/testimonials/$testimonial->id/edit");
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
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $testimonial = Testimonial::findOrFail($id);
            if ($published == 'no') {
                $testimonial->published = 'no';
                $testimonial->unpublished_by = Auth::user()->id;
                $testimonial->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $testimonial->published = 'yes';
                $testimonial->published_by = Auth::user()->id;
                $testimonial->published_date = date("Y-m-d H:i:s");
            }
            $testimonial->save();
        } else {
            return redirect(404);
        }
    }
}
