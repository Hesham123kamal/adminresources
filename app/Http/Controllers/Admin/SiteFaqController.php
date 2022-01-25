<?php

namespace App\Http\Controllers\Admin;

use App\SiteFaq;
use App\Http\Controllers\Controller;
use App\SiteFaqType;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SiteFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types=SiteFaqType::get();
        return view('auth.site_faq.view',compact('types'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $site_faqs = SiteFaq::join('site_faq_type','site_faq_type.id','=','site_faq.type')
                    ->select('site_faq.*','site_faq_type.name as type_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $site_faqs = $site_faqs->where('site_faq.id', '=', $id);
        }
        if (isset($data['question']) && !empty($data['question'])) {
            $question = $data['question'];
            $site_faqs = $site_faqs->where('site_faq.question', 'LIKE', "%$question%");
        }
        if (isset($data['answer']) && !empty($data['answer'])) {
            $answer = $data['answer'];
            $site_faqs = $site_faqs->where('site_faq.answer', 'LIKE', "%$answer%");
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $site_faqs = $site_faqs->where('site_faq_type.id', '=', $type);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $site_faqs = $site_faqs->whereBetween('site_faq.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $site_faqs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'site_faq.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'site_faq.id';
                break;
            case 1:
                $columnName = 'site_faq.question';
                break;
            case 2:
                $columnName = 'site_faq.answer';
                break;
            case 3:
                $columnName = 'site_faq.createdtime';
                break;
            case 4:
                $columnName = 'site_faq_type.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $site_faqs = $site_faqs->where(function ($q) use ($search) {
                $q->where('site_faq.question', 'LIKE', "%$search%")
                    ->orWhere('site_faq.answer', 'LIKE', "%$search%")
                    ->orWhere('site_faq_type.name', 'LIKE', "%$search%")
                    ->orWhere('site_faq.id', '=', $search);
            });
        }

        $site_faqs = $site_faqs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($site_faqs as $site_faq) {
            $type_name = $site_faq->type_name;
            if(PerUser('site_faq_type_edit') && $type_name !=''){
                $type_name= '<a target="_blank" href="' . URL('admin/site_faq_type/' . $site_faq->type . '/edit') . '">' . $type_name . '</a>';
            }
            $records["data"][] = [
                $site_faq->id,
                $site_faq->question,
                $site_faq->answer,
                $site_faq->createdtime,
                $type_name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $site_faq->id . '" type="checkbox" ' . ((!PerUser('site_faq_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('site_faq_publish')) ? 'class="changeStatues"' : '') . ' ' . (($site_faq->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $site_faq->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $site_faq->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('site_faq_edit')) ? '<li>
                                            <a href="' . URL('admin/site_faq/' . $site_faq->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('site_faq_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $site_faq->id . '" >
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
        $types=SiteFaqType::get();
        return view('auth.site_faq.add',compact('types'));
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
                'type' =>'required|exists:mysql2.site_faq_type,id',
                'question' => 'required',
                'answer' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $site_faq = new SiteFaq();
            $site_faq->question = $data['question'];
            $site_faq->answer = $data['answer'];
            $site_faq->type = $data['type'];
            $site_faq->published = $published;
            $site_faq->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $site_faq->published_by = Auth::user()->id;
                $site_faq->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $site_faq->unpublished_by = Auth::user()->id;
                $site_faq->unpublished_date = date("Y-m-d H:i:s");
            }
            $site_faq->lastedit_by = Auth::user()->id;
            $site_faq->added_by = Auth::user()->id;
            $site_faq->lastedit_date = date("Y-m-d H:i:s");
            $site_faq->added_date = date("Y-m-d H:i:s");
            if ($site_faq->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.site_faq'));
                return Redirect::to('admin/site_faq/create');
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
        $site_faq = SiteFaq::findOrFail($id);
        $types=SiteFaqType::get();
        return view('auth.site_faq.edit', compact('site_faq','types'));
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
        $site_faq = SiteFaq::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'type' =>'required|exists:mysql2.site_faq_type,id',
                'question' => 'required',
                'answer' => 'required',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $site_faq->question = $data['question'];
            $site_faq->answer = $data['answer'];
            $site_faq->type = $data['type'];
            if ($published == 'yes' && $site_faq->published=='no') {
                $site_faq->published_by = Auth::user()->id;
                $site_faq->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $site_faq->published=='yes') {
                $site_faq->unpublished_by = Auth::user()->id;
                $site_faq->unpublished_date = date("Y-m-d H:i:s");
            }
            $site_faq->published = $published;
            $site_faq->lastedit_by = Auth::user()->id;
            $site_faq->lastedit_date = date("Y-m-d H:i:s");
            if ($site_faq->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.site_faq'));
                return Redirect::to("admin/site_faq/$site_faq->id/edit");
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
        $site_faq = SiteFaq::findOrFail($id);
        $site_faq->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $site_faq = SiteFaq::findOrFail($id);
            if ($published == 'no') {
                $site_faq->published = 'no';
                $site_faq->unpublished_by = Auth::user()->id;
                $site_faq->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $site_faq->published = 'yes';
                $site_faq->published_by = Auth::user()->id;
                $site_faq->published_date = date("Y-m-d H:i:s");
            }
            $site_faq->save();
        } else {
            return redirect(404);
        }
    }
}
