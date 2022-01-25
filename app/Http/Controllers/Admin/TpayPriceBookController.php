<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Http\Controllers\Controller;
use App\TpayPriceBook;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class TpayPriceBookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.tpay_price_book.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $rows = TpayPriceBook::leftjoin('country','country.id','=','tpay_price_book.country_id')
                            ->select('tpay_price_book.*','country.arab_name as country_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $rows = $rows->where('tpay_price_book.id', '=', $id);
        }
        if (isset($data['country']) && !empty($data['country'])) {
            $country = $data['country'];
            $rows = $rows->where('country.arab_name', 'LIKE', "%$country%");
        }
        if (isset($data['sku']) && !empty($data['sku'])) {
            $sku = $data['sku'];
            $rows = $rows->where('tpay_price_book.sku', 'LIKE', "%$sku%");
        }
        if (isset($data['operator_code']) && !empty($data['operator_code'])) {
            $operator_code = $data['operator_code'];
            $rows = $rows->where('tpay_price_book.operator_code', '=', $operator_code);
        }
        if (isset($data['operator_name']) && !empty($data['operator_name'])) {
            $operator_name = $data['operator_name'];
            $rows = $rows->where('tpay_price_book.operator_name', 'LIKE', "%$operator_name%");
        }
        if (isset($data['currency_symbol']) && !empty($data['currency_symbol'])) {
            $currency_symbol = $data['currency_symbol'];
            $rows = $rows->where('tpay_price_book.currency_symbol', '=', $currency_symbol);
        }
        if (isset($data['price']) && !empty($data['price'])) {
            $price = $data['price'];
            $rows = $rows->where('tpay_price_book.price', '=', $price);
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $rows = $rows->whereBetween('tpay_price_book.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $rows->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'tpay_price_book.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'tpay_price_book.id';
                break;
            case 1:
                $columnName = 'country.arab_name';
                break;
            case 2:
                $columnName = 'tpay_price_book.sku';
                break;
            case 3:
                $columnName = 'tpay_price_book.operator_code';
                break;
            case 4:
                $columnName = 'tpay_price_book.operator_name';
                break;
            case 5:
                $columnName = 'tpay_price_book.currency_symbol';
                break;
            case 6:
                $columnName = 'tpay_price_book.price';
                break;
            case 7:
                $columnName = 'tpay_price_book.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $rows = $rows->where(function ($q) use ($search) {
                $q->where('tpay_price_book.sku', 'LIKE', "%$search%")
                    ->orWhere('tpay_price_book.operator_code', '=', $search)
                    ->orWhere('tpay_price_book.id', '=', $search)
                    ->orWhere('tpay_price_book.currency_symbol', '=', $search)
                    ->orWhere('tpay_price_book.operator_name', 'LIKE', "%$search%")
                    ->orWhere('country.arab_name', 'LIKE', "%$search%")
                    ->orWhere('tpay_price_book.price', '=', $search);
            });
        }

        $rows = $rows->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($rows as $row) {
            $records["data"][] = [
                $row->id,
                $row->country_name,
                $row->sku,
                $row->operator_code,
                $row->operator_name,
                $row->currency_symbol,
                $row->price,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $row->id . '" type="checkbox" ' . ((!PerUser('tpay_price_book_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('tpay_price_book_publish')) ? 'class="changeStatues"' : '') . ' ' . (($row->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $row->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                $row->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $row->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('tpay_price_book_edit')) ? '<li>
                                            <a href="' . URL('admin/tpay_price_book/' . $row->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('tpay_price_book_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $row->id . '" >
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
        $country = Country::pluck('arab_name', 'id');
        return view('auth.tpay_price_book.add',compact('country'));
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
                'sku' => 'required',
                'country' => 'required|exists:mysql2.country,id',
                'operator_code' => 'required|numeric',
                'operator_name' => 'required',
                'currency_symbol' => 'required',
                'price' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $record = new TpayPriceBook();
            $record->country_id = $data['country'];
            $record->sku = $data['sku'];
            $record->operator_code = $data['operator_code'];
            $record->operator_name = $data['operator_name'];
            $record->currency_symbol = $data['currency_symbol'];
            $record->price = $data['price'];
            //$record->published = $published;
            $record->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $record->published_by = Auth::user()->id;
//                $record->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $record->unpublished_by = Auth::user()->id;
//                $record->unpublished_date = date("Y-m-d H:i:s");
//            }
            $record->lastedit_by = Auth::user()->id;
            $record->added_by = Auth::user()->id;
            $record->lastedit_date = date("Y-m-d H:i:s");
            $record->added_date = date("Y-m-d H:i:s");
            if ($record->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.tpay_price_book'));
                return Redirect::to('admin/tpay_price_book/create');
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
        $record = TpayPriceBook::findOrFail($id);
        $country = Country::pluck('arab_name', 'id');
        return view('auth.tpay_price_book.edit', compact('record','country'));
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
        $record = TpayPriceBook::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'country' => 'required|exists:mysql2.country,id',
                'sku' => 'required',
                'operator_code' => 'required|numeric',
                'operator_name' => 'required',
                'currency_symbol' => 'required',
                'price' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $record->country_id = $data['country'];
            $record->sku = $data['sku'];
            $record->operator_code = $data['operator_code'];
            $record->operator_name = $data['operator_name'];
            $record->currency_symbol = $data['currency_symbol'];
            $record->price = $data['price'];
//            if ($published == 'yes' && $record->published=='no') {
//                $record->published_by = Auth::user()->id;
//                $record->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $record->published=='yes') {
//                $record->unpublished_by = Auth::user()->id;
//                $record->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $record->published = $published;
            $record->lastedit_by = Auth::user()->id;
            $record->lastedit_date = date("Y-m-d H:i:s");
            if ($record->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.tpay_price_book'));
                return Redirect::to('admin/tpay_price_book/create');
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
        $record = TpayPriceBook::findOrFail($id);
        $record->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $record = TpayPriceBook::findOrFail($id);
//            if ($published == 'no') {
//                $record->published = 'no';
//                $record->unpublished_by = Auth::user()->id;
//                $record->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $record->published = 'yes';
//                $record->published_by = Auth::user()->id;
//                $record->published_date = date("Y-m-d H:i:s");
//            }
//            $record->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
