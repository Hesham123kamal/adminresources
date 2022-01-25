<?php

namespace App\Http\Controllers\Admin;

use App\RecruitmentCurrency;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class  RecruitmentCurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruitment_currencies.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $currencies = RecruitmentCurrency::select('recruitment_currencies.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $currencies = $currencies->where('id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $currencies = $currencies->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $currencies = $currencies->whereBetween('createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $currencies->count();
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
            $currencies = $currencies->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $currencies = $currencies->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($currencies as $currency) {
            $records["data"][] = [
                $currency->id,
                $currency->name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $currency->id . '" type="checkbox" ' . ((!PerUser('recruitment_currencies_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_currencies_publish')) ? 'class="changeStatues"' : '') . ' ' . (($currency->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $currency->id . '">
                                    </label>
                                </div>
                            </td>',
                $currency->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $currency->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_currencies_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_currencies/' . $currency->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_currencies_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $currency->id . '" >
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
        return view('auth.recruitment_currencies.add');
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
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $currency = new RecruitmentCurrency();
            $currency->name = $data['name'];
            $currency->published = $published;
            $currency->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $currency->published_by = Auth::user()->id;
                $currency->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $currency->unpublished_by = Auth::user()->id;
                $currency->unpublished_date = date("Y-m-d H:i:s");
            }
            $currency->lastedit_by = Auth::user()->id;
            $currency->added_by = Auth::user()->id;
            $currency->lastedit_date = date("Y-m-d H:i:s");
            $currency->added_date = date("Y-m-d H:i:s");
            if ($currency->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_currency'));
                return Redirect::to('admin/recruitment_currencies/create');
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
        $currency = RecruitmentCurrency::findOrFail($id);
        return view('auth.recruitment_currencies.edit', compact('currency'));
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
        $currency = RecruitmentCurrency::findOrFail($id);
        $validator = Validator::make($request->all(),array(
            'name' => 'required',
        ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $currency->name = $data['name'];
            if ($published == 'yes' && $currency->published=='no') {
                $currency->published_by = Auth::user()->id;
                $currency->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $currency->published=='yes') {
                $currency->unpublished_by = Auth::user()->id;
                $currency->unpublished_date = date("Y-m-d H:i:s");
            }
            $currency->published = $published;
            $currency->lastedit_by = Auth::user()->id;
            $currency->lastedit_date = date("Y-m-d H:i:s");
            if ($currency->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_currency'));
                return Redirect::to("admin/recruitment_currencies/$currency->id/edit");
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
        $currency = RecruitmentCurrency::findOrFail($id);
        $currency->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $currency = RecruitmentCurrency::findOrFail($id);
            if ($published == 'no') {
                $currency->published = 'no';
                $currency->unpublished_by = Auth::user()->id;
                $currency->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $currency->published = 'yes';
                $currency->published_by = Auth::user()->id;
                $currency->published_date = date("Y-m-d H:i:s");
            }
            $currency->save();
        } else {
            return redirect(404);
        }
    }
}
