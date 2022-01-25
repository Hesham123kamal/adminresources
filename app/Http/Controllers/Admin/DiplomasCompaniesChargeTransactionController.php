<?php

namespace App\Http\Controllers\Admin;

use App\DiplomaCourse;
use App\DiplomasCompaniesChargeTransaction;
use App\DiplomaUserCourse;
use App\DiplomsCoursesUsersPlan;
use App\NormalUser;
use App\Employee;
use App\Diplomas;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DiplomasCompaniesChargeTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $diplomas = Diplomas::pluck('name', 'id');
        $companies = Company::pluck('name', 'id');
        return view('auth.diplomas_companies_charge_transaction.view',compact('diplomas','companies'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $transactions=DiplomasCompaniesChargeTransaction::leftjoin('users', 'users.id', '=', 'diplomas_companies_charge_transaction.user_id')
            ->leftjoin('companies', 'companies.id', '=', 'diplomas_companies_charge_transaction.company_id')
            ->select('diplomas_companies_charge_transaction.*', 'users.Email','companies.name as company_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $transactions = $transactions->where('diplomas_companies_charge_transaction.id', '=', $id);
        }
        if (isset($data['amount']) && !empty($data['amount'])) {
            $amount = $data['amount'];
            $transactions = $transactions->where('diplomas_companies_charge_transaction.amount', 'LIKE', "%$amount%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $transactions = $transactions->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $transactions = $transactions->where('diplomas_companies_charge_transaction.diploma_id', '=', $diploma);
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $company = $data['company'];
            $transactions = $transactions->where('diplomas_companies_charge_transaction.company_id', '=', $company);
        }
        if (isset($data['currency']) && !empty($data['currency'])) {
            $currency = $data['currency'];
            $transactions = $transactions->where('diplomas_companies_charge_transaction.currency', 'LIKE', "%$currency%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $transactions = $transactions->whereBetween('diplomas_companies_charge_transaction.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        if (isset($data['start_date_from']) && !empty($data['start_date_from']) && isset($data['start_date_to']) && !empty($data['start_date_to'])) {
            $start_date_from = $data['start_date_from'];
            $start_date_to = $data['start_date_to'];
            $transactions = $transactions->whereBetween('diplomas_companies_charge_transaction.start_date', [$start_date_from .' 00:00:00', $start_date_to.' 23:59:59']);
        }
        if (isset($data['end_date_from']) && !empty($data['end_date_from']) && isset($data['end_date_to']) && !empty($data['end_date_to'])) {
            $end_date_from = $data['end_date_from'];
            $end_date_to = $data['end_date_to'];
            $transactions = $transactions->whereBetween('diplomas_companies_charge_transaction.end_date', [$end_date_from .' 00:00:00', $end_date_to.' 23:59:59']);
        }
        if (isset($data['suspend']) && !empty($data['suspend'])) {
            $suspend = $data['suspend'];
            $transactions = $transactions->where('diplomas_companies_charge_transaction.suspend', '=', $suspend);
        }
        if (isset($data['suspend_date_from']) && !empty($data['suspend_date_from']) && isset($data['suspend_date_to']) && !empty($data['suspend_date_to'])) {
            $suspend_date_from = $data['suspend_date_from'];
            $suspend_date_to = $data['suspend_date_to'];
            $transactions = $transactions->whereBetween('diplomas_companies_charge_transaction.suspend_date', [$suspend_date_from .' 00:00:00', $suspend_date_to.' 23:59:59']);
        }

        $iTotalRecords = $transactions->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'diplomas_companies_charge_transaction.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'diplomas_companies_charge_transaction.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'diplomas_companies_charge_transaction.diploma_name';
                break;
            case 3:
                $columnName = 'diplomas_companies_charge_transaction.amount';
                break;
            case 4:
                $columnName = 'diplomas_companies_charge_transaction.currency';
                break;
            case 5:
                $columnName = 'diplomas_companies_charge_transaction.createtime';
                break;
            case 6:
                $columnName = 'diplomas_companies_charge_transaction.start_date';
                break;
            case 7:
                $columnName = 'diplomas_companies_charge_transaction.end_date';
                break;
            case 8:
                $columnName = 'diplomas_companies_charge_transaction.suspend';
                break;
            case 9:
                $columnName = 'diplomas_companies_charge_transaction.suspend_date';
                break;
            case 10:
                $columnName = 'companies.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('diplomas_companies_charge_transaction.amount', 'LIKE', "%$search%")
                    ->orWhere('diplomas_companies_charge_transaction.currency', 'LIKE', "%$search%")
                    ->orWhere('diplomas_companies_charge_transaction.diploma_name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('companies.name', 'LIKE', "%$search%")
                    ->orWhere('diplomas_companies_charge_transaction.id', '=', $search)
                    ->orWhere('diplomas_companies_charge_transaction.suspend', '=', $search);
            });
        }

        $transactions = $transactions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($transactions as $transaction) {
            $user = $transaction->Email;
            $diploma = $transaction->diploma_name;
            $company = $transaction->company_name;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $transaction->user_id . '/edit') . '">' . $user . '</a>';
            }
            if(PerUser('diplomas_edit') && $diploma !=''){
                $diploma= '<a target="_blank" href="' . URL('admin/diplomas/' . $transaction->diploma_id . '/edit') . '">' . $diploma . '</a>';
            }
            if(PerUser('company_edit') && $company !=''){
                $company= '<a target="_blank" href="' . URL('admin/company/' . $transaction->company_id . '/edit') . '">' . $company . '</a>';
            }
            $records["data"][] = [
                $transaction->id,
                $user,
                $diploma,
                $transaction->amount,
                $transaction->currency,
                $transaction->createtime,
                $transaction->start_date,
                $transaction->end_date,
                $transaction->suspend,
                $transaction->suspend_date,
                $company,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $transaction->id . '" type="checkbox" ' . ((!PerUser('diplomas_companies_charge_transaction_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('diplomas_companies_charge_transaction_publish')) ? 'class="changeStatues"' : '') . ' ' . (($transaction->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $transaction->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('diplomas_companies_charge_transaction_edit')) ? '<li>
                                            <a href="' . URL('admin/dcct/' . $transaction->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diplomas_companies_charge_transaction_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $transaction->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diplomas_companies_charge_transaction_copy')) ? '<li>
                                            <a href="'.URL('admin/dcct/copy/'.$transaction->id).'" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.copy') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diplomas_companies_charge_transaction_add_courses')) ? '<li>
                                            <a class="add_all_courses" data-id="' . $transaction->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.add_courses') . '
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
        $employees=Employee::pluck('username', 'id');
        $diplomas=Diplomas::pluck('name', 'id');
        $companies=Company::pluck('name', 'id');
        return view('auth.diplomas_companies_charge_transaction.add',compact('employees','diplomas','companies'));
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
            'diploma' => 'required|exists:mysql2.diplomas,id',
            'company' => 'required|exists:mysql2.companies,id',
            'period' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            //'end_date' => 'required|date_format:"Y-m-d"',
            //'subscribe_type' => 'required|in:diploma_free,diploma_paid,diploma_onlinepayment',
            'subscribe_country' => 'required|in:egy,ksa',
            'currency' => 'required',
        );
        if($request->file('attach')){
            $rules['attach']= 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            $diploma=Diplomas::where('id', $data['diploma'])->first();
            $diploma_name=$diploma->name;
            $diploma_charge_transaction=DiplomasCompaniesChargeTransaction::where('user_id',$user_id)->where('diploma_id',$diploma->id)->where('company_id',$data['company'])->orderBy('end_date','DESC')->first();
            $start_date=date('Y-m-d H:i:s',strtotime($data['start_date']));
            $end_date=NULL;
            if(count($diploma_charge_transaction)){
                if($diploma_charge_transaction->end_date>$start_date){
                    $start_date=date('Y-m-d H:i:s',strtotime($diploma_charge_transaction->end_date.' +1 day'));
                }
            }
            $end_date=date('Y-m-d H:i:s',strtotime($start_date.' +'.$data['period'].' months'));
            $pending = (isset($data['pending'])) ? 1 : 0;
            $suspend = (isset($data['suspend'])) ? 1 : 0;
            $employee_id = (isset($data['employee'])) ? $data['employee'] : 0;
            $coupon_id = (isset($data['coupon_id'])) ? $data['coupon_id'] : 0;
            $diploma_price = (isset($data['diploma_price'])) ? $data['diploma_price'] : 0;
            $transaction = new DiplomasCompaniesChargeTransaction();
            $transaction->user_id = $user_id;
            $transaction->diploma_id = $data['diploma'];
            $transaction->company_id = $data['company'];
            $transaction->diploma_name = $diploma_name;
            $transaction->diploma_price = $diploma_price;
            $transaction->period = $data['period'];
            $transaction->start_date = $start_date;
            $transaction->end_date = $end_date;
            $transaction->pending = $pending;
            $transaction->subscrip_type = $data['subscribe_type'];
            $transaction->amount = $data['amount'];
            $transaction->subscrip_country = $data['subscribe_country'];
            $transaction->currency = $data['currency'];
            $transaction->coupon_id = $coupon_id;
            $transaction->employee_id = $employee_id;
            $transaction->suspend = $suspend;
            if ($suspend == 1) {
                $transaction->suspend_date = date("Y-m-d H:i:s");
            }
            if ( $request->file('attach')){
                $attach = $request->file('attach');
                $attach_name = uploadFileToE3melbusiness($attach);
                $transaction->attach = $attach_name;
            }
            $transaction->createtime = date("Y-m-d H:i:s");
            $transaction->added_date = date("Y-m-d H:i:s");
            $transaction->added_by = Auth::user()->id;
            $transaction->lastedit_by = Auth::user()->id;
            $transaction->lastedit_date = date("Y-m-d H:i:s");
            if ($transaction->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.diplomas_companies_charge_transaction'));
                return Redirect::to('admin/dcct/create');
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
        $transaction = DiplomasCompaniesChargeTransaction::findOrFail($id);
        $transaction->start_date = date("Y-m-d", strtotime($transaction->start_date));
        $transaction->end_date = date("Y-m-d", strtotime($transaction->end_date));
        $employees=Employee::pluck('username', 'id');
        $diplomas=Diplomas::pluck('name', 'id');
        $companies=Company::pluck('name', 'id');
        $user=isset($transaction->user)?$transaction->user->Email:'';
        return view('auth.diplomas_companies_charge_transaction.edit', compact('transaction','employees','diplomas','user','companies'));
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
        $transaction = DiplomasCompaniesChargeTransaction::findOrFail($id);
        $old_diploma_id = $transaction->diploma_id;
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'diploma' => 'required|exists:mysql2.diplomas,id',
            'company' => 'required|exists:mysql2.companies,id',
            'period' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'required|date_format:"Y-m-d"',
            //'subscribe_type' => 'required|in:diploma_free,diploma_paid,diploma_onlinepayment',
            'subscribe_country' => 'required|in:egy,ksa',
            'currency' => 'required',
        );
        if($request->file('attach')){
            $rules['attach']= 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            $diploma_name=Diplomas::where('id', $data['diploma'])->first()->name;
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $pending = (isset($data['pending'])) ? 1 : 0;
            $suspend = (isset($data['suspend'])) ? 1 : 0;
            $employee_id = (isset($data['employee'])) ? $data['employee'] : 0;
            $coupon_id = (isset($data['coupon_id'])) ? $data['coupon_id'] : 0;
            $diploma_price = (isset($data['diploma_price'])) ? $data['diploma_price'] : 0;
            $transaction->user_id = $user_id;
            $transaction->diploma_id = $data['diploma'];
            $transaction->company_id = $data['company'];
            $transaction->diploma_name = $diploma_name;
            $transaction->diploma_price = $diploma_price;
            $transaction->period = $data['period'];
            $transaction->start_date = $data['start_date'];
            $transaction->end_date = $data['end_date'];
            $transaction->pending = $pending;
            $transaction->subscrip_type = $data['subscribe_type'];
            $transaction->amount = $data['amount'];
            $transaction->subscrip_country = $data['subscribe_country'];
            $transaction->currency = $data['currency'];
            $transaction->coupon_id = $coupon_id;
            $transaction->employee_id = $employee_id;
            if ($transaction->suspend==0 && $suspend == 1) {
                $transaction->suspend_date = date("Y-m-d H:i:s");
            }
            $transaction->suspend = $suspend;
            if ( $request->file('attach')){
                $attach = $request->file('attach');
                $attach_name = uploadFileToE3melbusiness($attach);
                $transaction->attach = $attach_name;
            }
            $transaction->lastedit_by = Auth::user()->id;
            $transaction->lastedit_date = date("Y-m-d H:i:s");
            if($old_diploma_id!=$data['diploma']) {
            $diploma_user_courses=DiplomaUserCourse::where('diploma_id','=',$old_diploma_id)->where('user_id','=',$user_id)->get();
                if(count($diploma_user_courses)){
                    return redirect()->back()->withErrors(['You must remove diploma user courses of diploma first'])->withInput();
                }
            }
            if ($transaction->save()) {
                if($old_diploma_id!=$data['diploma']) {
                    $q = DiplomsCoursesUsersPlan::where('diploma_id', '=', $old_diploma_id)->where('user_id', '=', $user_id)->delete();
                    $diploma_courses = DiplomaCourse::where('diploma_id', '=', $data['diploma'])->get();
                    if (count($diploma_courses)) {
                        foreach ($diploma_courses as $course) {
                            if ($course->diploma_id == $data['diploma']) {
                                $new_plan = new DiplomsCoursesUsersPlan();
                                $new_plan->diploma_id = $course->diploma_id;
                                $new_plan->course_id = $course->related_course;
                                $new_plan->user_id = $user_id;
                                $new_plan->sort = $course->sort;
                                $new_plan->added_by = Auth::user()->id;
                                $new_plan->save();
                            }
                        }
                    }
                    $min_plan_sort = DiplomsCoursesUsersPlan::where('diploma_id', '=', $data['diploma'])->where('user_id', '=', $user_id)->min('sort');
                    $plans_with_min_sort = DiplomsCoursesUsersPlan::where('diploma_id', '=', $data['diploma'])->where('user_id', '=', $user_id)->where('sort', '=', $min_plan_sort)->get();
                    if (count($plans_with_min_sort)) {
                        foreach ($plans_with_min_sort as $plan_with_min_sort) {
                            $diploma_user_course = new DiplomaUserCourse();
                            $diploma_user_course->diploma_id = $plan_with_min_sort->diploma_id;
                            $diploma_user_course->course_id = $plan_with_min_sort->course_id;
                            $diploma_user_course->user_id = $plan_with_min_sort->user_id;
                            $diploma_user_course->exam = $plan_with_min_sort->exam;
                            $diploma_user_course->sort = $plan_with_min_sort->sort;
                            $diploma_user_course->added_by = Auth::user()->id;
                            $diploma_user_course->save();
                        }
                    }
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.diplomas_companies_charge_transaction'));
                return Redirect::to("admin/dcct/$transaction->id/edit");
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
        $transaction = DiplomasCompaniesChargeTransaction::findOrFail($id);
        $transaction->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $transaction = DiplomasCompaniesChargeTransaction::findOrFail($id);
//            if ($published == 'no') {
//                $transaction->published = 'no';
//                $transaction->unpublished_by = Auth::user()->id;
//                $transaction->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $transaction->published = 'yes';
//                $transaction->published_by = Auth::user()->id;
//                $transaction->published_date = date("Y-m-d H:i:s");
//            }
//            $transaction->save();
//        } else {
//            return redirect(404);
//        }
//    }
    public function copy($id)
    {
        $diploma_charge_transaction = DiplomasCompaniesChargeTransaction::findOrFail($id);
        $diploma_charge_transaction->createtime = date("Y-m-d H:i:s");
        $diploma_charge_transaction->replicate()->save();
        return Redirect::to('admin/dcct/'.$diploma_charge_transaction->id.'/edit');
    }
    public function addCourses($id,Request $request){
        $diplomas_companies_charge_transactions=DiplomasCompaniesChargeTransaction::findOrFail($id);
        $diploma_id=$diplomas_companies_charge_transactions->diploma_id;
        $user_id=$diplomas_companies_charge_transactions->user_id;
        $diplomas_courses=DiplomsCoursesUsersPlan::where('diploma_id',$diploma_id)->where('user_id',$user_id)->get();
        foreach ($diplomas_courses as $diplomas_course){
            $related_course=$diplomas_course->course_id;
            $sort=$diplomas_course->sort;
            $diploma_user_course=DiplomaUserCourse::where('diploma_id',$diploma_id)->where('user_id',$user_id)->where('course_id',$related_course)->get();
            if(!count($diploma_user_course)){
                $newDiplomaCourse=new DiplomaUserCourse();
                $newDiplomaCourse->diploma_id=$diploma_id;
                $newDiplomaCourse->course_id=$related_course;
                $newDiplomaCourse->user_id=$user_id;
                $newDiplomaCourse->createtime=date('Y-m-d H:i:s');
                $newDiplomaCourse->sort=$sort;
                $newDiplomaCourse->save();
            }
        }
        return response()->json(['message'=>'<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.Lang::get('main.success_adding_diplomas_companies_charge_transactions_courses').'</div>','success'=>true])->setCallback($request->input('callback'));
    }

}
