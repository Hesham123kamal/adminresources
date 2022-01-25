<?php

namespace App\Http\Controllers\Admin;

use App\CoursesOffers;
use App\Courses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoursesOffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        return view('auth.courses_offers.view',compact('courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $courses_offers = CoursesOffers::leftjoin('courses','courses.id','=','courses_offers.course_id')
        ->select('courses_offers.*','courses.name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $courses_offers = $courses_offers->where('courses_offers.id', '=', "$id");
        }
        if (isset($data['description']) && !empty($data['description'])) {
            $description = $data['description'];
            $courses_offers = $courses_offers->where('courses_offers.description', 'LIKE', "%$description%");
        }
        if (isset($data['egy_price']) && !empty($data['egy_price'])) {
            $egy_price = $data['egy_price'];
            $courses_offers = $courses_offers->where('courses_offers.egy_price', 'LIKE', "%$egy_price%");
        }
        if (isset($data['egy_sale_price']) && !empty($data['egy_sale_price'])) {
            $egy_sale_price = $data['egy_sale_price'];
            $courses_offers = $courses_offers->where('courses_offers.egy_sale_price', 'LIKE', "%$egy_sale_price%");
        }
        if (isset($data['ksa_price']) && !empty($data['ksa_price'])) {
            $ksa_price = $data['ksa_price'];
            $courses_offers = $courses_offers->where('courses_offers.ksa_price', 'LIKE', "%$ksa_price%");
        }
        if (isset($data['ksa_sale_price']) && !empty($data['ksa_sale_price'])) {
            $ksa_sale_price = $data['ksa_sale_price'];
            $courses_offers = $courses_offers->where('courses_offers.ksa_sale_price', 'LIKE', "%$ksa_sale_price%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $courses_offers = $courses_offers->where('courses.id', '=', $course);
        }


        if (isset($data['expired_time_from']) && !empty($data['expired_time_from']) && isset($data['expired_time_to']) && !empty($data['expired_time_to'])) {
            $expired_time_from = $data['expired_time_from'];
            $expired_time_to = $data['expired_time_to'];
            $courses_offers = $courses_offers->whereBetween('courses_offers.expired_date', [$expired_time_from, $expired_time_to]);
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $courses_offers = $courses_offers->whereBetween('courses_offers.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $courses_offers->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'courses_offers.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'courses_offers.id';
                break;
            case 1:
                $columnName = 'courses.name';
                break;
            case 2:
                $columnName = 'courses_offers.description';
                break;
            case 4:
                $columnName = 'courses_offers.egy_price';
                break;
            case 5:
                $columnName = 'courses_offers.egy_sale_price';
                break;
            case 6:
                $columnName = 'courses_offers.ksa_price';
                break;
            case 7:
                $columnName = 'courses_offers.ksa_sale_price';
                break;
            case 8:
                $columnName = 'courses_offers.expired_date';
                break;
            case 9:
                $columnName = 'courses_offers.createdtime';
                break;

        }

        $search = $data['search']['value'];
        if ($search) {
            $courses_offers = $courses_offers->where(function ($q) use ($search) {
                $q->where('courses_offers.egy_price', 'LIKE', "%$search%")
                    ->orWhere('courses_offers.egy_sale_price', 'LIKE', "%$search%")
                    ->orWhere('courses_offers.ksa_price', 'LIKE', "%$search%")
                    ->orWhere('courses_offers.ksa_sale_price', 'LIKE', "%$search%")
                    ->orWhere('courses_offers.id', '=', $search)
                    ->orWhere('courses.name', '=', 'LIKE', "%$search%");
            });
        }

        $courses_offers = $courses_offers->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($courses_offers as $offer) {
            $course_name = $offer->name;
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $offer->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            $records["data"][] = [
                $offer->id,
                $course_name,
                strip_tags($offer->description),
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $offer->id . '" type="checkbox" ' . ((!PerUser('courses_offers_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('courses_offers_publish')) ? 'class="changeStatues"' : '') . ' ' . (($offer->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $offer->id . '">
                                    </label>
                                </div>
                            </td>',
                $offer->egy_price,
                $offer->egy_sale_price,
                $offer->ksa_price,
                $offer->ksa_sale_price,
                date('Y-m-d', strtotime($offer->expired_date)),
                $offer->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $offer->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_offers_edit')) ? '<li>
                                            <a href="' . URL('admin/courses_offers/' . $offer->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_offers_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $offer->id . '" >
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
        $courses = Courses::pluck('name', 'id');
        return view('auth.courses_offers.add',compact('courses'));
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
                'course' =>'required|exists:mysql2.courses,id',
                'egy_price' => 'required|numeric',
                'egy_sale_price' => 'required|numeric',
                'ksa_price' => 'required|numeric',
                'ksa_sale_price' => 'required|numeric',
                'expired_date' => 'required|date_format:"Y-m-d"',
                'description' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $offer = new CoursesOffers();
            $offer->description = $data['description'];
            $offer->course_id = $data['course'];
            $offer->egy_price = $data['egy_price'];
            $offer->ksa_price = $data['ksa_price'];
            $offer->egy_sale_price = $data['egy_sale_price'];
            $offer->ksa_sale_price = $data['ksa_sale_price'];
            //$offer->published = $published;
            $offer->expired_date = $data['expired_date'];
            $offer->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $offer->published_by = Auth::user()->id;
//                $offer->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $offer->unpublished_by = Auth::user()->id;
//                $offer->unpublished_date = date("Y-m-d H:i:s");
//            }
            $offer->lastedit_by = Auth::user()->id;
            $offer->added_by = Auth::user()->id;
            $offer->lastedit_date = date("Y-m-d H:i:s");
            $offer->added_date = date("Y-m-d H:i:s");
            if ($offer->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.course_offer'));
                return Redirect::to('admin/courses_offers/create');
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
        $courses = Courses::pluck('name', 'id');
        $offer = CoursesOffers::findOrFail($id);
        $offer->expired_date = date("Y-m-d", strtotime($offer->expired_date));
        return view('auth.courses_offers.edit', compact('courses','offer'));
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
        $offer = CoursesOffers::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'course' =>'required|exists:mysql2.courses,id',
                'egy_price' => 'required|numeric',
                'egy_sale_price' => 'required|numeric',
                'ksa_price' => 'required|numeric',
                'ksa_sale_price' => 'required|numeric',
                'expired_date' => 'required|date_format:"Y-m-d"',
                'description' => 'required',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $offer->description = $data['description'];
            $offer->course_id = $data['course'];
            $offer->egy_price = $data['egy_price'];
            $offer->ksa_price = $data['ksa_price'];
            $offer->egy_sale_price = $data['egy_sale_price'];
            $offer->ksa_sale_price = $data['ksa_sale_price'];
            $offer->expired_date = $data['expired_date'];
//            if ($published == 'yes' && $offer->published=='no') {
//                $offer->published_by = Auth::user()->id;
//                $offer->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $offer->published=='yes') {
//                $offer->unpublished_by = Auth::user()->id;
//                $offer->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $offer->published = $published;
            $offer->lastedit_by = Auth::user()->id;
            $offer->lastedit_date = date("Y-m-d H:i:s");
            if ($offer->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.course_offer'));
                return Redirect::to("admin/courses_offers/$offer->id/edit");
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
        $offer = CoursesOffers::findOrFail($id);
        $offer->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $offer = CoursesOffers::findOrFail($id);
//            if ($published == 'no') {
//                $offer->published = 'no';
//                $offer->unpublished_by = Auth::user()->id;
//                $offer->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $offer->published = 'yes';
//                $offer->published_by = Auth::user()->id;
//                $offer->published_date = date("Y-m-d H:i:s");
//            }
//            $offer->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
