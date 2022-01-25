<?php

namespace App\Http\Controllers\Admin;

use App\Contactus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class ContactusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.contactus.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $contacts = Contactus::select('contactus.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $contacts = $contacts->where('contactus.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $contacts = $contacts->where('contactus.name', 'LIKE', "%$name%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email	 = $data['email'];
            $contacts = $contacts->where('contactus.email', 'LIKE', "%$email%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone	 = $data['phone'];
            $contacts = $contacts->where('contactus.phone', 'LIKE', "%$phone%");
        }
        if (isset($data['message']) && !empty($data['message'])) {
            $message	 = $data['message'];
            $contacts = $contacts->where('contactus.message', 'LIKE', "%$message%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $contacts = $contacts->whereBetween('contactus.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $contacts->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'contactus.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'contactus.id';
                break;
            case 1:
                $columnName = 'contactus.name';
                break;
            case 2:
                $columnName = 'contactus.email';
                break;
            case 3:
                $columnName = 'contactus.message';
                break;
            case 4:
                $columnName = 'contactus.phone';
                break;
            case 5:
                $columnName = 'contactus.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $contacts = $contacts->where(function ($q) use ($search) {
                $q->where('contactus.id', '=', $search)
                    ->orWhere('contactus.name', 'Like', "%$search%")
                    ->orWhere('contactus.email', 'Like', "%$search%")
                    ->orWhere('contactus.message', 'Like', "%$search%")
                    ->orWhere('contactus.phone', 'Like', "%$search%");
            });
        }

        $contacts = $contacts->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($contacts as $contact) {
            $records["data"][] = [
                $contact->id,
                $contact->name,
                $contact->email,
                $contact->message,
                $contact->phone,
                $contact->createdtime,
                '',
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

}
