<?php

namespace App\Http\Controllers\Admin;

use App\BooksPlaylist;
use App\Books;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class BooksPlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Books::select('books.*')->get();
        return view('auth.books_playlist.view', compact('books'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $books_playlist = BooksPlaylist::select('books_playlist.*','books.title AS book_title')->leftJoin('books','books_playlist.book_id','=','books.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $books_playlist = $books_playlist->where('books_playlist.id', '=', "$id");
        }
        if (isset($data['book']) && !empty($data['book'])) {
            $book_id = $data['book'];
            $books_playlist = $books_playlist->where('books.id', '=', $book_id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $books_playlist = $books_playlist->where('books_playlist.name', 'LIKE', "%$name%");
        }
        if (isset($data['view']) && !empty($data['view'])) {
            $view = $data['view'];
            $books_playlist = $books_playlist->where('books_playlist.view', '=', $view);
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $books_playlist = $books_playlist->whereBetween('books_playlist.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        $iTotalRecords = $books_playlist->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'books_playlist.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'books_playlist.id';
                break;
            case 1:
                $columnName = 'books.title';
                break;
            case 2:
                $columnName = 'books_playlist.name';
                break;
            case 3:
                $columnName = 'books_playlist.id';
                break;
            case 4:
                $columnName = 'books_playlist.view';
                break;
            case 5:
                $columnName = 'books_playlist.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $books_playlist = $books_playlist->where(function ($q) use ($search) {
                $q->where('books.title', 'LIKE', "%$search%")
                    ->orWhere('books_playlist.name', 'LIKE', "%$search%")
                    ->orWhere('books_playlist.id', '=', $search)
                    ->orWhere('books_playlist.view', '=', $search);
            });
        }

        $books_playlist = $books_playlist->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($books_playlist as $book) {
            $book_title=$book->book_title;
            if(PerUser('books_edit') && $book_title !=''){
                $book_title= '<a target="_blank" href="' . URL('admin/books/' . $book->book_id . '/edit') . '">' . $book_title . '</a>';
            }
            $records["data"][] = [
                $book->id,
                $book_title,
                $book->name,
                '<a href="' . URL('admin/bookplaylistdownload/'.$book->audio) . '">' . $book->audio . '</a>',
                $book->view,
                $book->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $book->id . '" type="checkbox" ' . ((!PerUser('books_playlist_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('books_playlist_publish')) ? 'class="changeStatues"' : '') . ' ' . (($book->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $book->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $book->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('books_playlist_edit')) ? '<li>
                                            <a href="' . URL('admin/books_playlist/' . $book->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('books_playlist_delete')) ? '<li>
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
        $books = Books::select('books.*')->get();
        return view('auth.books_playlist.add',compact('books'));
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
                'book' => 'required|exists:mysql2.books,id',
                'name' => 'required',
                'audio' => 'mimes:mpga,wav|required',
            ));
        if ($validator->fails()) {
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json(['success'=>false,'msg'=>$messsage]);
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $audio = $request->file('audio');
            $audio_name = uploadFileToE3melbusiness($audio,false,null,true);
            $books_playlist = new BooksPlaylist();
            $books_playlist->book_id = $data['book'];
            $books_playlist->name = $data['name'];
            $books_playlist->audio = $audio_name;
            $books_playlist->published = $published;
            if ($published == 'yes') {
                $books_playlist->published_by = Auth::user()->id;
                $books_playlist->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $books_playlist->unpublished_by = Auth::user()->id;
                $books_playlist->unpublished_date = date("Y-m-d H:i:s");
            }
            $books_playlist->lastedit_by = Auth::user()->id;
            $books_playlist->lastedit_date = date("Y-m-d H:i:s");
            $books_playlist->added_by = Auth::user()->id;
            $books_playlist->added_date = date("Y-m-d H:i:s");
            $books_playlist->added_date = date("Y-m-d H:i:s");
            if ($books_playlist->save()) {
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.insert') . Lang::get('main.books_playlist')  . '</li>';
                $messsage .= '</ul></div>';
                return response()->json(['success'=>true,'msg'=>$messsage]);
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
        $books_playlist = BooksPlaylist::findOrFail($id);
        $books = Books::select('books.*')->get();
        return view('auth.books_playlist.edit', compact('books','books_playlist'));
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
        $book = BooksPlaylist::findOrFail($id);
        $rules=array(
            'book' => 'required|exists:mysql2.books,id',
            'name' => 'required',
        );
        if ( $request->file('audio')){
            $rules['audio']='mimes:mpga,wav|required';
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
            if ( $request->file('audio')){
                $audio= $request->file('audio');
                $audioName = uploadFileToE3melbusiness($audio,false,null,true);
                $book->audio = $audioName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $book->name = $data['name'];
            $book->book_id = $data['book'];
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
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.update') . Lang::get('main.books_playlist'). '</li>';
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
        $book = BooksPlaylist::findOrFail($id);
        $book->deleted_at=date("Y-m-d H:i:s");
        $book->deleted_by=Auth::user()->id;
        $book->save();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $book = BooksPlaylist::find($id);
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
        try {
            $file = audioBooksFilePath() . $file_name;
            return \Response::make(file_get_contents($file), 200, [
                'Content-Type' => 'audio/mpeg',
                'Content-Disposition' => 'attachment; ' . $file_name,
            ]);
        }catch (\Exception $e){
            return redirect()->back();
        }

    }
}
