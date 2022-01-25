<?php

namespace App\Http\Controllers\Admin;

use App\BooksRequests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class BooksRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.books_requests.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $books_requests = BooksRequests::join('country','country.id','=','books_requests.country')
                    ->select('books_requests.*','country.arab_name as country_name');

        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $books_requests = $books_requests->where('books_requests.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $books_requests = $books_requests->where('books_requests.name', 'LIKE', "%$name%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $books_requests = $books_requests->where('books_requests.phone', 'LIKE', "%$phone%");
        }
        if (isset($data['country']) && !empty($data['country'])) {
            $country = $data['country'];
            $books_requests = $books_requests->where('country.arab_name', 'LIKE', "%$country%");
        }
        if (isset($data['books_names']) && !empty($data['books_names'])) {
            $books_names = $data['books_names'];
            $books_requests = $books_requests->where('books_requests.books_names', 'LIKE', "%$books_names%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $books_requests = $books_requests->whereBetween('books_requests.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $books_requests->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'books_requests.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'books_requests.id';
                break;
            case 1:
                $columnName = 'books_requests.name';
                break;
            case 2:
                $columnName = 'books_requests.phone';
                break;
            case 3:
                $columnName = 'books_requests.books_names';
                break;
            case 4:
                $columnName = 'country.arab_name';
                break;
            case 5:
                $columnName = 'books_requests.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $books_requests = $books_requests->where(function ($q) use ($search) {
                $q->where('books_requests.name', 'LIKE', "%$search%")
                    ->orWhere('books_requests.phone', 'LIKE', "%$search%")
                    ->orWhere('books_requests.books_names', 'LIKE', "%$search%")
                    ->orWhere('country.arab_name', 'LIKE', "%$search%")
                    ->orWhere('books_requests.id', '=', $search);
            });
        }

        $books_requests = $books_requests->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($books_requests as $book_request) {
            $records["data"][] = [
                $book_request->id,
                $book_request->name,
                $book_request->phone,
                $book_request->books_names,
                $book_request->country_name,
                $book_request->createtime,''
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
        return response()->json($records)->setCallback($request->input('callback'));

    }

}
