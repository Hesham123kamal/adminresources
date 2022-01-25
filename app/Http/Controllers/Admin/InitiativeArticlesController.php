<?php

namespace App\Http\Controllers\Admin;

use App\Articles;
use App\Authors;
use App\InitiativeSections;
use App\Tags;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class InitiativeArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authors = Authors::select('author.*')->get();
        $sections = InitiativeSections::pluck('title', 'id');
        return view('auth.initiative_articles.view', compact('authors','sections'));
    }


    function search(Request $request)
    {
        $data = $request->input();
        $articles = Articles::select('articles.*','author.name AS author_name','author.id AS author_id','author.id AS author_id')->leftJoin('author','articles.author_id','=','author.id')
                    ->leftJoin('tags_related', function($join)
                     {
                        $join->on('tags_related.src_id', '=', 'articles.id')->where('type','=','articles');
                     })
                    ->leftJoin('tags','tags.id','=','tags_related.tag_id')->distinct()
                    ->leftJoin('initiative_articles_sections','initiative_articles_sections.article_id','=','articles.id')
                    ->leftJoin('initiative_sections','initiative_sections.id','=','initiative_articles_sections.section_id')->distinct()
                    ->where('articles.category_id','=',1);
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $articles = $articles->where('articles.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $articles = $articles->where('articles.name', 'LIKE', "%$name%");
        }
        if (isset($data['section']) && !empty($data['section'])) {
            $section= $data['section'];
            $articles = $articles->where('initiative_sections.id', '=', "$section");
        }
        if (isset($data['author']) && !empty($data['author'])) {
            $author = $data['author'];
            $articles = $articles->where('articles.author_id', 'LIKE', "%$author%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $articles = $articles->where('articles.url', 'LIKE', "%$url%");
        }
        if (isset($data['tag']) && !empty($data['tag'])) {
            $tag= $data['tag'];
            $articles = $articles->where('tags.name', 'LIKE', "%$tag%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $articles = $articles->whereBetween('articles.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        $iTotalRecords = $articles->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'articles.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'articles.id';
                break;
            case 1:
                $columnName = 'articles.name';
                break;
            case 2:
                $columnName = 'initiative_sections.title';
                break;
            case 3:
                $columnName = 'author.name';
                break;
            case 5:
                $columnName = 'articles.url';
                break;
            case 6:
                $columnName = 'articles.id';
                break;
            case 7:
                $columnName = 'articles.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $articles = $articles->where(function ($q) use ($search) {
                $q->where('articles.name', 'LIKE', "%$search%")
                    ->orWhere('initiative_sections.title', 'LIKE', "%$search%")
                    ->orWhere('author.name', 'LIKE', "%$search%")
                    ->orWhere('articles.views', 'LIKE', "%$search%")
                    ->orWhere('articles.url', 'LIKE', "%$search%")
                    ->orWhere('articles.description', 'LIKE', "%$search%")
                    ->orWhere('articles.public', 'LIKE', "%$search%")
                    ->orWhere('articles.picpath', 'LIKE', "%$search%")
                    ->orWhere('tags.name', 'LIKE', "%$search%")
                    ->orWhere('articles.id', '=', $search);
            });
        }

        $articles = $articles->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($articles as $article) {
            $tags=$article->tag()->get();
            $section=$article->section()->get();
            $tags_string='';
            $section_string='';
            if(count($tags)){
                if(PerUser('tags_edit')) {
                    foreach ($tags as $tag) {
                        $tags_string .= '<a target="_blank" href="' . URL('admin/tags/' . $tag->id . '/edit') . '"><span class="badge badge-info">' . $tag->name . '</span></a>';
                    }
                }
                else{
                    foreach ($tags as $tag) {
                        $tags_string .= '<span class="badge badge-info">' . $tag->name . '</span>';
                    }
                }
            }
            if(count($section)){
                $section=$section[0];
                if(PerUser('initiative_sections_edit')) {
                    $section_string = '<a target="_blank" href="' . URL('admin/initiative_sections/' . $section->id . '/edit') . '">' . $section->title . '</a>';
                }
                else{
                    $section_string = $section->title;
                }
            }
            $article=makeDefaultImageGeneral($article,'picpath');
            $author=$article->author_name;
            if(PerUser('author_edit') && $author !=''){
                $author= '<a target="_blank" href="' . URL('admin/author/' . $article->author_id . '/edit') . '">' . $author . '</a>';
            }
            $records["data"][] = [
                $article->id,
                $article->name,
                $section_string,
                $author,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="100%" src="' . assetURL($article->picpath) . '"/></a>',
                '<a href="' . e3mURL('blog/' . $article->url) . '" target="_blank">' . $article->url . '</a>',
                $tags_string,
                $article->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $article->id . '" type="checkbox" ' . ((!PerUser('initiative_articles_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('initiative_articles_publish')) ? 'class="changeStatues"' : '') . ' ' . (($article->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $article->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $article->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('initiative_articles_edit')) ? '<li>
                                            <a href="' . URL('admin/initiative_articles/' . $article->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('initiative_articles_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $article->id . '" >
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
        $tags = Tags::get()->pluck('name', 'id');
        $sections = InitiativeSections::get()->pluck('title', 'id');
        return view('auth.initiative_articles.add', compact('authors','sections','tags'));
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
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'section' => 'required|exists:mysql2.initiative_sections,id',
                'description' => 'required',
                'picture' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'url' => 'required|unique:mysql2.articles,url',
                'public_title' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('picture');
            $picName = uploadFileToE3melbusiness($pic);
            $articles = new Articles();
            $articles->name = $data['name'];
            $articles->description = $data['description'];
            $articles->meta_description = $data['meta_description'];
            $articles->category_id = 1;
            $articles->article_date = $data['article_date'];
            $articles->public_title = $data['public_title'];
            $articles->author_id = $data['author'];
            $articles->picpath = $picName;
            $articles->url = str_replace(' ','-',$data['url']);
            $articles->published = $published;
            if ($published == 'yes') {
                $articles->published_by = Auth::user()->id;
                $articles->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $articles->unpublished_by = Auth::user()->id;
                $articles->unpublished_date = date("Y-m-d H:i:s");
            }
            $articles->added_by = Auth::user()->id;
            $articles->added_date = date("Y-m-d H:i:s");
            if ($articles->save()) {
                if(isset($data['tag'])){
                    $tags=(array)$data['tag'];
                    $pivotData = array_fill(0, count($tags), ['type' => 'articles','added_by'=>Auth::user()->id,'src_id'=>$articles->id]);
                    $syncData  = array_combine($tags, $pivotData);
                    $articles->tag()->sync($syncData);

                }
                $section=[$data['section']];
                $pivotData = array_fill(0, 1, ['article_id'=>$articles->id]);
                $syncData  = array_combine($section, $pivotData);
                $articles->section()->sync($syncData);

                Session::flash('success', Lang::get('main.insert') . Lang::get('main.article'));
                return Redirect::to('admin/initiative_articles/create');
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
        $article = Articles::findOrFail($id);
        $article=makeDefaultImageGeneral($article,'picpath');
        $authors = Authors::select('author.*')->get();
        $tags = Tags::get()->pluck('name', 'id');
        $sections = InitiativeSections::get()->pluck('title', 'id');
        $article_section=isset($article->section->pluck('id')->toArray()[0])?$article->section->pluck('id')->toArray()[0]:null;
        return view('auth.initiative_articles.edit', compact('authors','article','sections','tags','article_section'));
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
        $article = Articles::findOrFail($id);
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules=array(
            'name' => 'required',
            'section' => 'required|exists:mysql2.initiative_sections,id',
            'description' => 'required',
            'url' => "required|unique:mysql2.articles,url,$id,id",
            'public_title' => 'required',
        );
        if ( $request->file('picture')){
            $rules['picture']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            if ( $request->file('picture')){
                $pic = $request->file('picture');
                $picName = uploadFileToE3melbusiness($pic);
                $article->picpath = $picName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $article->name = $data['name'];
            $article->description = $data['description'];
            $article->meta_description = $data['meta_description'];
            $article->article_date = $data['article_date'];
            $article->public_title = $data['public_title'];
            $article->author_id = $data['author'];
            $old_url = $article->url;
            $article->url = str_replace(' ','-',$data['url']);
            if ($published == 'yes' && $article->published=='no') {
                $article->published_by = Auth::user()->id;
                $article->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $article->published=='yes') {
                $article->unpublished_by = Auth::user()->id;
                $article->unpublished_date = date("Y-m-d H:i:s");
            }
            $article->published = $published;
            $article->lastedit_by = Auth::user()->id;
            $article->lastedit_date = date("Y-m-d H:i:s");
            if ($article->save()) {
                if(isset($data['tag'])){
                    $tags=(array)$data['tag'];
                    $pivotData = array_fill(0, count($tags), ['type' => 'articles','added_by'=>Auth::user()->id,'src_id'=>$article->id]);
                    $syncData  = array_combine($tags, $pivotData);
                    $article->tag()->sync($syncData);

                }
                else{
                    $article->tag()->detach();
                }
                $article->section()->detach();
                $section=[$data['section']];
                $pivotData = array_fill(0, 1, ['article_id'=>$article->id]);
                $syncData  = array_combine($section, $pivotData);
                $article->section()->sync($syncData);
                if($old_url != $article->url){
                    saveOldUrl($id,'articles',$old_url,$article->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.article'));
                return Redirect::to("admin/initiative_articles/$article->id/edit");
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
        $article = Articles::find($id);
        if (count($article)) {
            $article->delete();
            $article->deleted_by = Auth::user()->id;
            $article->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $article = Articles::find($id);
            if ($published == 'no') {
                $article->published = 'no';
                $article->unpublished_by = Auth::user()->id;
                $article->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $article->published = 'yes';
                $article->published_by = Auth::user()->id;
                $article->published_date = date("Y-m-d H:i:s");
            }
            $article->save();
        } else {
            return redirect(404);
        }
    }
}
