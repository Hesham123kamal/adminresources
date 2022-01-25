<?php

namespace App\Http\Controllers;

use App\Books;
use App\Courses;
use App\CoursesViews;
use App\Successstories;
use App\Webinars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function index()
    {
//        dd(date('Y-m-d', strtotime('2018-12-21 -3 months')));
        return view('auth.export');
    }
    function outputCSV($data,$file_name = 'file.csv') {
        # output headers so that the file is downloaded rather than displayed
        //header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file_name");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");
        $output="";
        # Then loop through the rows
        foreach ($data as $row) {
            # Add the rows to the body
            $output.=implode(',',$row)."\n";
        }
        # Close the stream off
        print chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
    }
    function outputCSV2($data,$file_name = 'file.csv') {
        # output headers so that the file is downloaded rather than displayed
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=$file_name");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");

        # Start the ouput
        $output = fopen("php://output", "w");

        # Then loop through the rows
        foreach ($data as $row) {
            # Add the rows to the body
            fputcsv($output, $row); // here you can change delimiter/enclosure
        }
        # Close the stream off
        fclose($output);
    }
    public function exportCoursesPercentage(){
        //38745
        $users_course_view_completed=DB::connection('mysql2')->table('users_course_view_completed')
            ->join('courses','courses.id','=','users_course_view_completed.course_id')
            ->join('users','users.id','=','users_course_view_completed.user_id');
        $users_course_view_completed=$users_course_view_completed->get();
        $name='users_course_view_completed_';
        $arrayData[]=['Course ID','User ID','Percentages'];
        foreach ($users_course_view_completed as $item){
            $arrayData[]=[
                $item->course_id,
                $item->user_id,
                ($item->total)?round(($item->completed/$item->total)*100):'total_error',
            ];
        }
        self::outputCSV($arrayData,$name.'.csv');
    }
    public function export(Request $request)
    {
        switch ($request->table){
            case 'workshop':
                $courses = Webinars::select('webinar.*','instractors.name AS instractor_name')->where('type','online')->leftJoin('instractors','instractors.id','=','webinar.instractor')->get();
                $coursesArray[] = ['ID', 'workshop name','Instructor','Created time', 'View'];
                foreach ($courses as $course) {
                    $coursesArray[] = [
                        $course->id,
                        $course->name,
                        $course->instractor_name,
                        $course->createdtime,
                        $course->view,
                    ];
                }
                $name = 'workshop_' . date('Y_m_d_H_i_s') . '_' . time();
            break;
            case 'webinar':
                $courses = Webinars::select('webinar.*','instractors.name AS instractor_name')->where('type','offline')->leftJoin('instractors','instractors.id','=','webinar.instractor')->get();
                $coursesArray[] = ['ID', 'webinar name','Instructor','Created time', 'View'];
                foreach ($courses as $course) {
                    $coursesArray[] = [
                        $course->id,
                        $course->name,
                        $course->instractor_name,
                        $course->createdtime,
                        $course->view,
                    ];
                }
                $name = 'webinars_' . date('Y_m_d_H_i_s') . '_' . time();
                break;
            case 'books':
                $courses = Books::select('books.*','author.name AS author_name')->leftJoin('author','author.id','=','books.author_id')->get();
                $coursesArray[] = ['ID', 'book name','Instructor','Created time', 'View'];
                foreach ($courses as $course) {
                    $coursesArray[] = [
                        $course->id,
                        $course->title,
                        $course->author_name,
                        $course->createdtime,
                        $course->view,
                    ];
                }
                $name = 'books_' . date('Y_m_d_H_i_s') . '_' . time();
                break;
            case 'successtories':
                $courses = Successstories::select('successtories.*','instractors.name AS instractor_name')->leftJoin('instractors','instractors.id','=','successtories.instractor')->get();
                $coursesArray[] = ['ID', 'success story name','Instructor','Created time', 'View'];
                foreach ($courses as $course) {
                    $coursesArray[] = [
                        $course->id,
                        $course->name,
                        $course->instractor_name,
                        $course->createdtime,
                        $course->view,
                    ];
                }
                $name = 'successtories_' . date('Y_m_d_H_i_s') . '_' . time();
                break;
            case 'courses':
                $courses = Courses::select('courses.*','instractors.name AS instractor_name')->leftJoin('instractors','instractors.id','=','courses.instractor')->get();
                $coursesArray[] = ['ID', 'Course name','Lectures','Instructor','Length', 'Created time', 'View','Show on'];
                foreach ($courses as $course) {
                    $coursesArray[] = [
                        $course->id,
                        $course->name,
                        $course->lectures,
                        $course->instractor_name,
                        $course->length,
                        $course->createdtime,
                        $course->view,
                        $course->show_on,
                    ];
                }
                $name = 'courses_' . date('Y_m_d_H_i_s') . '_' . time();
                break;
        }
        if(isset($coursesArray)){
            self::outputCSV($coursesArray,$name.'.csv');
        }
        return'';
        Excel::create($name, function ($excel) use ($coursesArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Courses');
            $excel->setCreator('')->setCompany(' ');
            $excel->setDescription('Courses');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('Courses', function ($sheet) use ($coursesArray) {
                $sheet->fromArray($coursesArray, null, 'A1', false, false);
            });

        })->download('xlsx');
    }

    public function search(Request $request)
    {
        $data = $request->input();
        if (isset($data['date_from']) && !empty($data['date_from']) && isset($data['date_to']) && !empty($data['date_to'])) {
            $date_from = $data['date_from'];
            $date_to = $data['date_to'];
            if (date('Y-m-d', strtotime($date_to)) >= date('Y-m-d', strtotime($date_from . ' +3 months'))) {
                $courses_views = CoursesViews::select('courses_views.*')
                    ->whereBetween('courses_views.createdtime', [$date_from, date('Y-m-d', strtotime($date_from . ' +3 months'))])
                    ->get();
//                dd($courses_views);
                $coursesViewsArray[] = ['id', 'course_id', 'user_id', 'count', 'createdtime'];
                foreach ($courses_views as $course_views) {
                    $coursesViewsArray[] = [
                        $course_views->id,
                        $course_views->course_id,
                        $course_views->user_id,
                        $course_views->count,
                        $course_views->createdtime,
                    ];
                }
                $name = 'courses_views_' . date('Y_m_d_H_i_s') . '_' . time();
                self::outputCSV($coursesViewsArray,$name.'.csv');
                return'';
                Excel::create($name, function ($excel) use ($coursesViewsArray) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Courses Views');
                    $excel->setCreator('E3melbusiness')->setCompany('E3melbusiness');
                    $excel->setDescription('Courses Views');

                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('Courses Views', function ($sheet) use ($coursesViewsArray) {
                        $sheet->fromArray($coursesViewsArray, null, 'A1', false, false);
                    });
                })->download('xlsx');
            } else {
                $courses_views = CoursesViews::select('courses_views.*')
                    ->whereBetween('courses_views.createdtime', [$date_from, $date_to])
                    ->get();
//                dd($courses_views);
                $coursesViewsArray[] = ['id', 'course_id', 'user_id', 'count', 'createdtime'];
                foreach ($courses_views as $course_views) {
                    $coursesViewsArray[] = [
                        $course_views->id,
                        $course_views->course_id,
                        $course_views->user_id,
                        $course_views->count,
                        $course_views->createdtime,
                    ];
                }
                $name = 'courses_views_' . date('Y_m_d_H_i_s') . '_' . time();
                self::outputCSV($coursesViewsArray,$name.'.csv');
                return'';
                Excel::create($name, function ($excel) use ($coursesViewsArray) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Courses Views');
                    $excel->setCreator('E3melbusiness')->setCompany('E3melbusiness');
                    $excel->setDescription('Courses Views');

                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('Courses Views', function ($sheet) use ($coursesViewsArray) {
                        $sheet->fromArray($coursesViewsArray, null, 'A1', false, false);
                    });
                })->download('xlsx');
            }
        } else {
            $courses_views = CoursesViews::where('createdtime', '>=', date('Y-m-d', strtotime('-3 months')))->get();
            $coursesViewsArray[] = ['id', 'course_id', 'user_id', 'count', 'createdtime'];
            foreach ($courses_views as $course_views) {
                $coursesViewsArray[] = [
                    $course_views->id,
                    $course_views->course_id,
                    $course_views->user_id,
                    $course_views->count,
                    $course_views->createdtime,
                ];
            }
            $name = 'courses_views_' . date('Y_m_d_H_i_s') . '_' . time();
            self::outputCSV($coursesViewsArray,$name.'.csv');
            return'';
            Excel::create($name, function ($excel) use ($coursesViewsArray) {

                // Set the spreadsheet title, creator, and description
                $excel->setTitle('Courses Views');
                $excel->setCreator('E3melbusiness')->setCompany('E3melbusiness');
                $excel->setDescription('Courses Views');

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('Courses Views', function ($sheet) use ($coursesViewsArray) {
                    $sheet->fromArray($coursesViewsArray, null, 'A1', false, false);
                });

            })->download('xlsx');
        }


    }

}
