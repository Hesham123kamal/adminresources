<?php

namespace App\Http\Controllers\Admin;

use App\Authors;
use App\Books;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authors = Authors::select('author.*')->get();
        return view('auth.books.view', compact('authors'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $books = Books::select('books.*','author.name AS author_name','author.id AS author_id')->leftJoin('author','books.author_id','=','author.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $books = $books->where('books.id', '=', "$id");
        }
        if (isset($data['title']) && !empty($data['title'])) {
            $title = $data['title'];
            $books = $books->where('books.title', 'LIKE', "%$title%");
        }
        if (isset($data['title_en']) && !empty($data['title_en'])) {
            $title_en = $data['title_en'];
            $books = $books->where('books.title_en', 'LIKE', "%$title_en%");
        }
        if (isset($data['author']) && !empty($data['author'])) {
            $author = $data['author'];
            $books = $books->where('books.author_id', '=', $author);
        }
        if (isset($data['book']) && !empty($data['book'])) {
            $book = $data['book'];
            $books = $books->where('books.book', 'LIKE', "%$book%");
        }
        if (isset($data['rating']) && !empty($data['rating'])) {
            $rating = $data['rating'];
            $books = $books->where('books.rating', '=', $rating);
        }
        if (isset($data['rating_count']) && !empty($data['rating_count'])) {
            $rating_count = $data['rating_count'];
            $books = $books->where('books.rating_count', '=', $rating_count);
        }
        if (isset($data['view']) && !empty($data['view'])) {
            $view = $data['view'];
            $books = $books->where('books.view', '=', $view);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $books = $books->whereBetween('books.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $books = $books->where('books.url', 'LIKE', "%$url%");
        }

        $iTotalRecords = $books->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'books.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'books.id';
                break;
            case 1:
                $columnName = 'books.title';
                break;
            case 2:
                $columnName = 'books.title_en';
                break;
            case 3:
                $columnName = 'author.name';
                break;
            case 4:
                $columnName = 'books.book';
                break;
            case 5:
                $columnName = 'books.url';
                break;
            case 6:
                $columnName = 'books.rating';
                break;
            case 7:
                $columnName = 'books.rating_count';
                break;
            case 8:
                $columnName = 'books.view';
                break;
            case 9:
                $columnName = 'books.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $books = $books->where(function ($q) use ($search) {
                $q->where('books.title', 'LIKE', "%$search%")
                    ->orWhere('books.title_en', 'LIKE', "%$search%")
                    ->orWhere('author.name', 'LIKE', "%$search%")
                    ->orWhere('books.book', 'LIKE', "%$search%")
                    ->orWhere('books.url', 'LIKE', "%$search%")
                    ->orWhere('books.description_en', 'LIKE', "%$search%")
                    ->orWhere('books.description', 'LIKE', "%$search%")
                    ->orWhere('books.meta_description', 'LIKE', "%$search%")
                    ->orWhere('books.id', '=', $search)
                    ->orWhere('books.rating', '=', $search)
                    ->orWhere('books.rating_count', '=', $search)
                    ->orWhere('books.view', '=', $search);
            });
        }

        $books = $books->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($books as $book) {
            $author=$book->author_name;
            if(PerUser('author_edit') && $author !=''){
                $author= '<a target="_blank" href="' . URL('admin/author/' . $book->author_id . '/edit') . '">' . $author . '</a>';
            }
            $records["data"][] = [
                $book->id,
                $book->title,
                $book->title_en,
                $author,
                '<a href="' . URL('admin/bookdownload/'.$book->book) . '">' . $book->book . '</a>',
                '<a href="' . e3mURL('books/' . $book->url) . '" target="_blank">' . $book->url . '</a>',
                $book->rating,
                $book->rating_count,
                $book->view,
                $book->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $book->id . '" type="checkbox" ' . ((!PerUser('books_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('books_publish')) ? 'class="changeStatues"' : '') . ' ' . (($book->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $book->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $book->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('books_edit')) ? '<li>
                                            <a href="' . URL('admin/books/' . $book->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('books_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $book->id . '" >
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
        $authors = Authors::select('author.*')->get();
        return view('auth.books.add',compact('authors'));
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
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $validator = Validator::make($request->all(),
            array(
                'title' => 'required',
                'book' => 'required|mimes:pdf',
                'picture' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'url' => 'required|unique:mysql2.books,url',
                'meta_description' => 'required',
            ));
        if ($validator->fails()) {
            //return redirect()->back()->withErrors($validator->errors())->withInput();
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json($messsage);
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $book = $request->file('book');
            $bookName = uploadFileToE3melbusiness($book);
            $pic = $request->file('picture');
            $picName = uploadFileToE3melbusiness($pic);
            $books = new Books();
            $books->title = $data['title'];
            $books->title_en = $data['title_en'];
//            $books->description = $data['description'];
            $books->description = isset($data['description'])?$data['description']:'';
            $books->description_en = isset($data['description_en'])?$data['description_en']:'';
            $book->short_description = isset($data['short_description'])?$data['short_description']:'';
            $books->author_id = isset($data['author'])?$data['author']:0;
            $books->book = $bookName;
            $books->image = $picName;
            $books->url = str_replace(' ','-',$data['url']);
            $books->meta_description = $data['meta_description'];
            $books->published = $published;
            if ($published == 'yes') {
                $books->published_by = Auth::user()->id;
                $books->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $books->unpublished_by = Auth::user()->id;
                $books->unpublished_date = date("Y-m-d H:i:s");
            }
            $books->lastedit_by = Auth::user()->id;
            $books->lastedit_date = date("Y-m-d H:i:s");
            $books->added_by = Auth::user()->id;
            $books->added_date = date("Y-m-d H:i:s");
            if ($books->save()) {
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.insert') . Lang::get('main.book')  . '</li>';
                $messsage .= '</ul></div>';
                return response()->json($messsage);
                //Session::flash('success', Lang::get('main.insert') . Lang::get('main.books'));
//                return Redirect::to('admin/webinars/create');
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
        $authors = Authors::select('author.*')->get();
        $book = Books::find($id);
        return view('auth.books.edit', compact('authors','book'));
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
        $book = Books::findOrFail($id);
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules=array(
            'title' => 'required',
            'meta_description' => 'required',
            'url' => "required|unique:mysql2.books,url,$id,id",
        );
        if ( $request->file('picture')){
            $rules['picture']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        if ( $request->file('book')){
            $rules['book']='mimes:pdf';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json($messsage);
        }else {
            if ( $request->file('picture')){
                $pic = $request->file('picture');
                $picName = uploadFileToE3melbusiness($pic);
                $book->image = $picName;
            }
            if ( $request->file('book')){
                $book_input = $request->file('book');
                $bookName = uploadFileToE3melbusiness($book_input);
                $book->book = $bookName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $book->title = $data['title'];
            $book->title_en = $data['title_en'];
//            $book->description = $data['description'];
//            $book->author_id = $data['author'];
            $book->description = isset($data['description'])?$data['description']:'';
            $book->description_en = isset($data['description_en'])?$data['description_en']:'';
            $book->short_description = isset($data['short_description'])?$data['short_description']:'';
            $book->author_id = isset($data['author'])?$data['author']:0;
            $old_url=$book->url;
            $book->url = str_replace(' ','-',$data['url']);
            $book->meta_description = $data['meta_description'];
            if ($published == 'yes' && $book->published=='no') {
                $book->published_by = Auth::user()->id;
                $book->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $book->published=='yes') {
                $book->unpublished_by = Auth::user()->id;
                $book->unpublished_date = date("Y-m-d H:i:s");
            }
            $book->published = $published;
            $book->lastedit_by = Auth::user()->id;
            $book->lastedit_date = date("Y-m-d H:i:s");
            if ($book->save()) {
                if($old_url != $book->url){
                    saveOldUrl($id,'books',$old_url,$book->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.update') . Lang::get('main.book'). '</li>';
                $messsage .= '</ul></div>';
                return response()->json($messsage);
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
        $book = Books::find($id);
        $book->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $book = Books::find($id);
            if ($published == 'no') {
                $book->published = 'no';
                $book->unpublished_by = Auth::user()->id;
                $book->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $book->published = 'yes';
                $book->published_by = Auth::user()->id;
                $book->published_date = date("Y-m-d H:i:s");
            }
            $book->save();
        } else {
            return redirect(404);
        }
    }

    public function download($file_name)
    {
        $file = filePath().$file_name;
        return \Response::make(file_get_contents($file), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; '.$file_name,
        ]);

    }
}
