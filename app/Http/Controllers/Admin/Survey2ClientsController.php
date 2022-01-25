<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Survey2Clients;
use Illuminate\Http\Request;

class Survey2ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.survey2_clients.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $clients = Survey2Clients::select('exam2_clients.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $clients = $clients->where('exam2_clients.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $clients = $clients->where('exam2_clients.name', 'LIKE', "%$name%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $clients = $clients->where('exam2_clients.email', 'LIKE', "%$email%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $clients = $clients->where('exam2_clients.phone', 'LIKE', "%$phone%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $clients = $clients->whereBetween('exam2_clients.created_at', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
        }

        $iTotalRecords = $clients->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'exam2_clients.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'exam2_clients.id';
                break;
            case 1:
                $columnName = 'exam2_clients.name';
                break;
            case 2:
                $columnName = 'exam2_clients.email';
                break;
            case 3:
                $columnName = 'exam2_clients.phone';
                break;
            case 4:
                $columnName = 'exam2_clients.created_at';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $clients = $clients->where(function ($q) use ($search) {
                $q->where('exam2_clients.id', '=', $search)
                    ->orWhere('exam2_clients.name', 'Like', "%$search%")
                    ->orWhere('exam2_clients.email', 'Like', "%$search%")
                    ->orWhere('exam2_clients.phone', 'Like', "%$search%");
            });
        }

        $clients = $clients->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($clients as $client) {
            $records["data"][] = [
                $client->id,
                $client->name,
                $client->email,
                $client->phone,
                $client->created_at->format('Y-m-d H:i:s'),
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
