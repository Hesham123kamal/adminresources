<?php

namespace App\Http\Controllers\Admin;

use App\NormalUser;
use App\AppleProducts;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class AppleProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.apple_products.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $apple_products = AppleProducts::select('apple_products.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $apple_products = $apple_products->where('apple_products.id', '=', $id);
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $apple_products = $apple_products->where('apple_products.type', 'LIKE', "%$type%");
        }
        if (isset($data['apple_product_id']) && !empty($data['apple_product_id'])) {
            $apple_product_id = $data['apple_product_id'];
            $apple_products = $apple_products->where('apple_products.apple_product_id', 'LIKE', "%$apple_product_id%");
        }
        $iTotalRecords = $apple_products->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'apple_products.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'apple_products.id';
                break;
            case 1:
                $columnName = 'apple_products.apple_product_id';
                break;
            case 2:
                $columnName = 'apple_products.type';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $apple_products = $apple_products->where(function ($q) use ($search) {
                $q->where('apple_products.id', '=', $search)
                    ->orWhere('apple_products.apple_product_id', 'LIKE', "%$search%")
                    ->orWhere('apple_products.type', 'LIKE', "%$search%");
            });
        }

        $apple_products = $apple_products->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($apple_products as $apple_product) {
            $records["data"][] = [
                $apple_product->id,
                $apple_product->apple_product_id,
                $apple_product->type,
                '<div class="btn-group text-center" id="single-order-' . $apple_product->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('apple_products_edit')) ? '<li>
                                            <a href="' . URL('admin/apple_products/' . $apple_product->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('apple_products_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $apple_product->id . '" >
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
        return view('auth.apple_products.add');
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
        $validator = Validator::make($request->all(),array(
            'apple_product_id' => 'required',
            'type' => 'required',
        ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $apple_product= new AppleProducts();
            $apple_product->apple_product_id = $data['apple_product_id'];
            $apple_product->type = $data['type'];
            if ($apple_product->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.apple_products'));
                return Redirect::to('admin/apple_products/create');
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
        $apple_product = AppleProducts::findOrFail($id);
        return view('auth.apple_products.edit', compact('apple_product'));
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
        $apple_product = AppleProducts::findOrFail($id);
        $validator = Validator::make($request->all(), array(
            'apple_product_id' => 'required',
            'type' => 'required',
        ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $apple_product->apple_product_id = $data['apple_product_id'];
            $apple_product->type = $data['type'];
            if ($apple_product->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.apple_products'));
                return Redirect::to("admin/apple_products/$apple_product->id/edit");
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
        $apple_product = AppleProducts::findOrFail($id);
        $apple_product->delete();
    }

}
