<?php

namespace App\Http\Controllers\Admin;

use App\MbaCertificates;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MbaCertificatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.mba_certificates.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $certificates=MbaCertificates::leftjoin('users', 'users.id', '=', 'mba_certificates.user_id')
            ->select('mba_certificates.*', 'users.Email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $certificates = $certificates->where('mba_certificates.id', '=', $id);
        }
        if (isset($data['serial_number']) && !empty($data['serial_number'])) {
            $serial_number = $data['serial_number'];
            $certificates = $certificates->where('mba_certificates.serial_number', 'LIKE', "%$serial_number%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $certificates = $certificates->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['user_name']) && !empty($data['user_name'])) {
            $user_name = $data['user_name'];
            $certificates = $certificates->where('mba_certificates.user_name', 'LIKE', "%$user_name%");
        }
        if (isset($data['user_name_en']) && !empty($data['user_name_en'])) {
            $user_name_en = $data['user_name_en'];
            $certificates = $certificates->where('mba_certificates.user_name_en', 'LIKE', "%$user_name_en%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $certificates = $certificates->whereBetween('mba_certificates.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $certificates->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'mba_certificates.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mba_certificates.id';
                break;
            case 1:
                $columnName = 'mba_certificates.serial_number';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'mba_certificates.user_name';
                break;
            case 4:
                $columnName = 'mba_certificates.user_name_en';
                break;
            case 5:
                $columnName = 'mba_certificates.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $certificates = $certificates->where(function ($q) use ($search) {
                $q->where('mba_certificates.serial_number', 'LIKE', "%$search%")
                    ->orWhere('mba_certificates.user_name', 'LIKE', "%$search%")
                    ->orWhere('mba_certificates.user_name_en', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('mba_certificates.id', '=', $search);
            });
        }

        $certificates = $certificates->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($certificates as $certificate) {
            $user = $certificate->Email;
            $user_name=$certificate->user_name;
            $user_name_en=$certificate->user_name_en;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user . '</a>';
                $user_name= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user_name . '</a>';
                $user_name_en= '<a target="_blank" href="' . URL('admin/normal_user/' . $certificate->user_id . '/edit') . '">' . $user_name_en . '</a>';
            }
            $records["data"][] = [
                $certificate->id,
                $certificate->serial_number,
                $user,
                $user_name,
                $user_name_en,
                $certificate->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $certificate->id . '" type="checkbox" ' . ((!PerUser('mba_certificates_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('mba_certificates_publish')) ? 'class="changeStatues"' : '') . ' ' . (($certificate->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $certificate->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $certificate->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('mba_certificates_edit')) ? '<li>
                                            <a href="' . URL('admin/mba_certificates/' . $certificate->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('mba_certificates_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $certificate->id . '" >
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
        return view('auth.mba_certificates.add');
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
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'serial_number' => 'required',
            'user_name' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            $certificate = new MbaCertificates();
            $certificate->user_id = $user_id;
            $certificate->serial_number = $data['serial_number'];
            $certificate->user_name = $data['user_name'];
            $certificate->user_name_en = $data['user_name_en'];
            $certificate->session_user_id = 0;
            $certificate->diploma_id = 0;
            $certificate->createdtime = date("Y-m-d H:i:s");
            if ($certificate->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.mba_certificate'));
                return Redirect::to('admin/mba_certificates/create');
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
        $certificate=MbaCertificates::findOrFail($id);
        $user=isset($certificate->user)?$certificate->user->Email:'';
        return view('auth.mba_certificates.edit',compact('certificate','user'));
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
        $certificate = MbaCertificates::findOrFail($id);
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'serial_number' => 'required',
            'user_name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            $certificate->user_id = $user_id;
            $certificate->serial_number = $data['serial_number'];
            $certificate->user_name = $data['user_name'];
            $certificate->user_name_en = $data['user_name_en'];
            $certificate->session_user_id = 0;
            $certificate->diploma_id = 0;
            if ($certificate->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.mba_certificate'));
                return Redirect::to("admin/mba_certificates/$certificate->id/edit");
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
        $certificate = MbaCertificates::findOrFail($id);
        $certificate->delete();
    }

}
