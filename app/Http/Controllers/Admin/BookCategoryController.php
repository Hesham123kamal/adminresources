<?php

namespace App\Http\Controllers\Admin;

use App\AllCategory;
use App\SubCategory;
use App\Books;
use App\BookCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class BookCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.books_categories.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $books_categories = BookCategory::leftjoin('categories','categories.id','=','books_categories.category_id')
            ->leftjoin('sup_categories','sup_categories.id','=','books_categories.sup_category_id')
            ->leftjoin('books','books.id','=','books_categories.book_id')
            ->select('books_categories.*','categories.name as category_name','sup_categories.name as sub_category_name','books.title as book_title');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $books_categories = $books_categories->where('books_categories.id', '=', $id);
        }
        if (isset($data['category']) && !empty($data['category'])) {
            $category = $data['category'];
            $books_categories = $books_categories->where('categories.name','LIKE', "%$category%");
        }
        if (isset($data['sub_category']) && !empty($data['sub_category'])) {
            $sub_category = $data['sub_category'];
            $books_categories = $books_categories->where('sup_categories.name','LIKE', "%$sub_category%");
        }
        if (isset($data['book']) && !empty($data['book'])) {
            $book = $data['book'];
            $books_categories = $books_categories->where('books.title','LIKE', "%$book%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $books_categories = $books_categories->whereBetween('books_categories.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $books_categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'books_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'books_categories.id';
                break;
            case 1:
                $columnName = 'categories.name';
                break;
            case 2:
                $columnName = 'sup_categories.name';
                break;
            case 3:
                $columnName = 'books.title';
                break;
            case 4:
                $columnName = 'books_categories.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $books_categories = $books_categories->where(function ($q) use ($search) {
                $q->where('books_categories.id', '=', $search)
                    ->orWhere('categories.name', 'LIKE', "%$search%")
                    ->orWhere('sup_categories.name', 'LIKE', "%$search%")
                    ->orWhere('books.title', 'LIKE', "%$search%");
            });
        }

        $books_categories = $books_categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($books_categories as $book_category) {
            $category_name = $book_category->category_name;
            $sub_category_name = $book_category->sub_category_name;
            $book_title = $book_category->book_title;
            if(PerUser('all_categories_edit') && $category_name !=''){
                $category_name= '<a target="_blank" href="' . URL('admin/all_categories/' . $book_category->category_id . '/edit') . '">' . $category_name . '</a>';
            }
            if(PerUser('sub_categories_edit') && $sub_category_name !=''){
                $sub_category_name= '<a target="_blank" href="' . URL('admin/sub_categories/' . $book_category->sup_category_id . '/edit') . '">' . $sub_category_name . '</a>';
            }
            if(PerUser('books_edit') && $book_title !=''){
                $book_title= '<a target="_blank" href="' . URL('admin/books/' . $book_category->book_id . '/edit') . '">' . $book_title . '</a>';
            }
            $records["data"][] = [
                $book_category->id,
                $category_name,
                $sub_category_name,
                $book_title,
                $book_category->createtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $book_category->id . '" type="checkbox" ' . ((!PerUser('books_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('books_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($book_category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $book_category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $book_category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('books_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/books_categories/' . $book_category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('books_categories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $book_category->id . '" >
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
        $books = Books::pluck('title', 'id');
        return view('auth.books_categories.add',compact('categories','sub_categories','books'));
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
                'book' =>'required|exists:mysql2.books,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $book_category = new BookCategory();
            $book_category->published = $published;
            $book_category->category_id = $data['category'];
            $book_category->sup_category_id = $data['sub_category'];
            $book_category->book_id = $data['book'];
            $book_category->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $book_category->published_by = Auth::user()->id;
                $book_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $book_category->unpublished_by = Auth::user()->id;
                $book_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $book_category->lastedit_by = Auth::user()->id;
            $book_category->added_by = Auth::user()->id;
            $book_category->added_date = date("Y-m-d H:i:s");
            $book_category->lastedit_date = date("Y-m-d H:i:s");
            if ($book_category->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.book_category'));
                return Redirect::to('admin/books_categories/create');
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
        $book_category = BookCategory::findOrFail($id);
        $categories = AllCategory::pluck('name', 'id');
        $sub_categories = SubCategory::where('category_id','=',$book_category->category_id)->pluck('name', 'id');
        $books = Books::pluck('title', 'id');
        return view('auth.books_categories.edit', compact('book_category','categories','sub_categories','books'));
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
        $book_category = BookCategory::findOrFail($id);
        $rules=array(
            'category' =>'required|exists:mysql2.categories,id',
            'sub_category' =>'required|exists:mysql2.sup_categories,id',
            'book' =>'required|exists:mysql2.books,id',
        );
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $book_category->category_id = $data['category'];
            $book_category->sup_category_id = $data['sub_category'];
            $book_category->book_id = $data['book'];
            if ($published == 'yes' && $book_category->published=='no') {
                $book_category->published_by = Auth::user()->id;
                $book_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $book_category->published=='yes') {
                $book_category->unpublished_by = Auth::user()->id;
                $book_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $book_category->published = $published;
            $book_category->lastedit_by = Auth::user()->id;
            $book_category->lastedit_date = date("Y-m-d H:i:s");
            if ($book_category->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.book_category'));
                return Redirect::to("admin/books_categories/$book_category->id/edit");
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
        $book_category = BookCategory::findOrFail($id);
        $book_category->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $book_category = BookCategory::findOrFail($id);
            if ($published == 'no') {
                $book_category->published = 'no';
                $book_category->unpublished_by = Auth::user()->id;
                $book_category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $book_category->published = 'yes';
                $book_category->published_by = Auth::user()->id;
                $book_category->published_date = date("Y-m-d H:i:s");
            }
            $book_category->save();
        } else {
            return redirect(404);
        }
    }
}
