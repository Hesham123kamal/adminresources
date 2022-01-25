<?php

namespace App\Http\Controllers\Admin;

use App\Authors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.author.view');
    }


    function search(Request $request)
    {

        $data = $request->input();
        $authors = Authors::select('author.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $authors = $authors->where('author.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $authors = $authors->where('author.name', 'LIKE', "%$name%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $authors = $authors->where('author.email', 'LIKE', "%$email%");
        }
        if (isset($data['country']) && !empty($data['country'])) {
            $country = $data['country'];
            $authors = $authors->where('author.country', 'LIKE', "%$country%");
        }
        

        $iTotalRecords = $authors->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'author.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'author.id';
                break;
            case 1:
                $columnName = 'author.name';
                break;
            case 2:
                $columnName = 'author.email';
                break;
            case 3:
                $columnName = 'author.country';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $authors = $authors->where(function ($q) use ($search) {
                $q->where('author.name', 'LIKE', "%$search%")
                    ->orWhere('author.email', 'LIKE', "%$search%")
                    ->orWhere('author.country', 'LIKE', "%$search%")
                    ->orWhere('author.id', '=', $search);
            });
        }

        $authors = $authors->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($authors as $author) {
            $records["data"][] = [
                $author->id,
                $author->name,
                $author->email,
                $author->country,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $author->id . '" type="checkbox" ' . ((!PerUser('author_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('author_publish')) ? 'class="changeStatues"' : '') . ' ' . (($author->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $author->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $author->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('author_edit')) ? '<li>
                                            <a href="' . URL('admin/author/' . $author->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('author_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $author->id . '" >
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
        return view('auth.author.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'email' => 'required|email',
                'country' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $authors = new Authors();
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $authors->name = $data['name'];
            $authors->email = $data['email'];
            $authors->country = $data['country'];
            $authors->added_by = Auth::user()->id;
            $authors->added_date = date("Y-m-d H:i:s");
            $authors->published = $published;
            if ($published == 'yes') {
                $authors->published_by = Auth::user()->id;
                $authors->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $authors->unpublished_by = Auth::user()->id;
                $authors->unpublished_date = date("Y-m-d H:i:s");
            }
            if ($authors->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.author'));
                return Redirect::to('admin/author/create');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $author = Authors::find($id);
        return view('auth.author.edit',compact('author'));
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
        $data = $request->input();
        $author = Authors::find($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'email' => 'required|email',
                'country' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $author->name = $data['name'];
            $author->email = $data['email'];
            $author->country = $data['country'];
            if ($published == 'yes' && $author->published=='no') {
                $author->published_by = Auth::user()->id;
                $author->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $author->published=='yes') {
                $author->unpublished_by = Auth::user()->id;
                $author->unpublished_date = date("Y-m-d H:i:s");
            }
            $author->published = $published;
            $author->lastedit_by = Auth::user()->id;
            $author->lastedit_date = date("Y-m-d H:i:s");
            if ($author->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.author'));
                return Redirect::to("admin/author/ $author->id/edit");
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
        $author = Authors::findOrFail($id);
        $author->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $author = Authors::findOrFail($id);
            if ($published == 'no') {
                $author->published = 'no';
                $author->unpublished_by = Auth::user()->id;
                $author->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $author->published = 'yes';
                $author->published_by = Auth::user()->id;
                $author->published_date = date("Y-m-d H:i:s");
            }
            $author->save();
        } else {
            return redirect(404);
        }
    }
}
