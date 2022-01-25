<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

//Clear Config cache:
Route::get('/config-clear', function() {
    $exitCode = Artisan::call('config:clear');
    return '<h1>Clear Config cleared</h1>';
});
Route::get('loginWithUserID/{userid}',function($userid){
    Auth::logout();
    Auth::loginUsingId($userid);
    return Redirect::to('/admin');
});
Route::post('/APINotifications/saveTokenNumber', 'APINotificationsController@saveTokenNumber');
Route::post('/APINotifications/sendNotifications', 'APINotificationsController@sendNotifications');


Route::get('/', function () {
    // dd($_SERVER);
    return Redirect::to('/admin');
    //return view('welcome');
});
Route::get('updateAcademyChargeTransaction', function () {
    // dd($_SERVER);
    updateAcademyChargeTransaction(5733);
    //return view('welcome');
});
/*auth routes*/
Auth::routes();
/*authentication routes*/
Route::group(['middleware' => 'PermissionsAuth'], function () {
    /*home page admin route*/
    Route::get('/admin', 'Admin\AdminController@home');
    Route::group(['prefix' => 'admin'], function () {
        Route::get('run_query', ['uses' => 'Admin\TestController@run_sql_file', 'as' => 'users_view'] );
        /*users routes*/
        Route::resource('users', 'Admin\UsersController', ['names' => ['create' => 'users_add', 'store' => 'users_add', 'index' => 'users', 'edit' => 'users_edit', 'update' => 'users_edit', 'destroy' => 'users_delete']]);
        Route::post('users_search', ['uses' => 'Admin\UsersController@search', 'as' => 'users_view']);
        Route::post('users/activation', ['uses' => 'Admin\UsersController@activation', 'as' => 'users_active']);
        Route::get('users_suspend', ['uses' => 'Admin\UserssuspendController@index', 'as' => 'users_suspend']);
        Route::post('users_suspend_search', ['uses' => 'Admin\UserssuspendController@search', 'as' => 'users_suspend_view']);

        /*profiles permission routes*/
        Route::resource('mba_progress', 'Admin\MbaProgressController', ['names' => ['create' => 'mba_progress', 'store' => 'mba_progress', 'index' => 'mba_progress' ]]);
        Route::post('mba_progress_search', ['uses' => 'Admin\MbaProgressController@search', 'as' => 'mba_progress']);
        Route::get('mba_progress_export', ['uses' => 'Admin\MbaProgressController@export', 'as' => 'mba_progress']);

        Route::post('mba_emails_search', ['uses' => 'Admin\MbaProgressController@emails_search', 'as' => 'mba_progress']);
        Route::get('mba_emails', ['uses' => 'Admin\MbaProgressController@emails', 'as' => 'mba_progress']);


        Route::resource('profiles', 'Admin\ProfilesController', ['names' => ['create' => 'profiles_add', 'store' => 'profiles_add', 'index' => 'profiles', 'edit' => 'profiles_edit', 'update' => 'profiles_edit', 'destroy' => 'profiles_delete']]);
        Route::post('profiles_search', ['uses' => 'Admin\ProfilesController@search', 'as' => 'profiles_view']);
        Route::post('profiles/activation', ['uses' => 'Admin\ProfilesController@activation', 'as' => 'profiles_active']);

        // working with resources
        Route::resource('courses_resources', 'Admin\CoursesResourcesController', ['names' => ['create' => 'courses_resources_add', 'store' => 'courses_resources_add', 'index' => 'courses_resources', 'edit' => 'courses_resources_edit', 'update' => 'courses_resources_edit', 'destroy' => 'courses_resources_delete']]);
        Route::post('courses_resources_search', ['uses' => 'Admin\CoursesResourcesController@search', 'as' => 'courses_resources_view']);
        Route::post('courses_resources/publish', ['uses' => 'Admin\CoursesResourcesController@publish', 'as' => 'courses_resources_publish']);
//        Route::post('course_resources_update/{id}', ['uses' => 'Admin\CoursesResourcesController@getUpdate', 'as' => 'courses_resources']);
        //Route::post('course_resources_download', ['uses' => 'Admin\CoursesResourcesController@downloadFile', 'as' => 'courses_resources_edit']);
        Route::get('course_resources_download/{file}', ['uses' => 'Admin\CoursesResourcesController@downloadFile', 'as' => 'courses_resources_edit']);

        // working with webinars
        Route::resource('webinar_resources', 'Admin\WebinarResourcesController', ['names' => ['create' => 'webinar_resources_add', 'store' => 'webinar_resources_add', 'index' => 'webinar_resources', 'edit' => 'webinar_resources_edit', 'update' => 'webinar_resources_edit', 'destroy' => 'webinar_resources_delete']]);
        Route::post('webinar_resources_search', ['uses' => 'Admin\WebinarResourcesController@search', 'as' => 'webinar_resources_view']);
//        Route::post('webinar_resources_update/{id}', ['uses' => 'Admin\WebinarResourcesController@getUpdate', 'as' => 'webinars_resources_edit']);

        // working with courses
        Route::resource('courses', 'Admin\CoursesController', ['names' => ['create' => 'courses_add', 'store' => 'courses_add', 'index' => 'courses', 'edit' => 'courses_edit', 'update' => 'courses_edit', 'destroy' => 'courses_delete']]);
        Route::post('courses_search', ['uses' => 'Admin\CoursesController@search', 'as' => 'courses_view']);
        Route::post('courses/publish', ['uses' => 'Admin\CoursesController@publish', 'as' => 'courses_publish']);
        Route::post('courses/getSubCategoriesByCategoryId', ['uses' => 'Admin\AllCategoryController@getSubCategoriesByCategoryId', 'as' => 'courses_add']);


//        Route::resource('modules_questions', 'Admin\ModulesQuestionsController', ['names' => ['create' => 'modules_questions_add', 'store' => 'modules_questions_add', 'index' => 'modules_questions', 'edit' => 'modules_questions_edit', 'update' => 'modules_questions_edit', 'destroy' => 'modules_questions_delete']]);
//        Route::post('modules_questions/getModulesQuestionsAJAX', ['uses' => 'Admin\ModulesQuestionsController@getModulesQuestionsAJAX', 'as' => 'modules_questions_view']);
//        Route::get('get_module_questions/{id}', ['uses' => 'Admin\ModulesQuestionsController@getModuleQuestions', 'as' => 'modules_questions_edit']);
//        Route::get('search_module_question', ['uses' => 'Admin\ModulesQuestionsController@searchModuleQuestions', 'as' => 'modules_questions_edit']);
//        Route::post('import_module_questions', ['uses' => 'Admin\ModulesQuestionsController@importModuleQuestions', 'as' => 'modules_questions_edit']);

        /* New version of Modules Questions */
        Route::resource('modules_questions2', 'Admin\ModulesQuestions2Controller', [
            'names' => [
                'create'  => 'modules_questions_add',
                'store'   => 'modules_questions_add',
                'index'   => 'modules_questions',
                'edit'    => 'modules_questions_edit',
                'update'  => 'modules_questions_edit',
                'destroy' => 'modules_questions_delete'
                ]
            ]
        );

        Route::GET('modules_questions2/{type}/{id}', [
            'uses' => 'Admin\ModulesQuestions2Controller@show', 'as' => 'modules_questions_edit'
        ]);
        Route::post('modules_questions2/getModulesQuestionsAJAX', [
            'uses' => 'Admin\ModulesQuestions2Controller@getModulesQuestionsAJAX', 'as' => 'modules_questions_view'
        ]);
        Route::get('get_module_questions2/{id}', [
            'uses' => 'Admin\ModulesQuestions2Controller@getModuleQuestions', 'as'  => 'modules_questions_edit'
        ]);
        Route::get('search_module_question2', [
            'uses' => 'Admin\ModulesQuestions2Controller@searchModuleQuestions', 'as' => 'modules_questions_edit'
        ]);
        Route::post('import_module_questions2', [
            'uses' => 'Admin\ModulesQuestions2Controller@importModuleQuestions', 'as' => 'modules_questions_edit'
        ]);
        Route::post('module_questions2_fetch', [
            'uses' => 'Admin\ModulesQuestions2Controller@fetchQuestions', 'as' => 'modules_questions_edit'
        ]);


        /* New version of Modules Trainings Questions */
        Route::resource('modules_trainings_questions2', 'Admin\ModulesTrainingsQuestions2Controller', [
                'names' => [
                    'create'  => 'modules_trainings_questions_add',
                    'store'   => 'modules_trainings_questions_add',
                    'index'   => 'modules_trainings_questions',
                    'edit'    => 'modules_trainings_questions_edit',
                    'update'  => 'modules_trainings_questions_edit',
                    'destroy' => 'modules_trainings_questions_delete'
                ]
            ]
        );

        Route::GET('modules_trainings_questions2/{type}/{id}', [
            'uses' => 'Admin\ModulesTrainingsQuestions2Controller@show', 'as' => 'modules_trainings_questions_edit'
        ]);
        Route::post('modules_trainings_questions2/getModulesTrainingsQuestionsAJAX', [
            'uses' => 'Admin\ModulesTrainingsQuestions2Controller@getModuleTrainingsQuestionsAJAX', 'as' => 'modules_trainings_questions_view'
        ]);
        Route::get('get_module_training_questions2/{id}', [
            'uses' => 'Admin\ModulesTrainingsQuestions2Controller@getTrainingQuestions', 'as'  => 'modules_trainings_questions_edit'
        ]);
        Route::get('search_module_training_question2', [
            'uses' => 'Admin\ModulesTrainingsQuestions2Controller@searchTrainingQuestions', 'as' => 'modules_trainings_questions_edit'
        ]);
        Route::post('import_module_training_questions2', [
            'uses' => 'Admin\ModulesTrainingsQuestions2Controller@importTrainingQuestions', 'as' => 'modules_trainings_questions_edit'
        ]);
        Route::post('module_training_questions2_fetch', [
            'uses' => 'Admin\ModulesTrainingsQuestions2Controller@fetchQuestions', 'as' => 'modules_trainings_questions_edit'
        ]);


        /*profile routes*/
        Route::get('profile', ['uses' => 'Admin\AdminController@profile', 'as' => 'profile']);
        Route::post('profile', ['uses' => 'Admin\AdminController@profilePost', 'as' => 'profile']);
        /*profile routes*/
        Route::get('system', ['uses' => 'Admin\AdminController@system', 'as' => 'system']);
        Route::post('system', ['uses' => 'Admin\AdminController@systemPost', 'as' => 'system']);

        Route::get('app_settings', ['uses' => 'Admin\AdminController@AppSettings', 'as' => 'app_settings_edit']);
        Route::put('app_settings', ['uses' => 'Admin\AdminController@AppSettingsEdit', 'as' => 'app_settings_edit']);

        Route::get('seo_sitemap', ['uses' => 'Admin\AdminController@SEOStieMap', 'as' => 'seo_sitemap_edit']);
        Route::put('seo_sitemap', ['uses' => 'Admin\AdminController@SEOStieMapEdit', 'as' => 'seo_sitemap_edit']);

        Route::get('subscription_prices', ['uses' => 'Admin\AdminController@subscriptionPrices', 'as' => 'subscription_prices_edit']);
        Route::put('subscription_prices', ['uses' => 'Admin\AdminController@subscriptionPricesEdit', 'as' => 'subscription_prices_edit']);

        //our_products
        Route::resource('our_products', 'Admin\OurProductsController', ['names' => ['create' => 'our_products_add', 'store' => 'our_products_add', 'index' => 'our_products', 'edit' => 'our_products_edit', 'update' => 'our_products_edit', 'destroy' => 'our_products_delete']]);
        Route::post('our_products/getOurProductsAJAX', ['uses' => 'Admin\OurProductsController@getOurProductsAJAX', 'as' => 'our_products_view']);
        Route::post('our_products/publish', ['uses' => 'Admin\OurProductsController@publish', 'as' => 'our_products_publish']);


        //our_products_courses
        Route::resource('our_products_courses', 'Admin\OurProductsCoursesController', ['names' => ['create' => 'our_products_courses_add', 'store' => 'our_products_courses_add', 'index' => 'our_products_courses', 'edit' => 'our_products_courses_edit', 'update' => 'our_products_courses_edit', 'destroy' => 'our_products_courses_delete']]);
        Route::post('our_products_courses/getOurProductsCoursesAJAX', ['uses' => 'Admin\OurProductsCoursesController@getOurProductsCoursesAJAX', 'as' => 'our_products_courses_view']);
        Route::post('our_products_courses/activation', ['uses' => 'Admin\OurProductsCoursesController@activation', 'as' => 'our_products_courses_active']);

        //modules_trainings
        Route::resource('modules_trainings', 'Admin\ModulesTrainingsController', ['names' => ['create' => 'modules_trainings_add', 'store' => 'modules_trainings_add', 'index' => 'modules_trainings', 'edit' => 'modules_trainings_edit', 'update' => 'modules_trainings_edit', 'destroy' => 'modules_trainings_delete']]);
        Route::post('modules_trainings/getModulesTrainingsAJAX', ['uses' => 'Admin\ModulesTrainingsController@getModulesTrainingsAJAX', 'as' => 'modules_trainings_view']);
        Route::post('modules_trainings/activation', ['uses' => 'Admin\ModulesTrainingsController@activation', 'as' => 'modules_trainings_active']);

        //modules_trainings_questions
//        Route::resource('modules_trainings_questions', 'Admin\ModulesTrainingsQuestionsController', ['names' => ['create' => 'modules_trainings_questions_add', 'store' => 'modules_trainings_questions_add', 'index' => 'modules_trainings_questions', 'edit' => 'modules_trainings_questions_edit', 'update' => 'modules_trainings_questions_edit', 'destroy' => 'modules_trainings_questions_delete']]);
//        Route::post('modules_trainings_questions/getModuleTrainingsQuestionsAJAX', ['uses' => 'Admin\ModulesTrainingsQuestionsController@getModuleTrainingsQuestionsAJAX', 'as' => 'modules_trainings_questions_view']);
//        Route::post('modules_trainings_questions/activation', ['uses' => 'Admin\ModulesTrainingsQuestionsController@activation', 'as' => 'modules_trainings_questions_active']);
//
//        Route::get('get_module_training/{id}', ['uses' => 'Admin\ModulesTrainingsQuestionsController@getModuleTraining', 'as' => 'modules_trainings_questions_edit']);
//        Route::get('get_training_questions/{id}', ['uses' => 'Admin\ModulesTrainingsQuestionsController@getTrainingQuestions', 'as' => 'modules_trainings_questions_edit']);
//        Route::get('search_training_question', ['uses' => 'Admin\ModulesTrainingsQuestionsController@searchTrainingQuestions', 'as' => 'modules_trainings_questions_edit']);
//        Route::post('import_training_questions', ['uses' => 'Admin\ModulesTrainingsQuestionsController@importTrainingQuestions', 'as' => 'modules_trainings_questions_edit']);

        Route::resource('modules', 'Admin\ModulesController', ['names' => ['create' => 'modules_add', 'store' => 'modules_add', 'index' => 'modules', 'edit' => 'modules_edit', 'update' => 'modules_edit', 'destroy' => 'modules_delete']]);
        Route::post('modules_search', ['uses' => 'Admin\ModulesController@search', 'as' => 'modules_view']);
        Route::post('modules/activation', ['uses' => 'Admin\ModulesController@activation', 'as' => 'modules_active']);

        /* Courses Questions Named Routes  */
//        Route::resource('courses_questions', 'Admin\CoursesQuestionsController', [
//            'names' => [
//                'create' => 'courses_questions_add',
//                'store' => 'courses_questions_add',
//                'index' => 'courses_questions',
//                'edit' => 'courses_questions_edit',
//                'update' => 'courses_questions_edit',
//                'destroy' => 'courses_questions_delete'
//            ]
//        ]);

        /* Courses Questions V3 */
        Route::resource('courses_questions3', 'Admin\CoursesQuestions3Controller', [
            'names' => [
                'create' => 'courses_questions_add',
                'store' => 'courses_questions_add',
                'index' => 'courses_questions',
                'show' => 'courses_questions_edit',
                'edit' => 'courses_questions_edit',
                'update' => 'courses_questions_edit',
                'destroy' => 'courses_questions_delete'
            ]
        ]);

        Route::GET('courses_questions3/{type}/{id}', ['uses' => 'Admin\CoursesQuestions3Controller@show', 'as' => 'courses_questions_edit']);
        Route::post('courses_questions3_search', ['uses' => 'Admin\CoursesQuestions3Controller@search', 'as' => 'courses_questions_view']);
        Route::post('courses_questions3_fetch', ['uses' => 'Admin\CoursesQuestions3Controller@fetchQuestions', 'as' => 'courses_questions_edit']);

//        Route::post('courses_questions_search', ['uses' => 'Admin\CoursesQuestionsController@search', 'as' => 'courses_questions_view']);
        Route::get('get_course_curriculum/{id}', ['uses' => 'Admin\CoursesQuestionsController@getCourseCurriculums', 'as' => 'courses_questions_edit']);
////        Route::get('get_curriculum_questions/{id}', ['uses' => 'Admin\CoursesQuestionsController@getCurriculumQuestions', 'as' => 'courses_questions_edit']);
//        Route::get('search_question', ['uses' => 'Admin\CoursesQuestionsController@searchCurriculumQuestions', 'as' => 'courses_questions_edit']);
//        Route::post('import_course_questions', ['uses' => 'Admin\CoursesQuestionsController@importCourseQuestions', 'as' => 'courses_questions_edit']);

        Route::resource('courses_questions2', 'Admin\CoursesQuestions2Controller', ['names' => ['create' => 'courses_questions_add', 'store' => 'courses_questions_add', 'index' => 'courses_questions', 'edit' => 'courses_questions_edit', 'update' => 'courses_questions_edit', 'destroy' => 'courses_questions_delete']]);
        Route::post('courses_questions_search2', ['uses' => 'Admin\CoursesQuestions2Controller@search', 'as' => 'courses_questions_view']);
        Route::get('get_course_curriculum2/{id}', ['uses' => 'Admin\CoursesQuestions2Controller@getCourseCurriculums', 'as' => 'courses_questions_edit']);
//        Route::get('get_curriculum_questions/{id}', ['uses' => 'Admin\CoursesQuestions2Controller@getCurriculumQuestions', 'as' => 'courses_questions_edit']);
        Route::get('get_curriculum_questions/{id}', ['uses' => 'Admin\CoursesQuestions3Controller@getCurriculumQuestions', 'as' => 'courses_questions_edit']);
        Route::get('search_question2', ['uses' => 'Admin\CoursesQuestions2Controller@searchCurriculumQuestions', 'as' => 'courses_questions_edit']);
        Route::post('import_course_questions2', ['uses' => 'Admin\CoursesQuestions2Controller@importCourseQuestions', 'as' => 'courses_questions_edit']);
        Route::post('add_new_question', ['uses' => 'Admin\CoursesQuestions2Controller@addNewQuestion', 'as' => 'courses_questions_add']);
        Route::post('import_question', ['uses' => 'Admin\CoursesQuestions2Controller@importQuestion', 'as' => 'courses_questions_edit']);
        Route::post('remove_question', ['uses' => 'Admin\CoursesQuestions2Controller@removeQuestion', 'as' => 'courses_questions_delete']);
        Route::post('question-image-upload', ['uses' => 'Admin\CoursesQuestions2Controller@imageUpload', 'as' => 'courses_questions_edit']);
        Route::post('remove-question-image', ['uses' => 'Admin\CoursesQuestions2Controller@imageRemove', 'as' => 'courses_questions_edit']);
        Route::post('delete-unsaved-questions', ['uses' => 'Admin\CoursesQuestions2Controller@deleteUnsavedQuestions', 'as' => 'courses_questions_edit']);


        //modules_projects
        Route::resource('modules_projects', 'Admin\ModulesProjectsController', ['names' => ['create' => 'modules_projects_add', 'store' => 'modules_projects_add', 'index' => 'modules_projects', 'edit' => 'modules_projects_edit', 'update' => 'modules_projects_edit', 'destroy' => 'modules_projects_delete']]);
        Route::post('modules_projects_search', ['uses' => 'Admin\ModulesProjectsController@search', 'as' => 'modules_projects_view']);
        Route::post('modules_projects/activation', ['uses' => 'Admin\ModulesProjectsController@activation', 'as' => 'modules_projects_active']);

        Route::resource('modules_courses', 'Admin\ModulesCoursesController', ['names' => ['create' => 'modules_courses_add', 'store' => 'modules_courses_add', 'index' => 'modules_courses', 'edit' => 'modules_courses_edit', 'update' => 'modules_courses_edit', 'destroy' => 'modules_courses_delete']]);
        Route::post('modules_courses_search', ['uses' => 'Admin\ModulesCoursesController@search', 'as' => 'modules_courses_view']);
        Route::post('modules_courses/activation', ['uses' => 'Admin\ModulesCoursesController@activation', 'as' => 'modules_courses_active']);

        Route::resource('modules_helper_courses', 'Admin\ModulesHelperCoursesController', ['names' => ['create' => 'modules_helper_courses_add', 'store' => 'modules_helper_courses_add', 'index' => 'modules_helper_courses', 'edit' => 'modules_helper_courses_edit', 'update' => 'modules_helper_courses_edit', 'destroy' => 'modules_helper_courses_delete']]);
        Route::post('modules_helper_courses_search', ['uses' => 'Admin\ModulesHelperCoursesController@search', 'as' => 'modules_helper_courses_view']);
        Route::post('modules_helper_courses/activation', ['uses' => 'Admin\ModulesHelperCoursesController@activation', 'as' => 'modules_helper_courses_active']);


        Route::resource('modules_users_projects', 'Admin\ModulesUsersProjectsController', ['names' => ['create' => 'modules_users_projects_add', 'store' => 'modules_users_projects_add', 'index' => 'modules_users_projects', 'edit' => 'modules_users_projects_edit', 'update' => 'modules_users_projects_edit', 'destroy' => 'modules_users_projects_delete']]);
        Route::post('modules_users_projects_search', ['uses' => 'Admin\ModulesUsersProjectsController@search', 'as' => 'modules_projects_view']);
        Route::put('modules_users_projects/{id}/editProjectStatus', ['uses' => 'Admin\ModulesUsersProjectsController@editProjectStatus', 'as' => 'modules_users_projects_edit']);
        Route::put('modules_users_projects/{id}/editProjectResult', ['uses' => 'Admin\ModulesUsersProjectsController@editProjectResult', 'as' => 'modules_users_projects_edit']);
        Route::post('modules_users_projects/upload_correction', ['uses' => 'Admin\ModulesUsersProjectsController@uploadCorrection', 'as' => 'modules_users_projects_edit']);
        Route::get('modules_users_projects/download_file/{id}', ['uses' => 'Admin\ModulesUsersProjectsController@downloadFile', 'as' => 'modules_projects_view']);


        Route::resource('instructors', 'Admin\InstructorsController', ['names' => ['create' => 'instructors_add', 'store' => 'instructors_add', 'index' => 'instructors', 'edit' => 'instructors_edit', 'update' => 'instructors_edit', 'destroy' => 'instructors_delete']]);
        Route::post('instructors_search', ['uses' => 'Admin\InstructorsController@search', 'as' => 'instructors_view']);
        Route::post('instructors/publish', ['uses' => 'Admin\InstructorsController@publish', 'as' => 'instructors_publish']);

        Route::resource('blocked_users', 'Admin\BlockedUsersController', ['names' => ['index' => 'blocked_users']]);
        Route::post('blocked_users_search', ['uses' => 'Admin\BlockedUsersController@search', 'as' => 'blocked_users_view']);


        Route::get('webinars/{id}/convert', ['uses' => 'Admin\WebinarsController@convert', 'as' => 'webinars_convert']);
        Route::resource('webinars', 'Admin\WebinarsController', ['names' => ['create' => 'webinars_add', 'store' => 'webinars_add', 'index' => 'webinars', 'edit' => 'webinars_edit', 'update' => 'webinars_edit', 'destroy' => 'webinars_delete']]);
        Route::post('webinars_search', ['uses' => 'Admin\WebinarsController@search', 'as' => 'webinars_view']);
        Route::post('webinars/activation', ['uses' => 'Admin\WebinarsController@activation', 'as' => 'webinars_active']);


        Route::resource('live', 'Admin\LiveController', ['names' => ['create' => 'live_add', 'store' => 'live_add', 'index' => 'live', 'edit' => 'live_edit', 'update' => 'live_edit', 'destroy' => 'live_delete']]);
        Route::post('live_search', ['uses' => 'Admin\LiveController@search', 'as' => 'live_view']);
        Route::post('live/activation', ['uses' => 'Admin\LiveController@activation', 'as' => 'live_active']);

        Route::resource('old_urls', 'Admin\OldUrlsController', ['names' => ['create' => 'old_urls_add', 'store' => 'old_urls_add', 'index' => 'old_urls', 'edit' => 'old_urls_edit', 'update' => 'old_urls_edit', 'destroy' => 'old_urls_delete']]);
        Route::post('old_urls_search', ['uses' => 'Admin\OldUrlsController@search', 'as' => 'old_urls_view']);

        Route::resource('books', 'Admin\BooksController', ['names' => ['create' => 'books_add', 'store' => 'books_add', 'index' => 'books', 'edit' => 'books_edit', 'update' => 'books_edit', 'destroy' => 'books_delete']]);
        Route::post('books_search', ['uses' => 'Admin\BooksController@search', 'as' => 'books_view']);
        Route::post('books/publish', ['uses' => 'Admin\BooksController@publish', 'as' => 'books_publish']);
        Route::get('bookdownload/{book}', ['uses' => 'Admin\BooksController@download', 'as' => 'books_edit']);

        Route::resource('books_playlist', 'Admin\BooksPlaylistController', ['names' => ['create' => 'books_playlist_add', 'store' => 'books_playlist_add', 'index' => 'books_playlist', 'edit' => 'books_playlist_edit', 'update' => 'books_playlist_edit', 'destroy' => 'books_playlist_delete']]);
        Route::post('books_playlist_search', ['uses' => 'Admin\BooksPlaylistController@search', 'as' => 'books_playlist_view']);
        Route::post('books_playlist/publish', ['uses' => 'Admin\BooksPlaylistController@publish', 'as' => 'books_playlist_publish']);
        Route::get('bookplaylistdownload/{book}', ['uses' => 'Admin\BooksPlaylistController@download', 'as' => 'books_playlist_edit']);

        Route::resource('diplomas', 'Admin\DiplomasController', ['names' => ['create' => 'diplomas_add', 'store' => 'diplomas_add', 'index' => 'diplomas', 'edit' => 'diplomas_edit', 'update' => 'diplomas_edit', 'destroy' => 'diplomas_delete']]);
        Route::post('diplomas_search', ['uses' => 'Admin\DiplomasController@search', 'as' => 'diplomas_view']);
        Route::post('diplomas/activation', ['uses' => 'Admin\DiplomasController@activation', 'as' => 'diplomas_active']);

        Route::resource('international_diplomas', 'Admin\InternationalDiplomasController', ['names' => ['create' => 'international_diplomas_add', 'store' => 'international_diplomas_add', 'index' => 'international_diplomas', 'edit' => 'international_diplomas_edit', 'update' => 'international_diplomas_edit', 'destroy' => 'international_diplomas_delete']]);
        Route::post('international_diplomas_search', ['uses' => 'Admin\InternationalDiplomasController@search', 'as' => 'international_diplomas_view']);
        Route::post('international_diplomas/activation', ['uses' => 'Admin\InternationalDiplomasController@activation', 'as' => 'international_diplomas_active']);

        Route::resource('successstories', 'Admin\SuccessstoriesController', ['names' => ['create' => 'successstories_add', 'store' => 'successstories_add', 'index' => 'successstories', 'edit' => 'successstories_edit', 'update' => 'successstories_edit', 'destroy' => 'successstories_delete']]);
        Route::post('successstories_search', ['uses' => 'Admin\SuccessstoriesController@search', 'as' => 'successstories_view']);
        Route::post('successstories/publish', ['uses' => 'Admin\SuccessstoriesController@publish', 'as' => 'successstories_publish']);

        Route::resource('articles', 'Admin\ArticlesController', ['names' => ['create' => 'articles_add', 'store' => 'articles_add', 'index' => 'articles', 'edit' => 'articles_edit', 'update' => 'articles_edit', 'destroy' => 'articles_delete']]);
        Route::post('articles_search', ['uses' => 'Admin\ArticlesController@search', 'as' => 'articles_view']);
        Route::post('articles/publish', ['uses' => 'Admin\ArticlesController@publish', 'as' => 'articles_publish']);

        Route::resource('initiative_sections', 'Admin\InitiativeSectionsController', ['names' => ['create' => 'initiative_sections_add', 'store' => 'initiative_sections_add', 'index' => 'initiative_sections', 'edit' => 'initiative_sections_edit', 'update' => 'initiative_sections_edit', 'destroy' => 'initiative_sections_delete']]);
        Route::post('initiative_sections_search', ['uses' => 'Admin\InitiativeSectionsController@search', 'as' => 'initiative_sections_view']);
        Route::post('initiative_sections/publish', ['uses' => 'Admin\InitiativeSectionsController@publish', 'as' => 'initiative_sections_publish']);

        Route::resource('initiative_videos', 'Admin\InitiativeVideosController', ['names' => ['create' => 'initiative_videos_add', 'store' => 'initiative_videos_add', 'index' => 'initiative_videos', 'edit' => 'initiative_videos_edit', 'update' => 'initiative_videos_edit', 'destroy' => 'initiative_videos_delete']]);
        Route::post('initiative_videos_search', ['uses' => 'Admin\InitiativeVideosController@search', 'as' => 'initiative_videos_view']);
        Route::post('initiative_videos/publish', ['uses' => 'Admin\InitiativeVideosController@publish', 'as' => 'initiative_videos_publish']);

        Route::resource('initiative_articles', 'Admin\InitiativeArticlesController', ['names' => ['create' => 'initiative_articles_add', 'store' => 'initiative_articles_add', 'index' => 'initiative_articles', 'edit' => 'initiative_articles_edit', 'update' => 'initiative_articles_edit', 'destroy' => 'initiative_articles_delete']]);
        Route::post('initiative_articles_search', ['uses' => 'Admin\InitiativeArticlesController@search', 'as' => 'initiative_articles_view']);
        Route::post('initiative_articles/publish', ['uses' => 'Admin\InitiativeArticlesController@publish', 'as' => 'initiative_articles_publish']);

        Route::resource('medical_categories', 'Admin\MedicalCategoriesController', ['names' => ['create' => 'medical_categories_add', 'store' => 'medical_categories_add', 'index' => 'medical_categories_view', 'edit' => 'medical_categories_edit', 'update' => 'medical_categories_edit', 'destroy' => 'medical_categories_delete']]);
        Route::post('medical_categories_search', ['uses' => 'Admin\MedicalCategoriesController@search', 'as' => 'medical_categories_view']);
        Route::post('medical_categories/publish', ['uses' => 'Admin\MedicalCategoriesController@publish', 'as' => 'medical_categories_publish']);


        Route::resource('medical_sup_categories', 'Admin\MedicalSupCategoriesController', ['names' => ['create' => 'medical_sup_categories_add', 'store' => 'medical_sup_categories_add', 'index' => 'medical_sup_categories_view', 'edit' => 'medical_sup_categories_edit', 'update' => 'medical_sup_categories_edit', 'destroy' => 'medical_sup_categories_delete']]);
        Route::post('medical_sup_categories_search', ['uses' => 'Admin\MedicalSupCategoriesController@search', 'as' => 'medical_sup_categories_view']);
        Route::post('medical_sup_categories/publish', ['uses' => 'Admin\MedicalSupCategoriesController@publish', 'as' => 'medical_sup_categories_publish']);

        Route::resource('mobile_notifications', 'Admin\MobileNotificationsController', ['names' => ['index' => 'mobile_notifications_add','create' => 'mobile_notifications_add', 'store' => 'mobile_notifications_add']]);
        Route::post('mobile_notifications/users', ['uses' => 'Admin\MobileNotificationsController@users', 'as' => 'mobile_notifications_add']);

        Route::resource('categories', 'Admin\CategoriesController', ['names' => ['create' => 'categories_add', 'store' => 'categories_add', 'index' => 'categories', 'edit' => 'categories_edit', 'update' => 'categories_edit', 'destroy' => 'categories_delete']]);
        Route::post('categories_search', ['uses' => 'Admin\CategoriesController@search', 'as' => 'categories_view']);
        Route::post('categories/activation', ['uses' => 'Admin\CategoriesController@activation', 'as' => 'categories_active']);

        Route::resource('author', 'Admin\AuthorController', ['names' => ['create' => 'author_add', 'store' => 'author_add', 'index' => 'author', 'edit' => 'author_edit', 'update' => 'author_edit', 'destroy' => 'author_delete']]);
        Route::post('author_search', ['uses' => 'Admin\AuthorController@search', 'as' => 'author_view']);
        Route::post('author/publish', ['uses' => 'Admin\AuthorController@publish', 'as' => 'author_publish']);


        Route::resource('static_pages', 'Admin\StaticPageController', ['names' => ['create' => 'static_pages_add', 'store' => 'static_pages_add', 'index' => 'static_pages', 'edit' => 'static_pages_edit', 'update' => 'static_pages_edit', 'destroy' => 'static_pages_delete']]);
        Route::post('static_pages_search', ['uses' => 'Admin\StaticPageController@search', 'as' => 'static_pages_view']);
        Route::post('static_pages/publish', ['uses' => 'Admin\StaticPageController@publish', 'as' => 'static_pages_publish']);

        Route::resource('modules_users_summary', 'Admin\ModulesUsersSummaryController', ['names' => ['index' => 'modules_users_summary', 'edit' => 'modules_users_summary_edit', 'update' => 'modules_users_summary_edit']]);
        Route::post('modules_users_summary_search', ['uses' => 'Admin\ModulesUsersSummaryController@search', 'as' => 'modules_users_summary_view']);

        Route::resource('courses_QandA', 'Admin\CoursesQuestionsAndAnswersController', ['names' => ['index' => 'courses_QandA', 'edit' => 'courses_QandA_edit', 'update' => 'courses_QandA_edit']]);
        Route::post('courses_QandA_search', ['uses' => 'Admin\CoursesQuestionsAndAnswersController@search', 'as' => 'courses_QandA_view']);

        Route::resource('recruitment_jobs', 'Admin\RecruitmentJobController', ['names' => ['create' => 'recruitment_jobs_add', 'store' => 'recruitment_jobs_add', 'index' => 'recruitment_jobs', 'edit' => 'recruitment_jobs_edit', 'update' => 'recruitment_jobs_edit', 'destroy' => 'recruitment_jobs_delete']]);
        Route::post('recruitment_jobs_search', ['uses' => 'Admin\RecruitmentJobController@search', 'as' => 'recruitment_jobs_view']);
        Route::post('recruitment_jobs/publish', ['uses' => 'Admin\RecruitmentJobController@publish', 'as' => 'recruitment_jobs_publish']);
        Route::post('recruitment_jobs/autoCompleteCountries', ['uses' => 'Admin\CountryController@autoCompleteCountries', 'as' => 'recruitment_jobs_add']);
        Route::post('recruitment_jobs/autoCompleteCities', ['uses' => 'Admin\CityController@autoCompleteCities', 'as' => 'recruitment_jobs_add']);
        Route::post('recruitment_jobs/autoCompleteStates', ['uses' => 'Admin\StateController@autoCompleteStates', 'as' => 'recruitment_jobs_add']);

        Route::resource('recruitment_industries', 'Admin\RecruitmentIndustryController', ['names' => ['create' => 'recruitment_industries_add', 'store' => 'recruitment_industries_add', 'index' => 'recruitment_industries', 'edit' => 'recruitment_industries_edit', 'update' => 'recruitment_industries_edit', 'destroy' => 'recruitment_industries_delete']]);
        Route::post('recruitment_industries_search', ['uses' => 'Admin\RecruitmentIndustryController@search', 'as' => 'recruitment_industries_view']);
        Route::post('recruitment_industries/publish', ['uses' => 'Admin\RecruitmentIndustryController@publish', 'as' => 'recruitment_industries_publish']);

        Route::resource('recruitment_job_types', 'Admin\RecruitmentJobTypesController', ['names' => ['create' => 'recruitment_job_types_add', 'store' => 'recruitment_job_types_add', 'index' => 'recruitment_job_types', 'edit' => 'recruitment_job_types_edit', 'update' => 'recruitment_job_types_edit', 'destroy' => 'recruitment_job_types_delete']]);
        Route::post('recruitment_job_types_search', ['uses' => 'Admin\RecruitmentJobTypesController@search', 'as' => 'recruitment_job_types_view']);
        Route::post('recruitment_job_types/publish', ['uses' => 'Admin\RecruitmentJobTypesController@publish', 'as' => 'recruitment_job_types_publish']);

        Route::resource('recruitment_currencies', 'Admin\RecruitmentCurrencyController', ['names' => ['create' => 'recruitment_currencies_add', 'store' => 'recruitment_currencies_add', 'index' => 'recruitment_currencies', 'edit' => 'recruitment_currencies_edit', 'update' => 'recruitment_currencies_edit', 'destroy' => 'recruitment_currencies_delete']]);
        Route::post('recruitment_currencies_search', ['uses' => 'Admin\RecruitmentCurrencyController@search', 'as' => 'recruitment_currencies_view']);
        Route::post('recruitment_currencies/publish', ['uses' => 'Admin\RecruitmentCurrencyController@publish', 'as' => 'recruitment_currencies_publish']);

        Route::resource('recruitment_jobs_types', 'Admin\RecruitmentJobsTypesController', ['names' => ['create' => 'recruitment_jobs_types_add', 'store' => 'recruitment_jobs_types_add', 'index' => 'recruitment_jobs_types', 'edit' => 'recruitment_jobs_types_edit', 'update' => 'recruitment_jobs_types_edit', 'destroy' => 'recruitment_jobs_types_delete']]);
        Route::post('recruitment_jobs_types_search', ['uses' => 'Admin\RecruitmentJobsTypesController@search', 'as' => 'recruitment_jobs_types_view']);
        Route::post('recruitment_jobs_types/publish', ['uses' => 'Admin\RecruitmentJobsTypesController@publish', 'as' => 'recruitment_jobs_types_publish']);

        Route::resource('recruitment_job_roles', 'Admin\RecruitmentJobRolesController', ['names' => ['create' => 'recruitment_job_roles_add', 'store' => 'recruitment_job_roles_add', 'index' => 'recruitment_job_roles', 'edit' => 'recruitment_job_roles_edit', 'update' => 'recruitment_job_roles_edit', 'destroy' => 'recruitment_job_roles_delete']]);
        Route::post('recruitment_job_roles_search', ['uses' => 'Admin\RecruitmentJobRolesController@search', 'as' => 'recruitment_job_roles_view']);
        Route::post('recruitment_job_roles/publish', ['uses' => 'Admin\RecruitmentJobRolesController@publish', 'as' => 'recruitment_job_roles_publish']);

        Route::resource('recruitment_jobs_roles', 'Admin\RecruitmentJobsRolesController', ['names' => ['create' => 'recruitment_jobs_roles_add', 'store' => 'recruitment_jobs_roles_add', 'index' => 'recruitment_jobs_roles', 'edit' => 'recruitment_jobs_roles_edit', 'update' => 'recruitment_jobs_roles_edit', 'destroy' => 'recruitment_jobs_roles_delete']]);
        Route::post('recruitment_jobs_roles_search', ['uses' => 'Admin\RecruitmentJobsRolesController@search', 'as' => 'recruitment_jobs_roles_view']);
        Route::post('recruitment_jobs_roles/publish', ['uses' => 'Admin\RecruitmentJobsRolesController@publish', 'as' => 'recruitment_jobs_roles_publish']);

        Route::resource('partner_requests', 'Admin\PartnerRequestController', ['names' => ['create' => 'partner_requests_add', 'store' => 'partner_requests_add', 'index' => 'partner_requests', 'edit' => 'partner_requests_edit', 'update' => 'partner_requests_edit', 'destroy' => 'partner_requests_delete']]);
        Route::post('partner_requests_search', ['uses' => 'Admin\PartnerRequestController@search', 'as' => 'partner_requests_view']);
        Route::post('partner_requests/publish', ['uses' => 'Admin\PartnerRequestController@publish', 'as' => 'partner_requests_publish']);

        Route::resource('become_instructor', 'Admin\BecomeInstructorController', ['names' => ['create' => 'become_instructor_add', 'store' => 'become_instructor_add', 'index' => 'become_instructor', 'edit' => 'become_instructor_edit', 'update' => 'become_instructor_edit', 'destroy' => 'become_instructor_delete']]);
        Route::post('become_instructor_search', ['uses' => 'Admin\BecomeInstructorController@search', 'as' => 'become_instructor_view']);
        Route::post('become_instructor/publish', ['uses' => 'Admin\BecomeInstructorController@publish', 'as' => 'become_instructor_publish']);
        Route::get('become_instructor/download_file/{id}/{type}', ['uses' => 'Admin\BecomeInstructorController@downloadFile', 'as' => 'become_instructor_view']);

        Route::resource('testimonials', 'Admin\TestimonialController', ['names' => ['create' => 'testimonials_add', 'store' => 'testimonials_add', 'index' => 'testimonials', 'edit' => 'testimonials_edit', 'update' => 'testimonials_edit', 'destroy' => 'testimonials_delete']]);
        Route::post('testimonials_search', ['uses' => 'Admin\TestimonialController@search', 'as' => 'testimonials_view']);
        Route::post('testimonials/publish', ['uses' => 'Admin\TestimonialController@publish', 'as' => 'testimonials_publish']);

        Route::resource('gallery', 'Admin\GalleryController', ['names' => ['create' => 'gallery_add', 'store' => 'gallery_add', 'index' => 'gallery', 'edit' => 'gallery_edit', 'update' => 'gallery_edit', 'destroy' => 'gallery_delete']]);
        Route::post('gallery_search', ['uses' => 'Admin\GalleryController@search', 'as' => 'gallery_view']);
        Route::post('gallery/publish', ['uses' => 'Admin\GalleryController@publish', 'as' => 'gallery_publish']);

        Route::resource('mba_charge_transaction', 'Admin\MbaChargeTransactionController', ['names' => ['create' => 'mba_charge_transaction_add', 'store' => 'mba_charge_transaction_add', 'index' => 'mba_charge_transaction', 'edit' => 'mba_charge_transaction_edit', 'update' => 'mba_charge_transaction_edit', 'destroy' => 'mba_charge_transaction_delete']]);
        Route::post('mba_charge_transaction_search', ['uses' => 'Admin\MbaChargeTransactionController@search', 'as' => 'mba_charge_transaction_view']);
        Route::post('mba_charge_transaction/publish', ['uses' => 'Admin\MbaChargeTransactionController@publish', 'as' => 'mba_charge_transaction_publish']);
        Route::get('mba_charge_transaction/copy/{id}', ['uses' => 'Admin\MbaChargeTransactionController@copy', 'as' => 'mba_charge_transaction_copy']);
        Route::post('mba_charge_transaction/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'mba_charge_transaction_add']);

        Route::resource('employee', 'Admin\EmployeeController', ['names' => ['create' => 'employee_add', 'store' => 'employee_add', 'index' => 'employee', 'edit' => 'employee_edit', 'update' => 'employee_edit', 'destroy' => 'employee_delete']]);
        Route::post('employee_search', ['uses' => 'Admin\EmployeeController@search', 'as' => 'employee_view']);
        Route::post('employee/publish', ['uses' => 'Admin\EmployeeController@publish', 'as' => 'employee_publish']);

        Route::resource('demo_medical_logs', 'Admin\DemoMedicalLogController', ['names' => ['create' => 'demo_medical_logs_add', 'store' => 'demo_medical_logs_add', 'index' => 'demo_medical_logs', 'edit' => 'demo_medical_logs_edit', 'update' => 'demo_medical_logs_edit', 'destroy' => 'demo_medical_logs_delete']]);
        Route::post('demo_medical_logs_search', ['uses' => 'Admin\DemoMedicalLogController@search', 'as' => 'demo_medical_logs_view']);
        Route::post('demo_medical_logs/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'demo_medical_logs_add']);

        Route::resource('users_suspend_liteversion', 'Admin\UsersSuspendLiteversionController', ['names' => ['create' => 'users_suspend_liteversion_add', 'store' => 'users_suspend_liteversion_add', 'index' => 'users_suspend_liteversion', 'edit' => 'users_suspend_liteversion_edit', 'update' => 'users_suspend_liteversion_edit', 'destroy' => 'users_suspend_liteversion_delete']]);
        Route::post('users_suspend_liteversion_search', ['uses' => 'Admin\UsersSuspendLiteversionController@search', 'as' => 'users_suspend_liteversion_view']);
        Route::post('users_suspend_liteversion/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'users_suspend_liteversion_add']);

        Route::resource('tags', 'Admin\TagsController', ['names' => ['create' => 'tags_add', 'store' => 'tags_add', 'index' => 'tags', 'edit' => 'tags_edit', 'update' => 'tags_edit', 'destroy' => 'tags_delete']]);
        Route::post('tags_search', ['uses' => 'Admin\TagsController@search', 'as' => 'tags_view']);

        Route::get('users_cvs', ['uses' =>'Admin\UsersCVsController@index', 'as' => 'users_cvs_view']);
        Route::post('users_cvs_search', ['uses' => 'Admin\UsersCVsController@search', 'as' => 'users_cvs_view']);
        Route::get('cv_download/{id}', ['uses' => 'Admin\UsersCVsController@cv_download', 'as' => 'users_cvs_download']);

        Route::resource('normal_user', 'Admin\NormalUserController', ['names' => ['create' => 'normal_user_add', 'store' => 'normal_user_add', 'index' => 'normal_user', 'edit' => 'normal_user_edit', 'update' => 'normal_user_edit', 'destroy' => 'normal_user_delete']]);
        Route::post('normal_user_search', ['uses' => 'Admin\NormalUserController@search', 'as' => 'normal_user_view']);
        Route::post('normal_user/publish', ['uses' => 'Admin\NormalUserController@publish', 'as' => 'normal_user_publish']);
        Route::post('normal_user/reset_password', ['uses' => 'Admin\NormalUserController@resetPassword', 'as' => 'normal_user_reset_password']);

        Route::resource('recruit_users', 'Admin\RecruitUsersController', ['names' => ['create' => 'recruit_users_add', 'store' => 'recruit_users_add', 'index' => 'recruit_users', 'edit' => 'recruit_users_edit', 'update' => 'recruit_users_edit', 'destroy' => 'recruit_users_delete']]);
        Route::post('recruit_users_search', ['uses' => 'Admin\RecruitUsersController@search', 'as' => 'recruit_users_view']);
        Route::post('recruit_users/publish', ['uses' => 'Admin\RecruitUsersController@publish', 'as' => 'recruit_users_publish']);

        Route::resource('all_categories', 'Admin\AllCategoryController', ['names' => ['create' => 'all_categories_add', 'store' => 'all_categories_add', 'index' => 'all_categories', 'edit' => 'all_categories_edit', 'update' => 'all_categories_edit', 'destroy' => 'all_categories_delete']]);
        Route::post('all_categories_search', ['uses' => 'Admin\AllCategoryController@search', 'as' => 'all_categories_view']);
        Route::post('all_categories/publish', ['uses' => 'Admin\AllCategoryController@publish', 'as' => 'all_categories_publish']);

        Route::resource('sub_categories', 'Admin\SubCategoryController', ['names' => ['create' => 'sub_categories_add', 'store' => 'sub_categories_add', 'index' => 'sub_categories', 'edit' => 'sub_categories_edit', 'update' => 'sub_categories_edit', 'destroy' => 'sub_categories_delete']]);
        Route::post('sub_categories_search', ['uses' => 'Admin\SubCategoryController@search', 'as' => 'sub_categories_view']);
        Route::post('sub_categories/publish', ['uses' => 'Admin\SubCategoryController@publish', 'as' => 'sub_categories_publish']);

        Route::resource('courses_categories', 'Admin\CourseCategoryController', ['names' => ['create' => 'courses_categories_add', 'store' => 'courses_categories_add', 'index' => 'courses_categories', 'edit' => 'courses_categories_edit', 'update' => 'courses_categories_edit', 'destroy' => 'courses_categories_delete']]);
        Route::post('courses_categories_search', ['uses' => 'Admin\CourseCategoryController@search', 'as' => 'courses_categories_view']);
        Route::post('courses_categories/publish', ['uses' => 'Admin\CourseCategoryController@publish', 'as' => 'courses_categories_publish']);
        Route::post('courses_categories/getSubCategoriesByCategoryId', ['uses' => 'Admin\AllCategoryController@getSubCategoriesByCategoryId', 'as' => 'courses_categories_add']);

        Route::resource('books_categories', 'Admin\BookCategoryController', ['names' => ['create' => 'books_categories_add', 'store' => 'books_categories_add', 'index' => 'books_categories', 'edit' => 'books_categories_edit', 'update' => 'books_categories_edit', 'destroy' => 'books_categories_delete']]);
        Route::post('books_categories_search', ['uses' => 'Admin\BookCategoryController@search', 'as' => 'books_categories_view']);
        Route::post('books_categories/publish', ['uses' => 'Admin\BookCategoryController@publish', 'as' => 'books_categories_publish']);
        Route::post('books_categories/getSubCategoriesByCategoryId', ['uses' => 'Admin\AllCategoryController@getSubCategoriesByCategoryId', 'as' => 'books_categories_add']);

        Route::resource('successtories_categories', 'Admin\SuccesStoryCategoryController', ['names' => ['create' => 'successtories_categories_add', 'store' => 'successtories_categories_add', 'index' => 'successtories_categories', 'edit' => 'successtories_categories_edit', 'update' => 'successtories_categories_edit', 'destroy' => 'successtories_categories_delete']]);
        Route::post('successtories_categories_search', ['uses' => 'Admin\SuccesStoryCategoryController@search', 'as' => 'successtories_categories_view']);
        Route::post('successtories_categories/publish', ['uses' => 'Admin\SuccesStoryCategoryController@publish', 'as' => 'successtories_categories_publish']);
        Route::post('successtories_categories/getSubCategoriesByCategoryId', ['uses' => 'Admin\AllCategoryController@getSubCategoriesByCategoryId', 'as' => 'books_categories_add']);

        Route::resource('webinars_categories', 'Admin\WebinarCategoryController', ['names' => ['create' => 'webinars_categories_add', 'store' => 'webinars_categories_add', 'index' => 'webinars_categories', 'edit' => 'webinars_categories_edit', 'update' => 'webinars_categories_edit', 'destroy' => 'webinars_categories_delete']]);
        Route::post('webinars_categories_search', ['uses' => 'Admin\WebinarCategoryController@search', 'as' => 'webinars_categories_view']);
        Route::post('webinars_categories/publish', ['uses' => 'Admin\WebinarCategoryController@publish', 'as' => 'webinars_categories_publish']);
        Route::post('webinars_categories/getSubCategoriesByCategoryId', ['uses' => 'Admin\AllCategoryController@getSubCategoriesByCategoryId', 'as' => 'webinars_categories_add']);

        Route::get('diploma_certificates/{id}/view_exams_result', ['uses' => 'Admin\DiplomaCertificateController@viewExamsResult', 'as' => 'diploma_certificates_view_exams_result']);
        Route::resource('diploma_certificates', 'Admin\DiplomaCertificateController', ['names' => ['index' => 'diploma_certificates', 'destroy' => 'diploma_certificates_delete']]);
        Route::post('diploma_certificates_search', ['uses' => 'Admin\DiplomaCertificateController@search', 'as' => 'diploma_certificates_view']);
        Route::post('diploma_certificates/publish', ['uses' => 'Admin\DiplomaCertificateController@publish', 'as' => 'diploma_certificates_publish']);
        Route::get('diploma_certificates/copy/{id}', ['uses' => 'Admin\DiplomaCertificateController@copy', 'as' => 'diploma_certificates_copy']);

        Route::resource('international_diploma_certificates', 'Admin\InternationalDiplomaCertificateController', ['names' => ['index' => 'international_diploma_certificates', 'destroy' => 'international_diploma_certificates_delete'],'parameters'
        => ['international_diploma_certificates' => 'int_diploma_certificates']]);
        Route::post('international_diploma_certificates_search', ['uses' => 'Admin\InternationalDiplomaCertificateController@search', 'as' => 'international_diploma_certificates_view','parameters' => ['international_diploma_certificates' => 'int_diploma_certificates']]);
        Route::post('international_diploma_certificates/publish', ['uses' => 'Admin\InternationalDiplomaCertificateController@publish', 'as' => 'international_diploma_certificates_publish','parameters' => ['international_diploma_certificates' => 'int_diploma_certificates']]);
        Route::get('international_diploma_certificates/copy/{id}', ['uses' => 'Admin\InternationalDiplomaCertificateController@copy', 'as' => 'international_diploma_certificates_copy','parameters' => ['international_diploma_certificates' => 'int_diploma_certificates']]);

        Route::resource('mlm_requests', 'Admin\MlmRequestsController', ['names' => ['index' => 'mlm_requests','show' => 'mlm_requests_send']]);
        Route::post('mlm_requests_search', ['uses' => 'Admin\MlmRequestsController@search', 'as' => 'mlm_requests_view']);
        Route::post('mlm_requests/process', ['uses' => 'Admin\MlmRequestsController@process', 'as' => 'mlm_requests_send']);
        Route::post('mlm_requests/send', ['uses' => 'Admin\MlmRequestsController@send', 'as' => 'mlm_requests_send']);

        Route::resource('diploma_courses', 'Admin\DiplomaCourseController', ['names' => ['create' => 'diploma_courses_add', 'store' => 'diploma_courses_add', 'index' => 'diploma_courses', 'edit' => 'diploma_courses_edit', 'update' => 'diploma_courses_edit', 'destroy' => 'diploma_courses_delete']]);
        Route::post('diploma_courses_search', ['uses' => 'Admin\DiplomaCourseController@search', 'as' => 'diploma_courses_view']);
        Route::post('diploma_courses/publish', ['uses' => 'Admin\DiplomaCourseController@publish', 'as' => 'diploma_courses_publish']);

        Route::resource('international_diploma_courses', 'Admin\InternationalDiplomaCourseController', ['names' => ['create' => 'international_diploma_courses_add', 'store' => 'international_diploma_courses_add', 'index' => 'international_diploma_courses', 'edit' => 'international_diploma_courses_edit', 'update' => 'international_diploma_courses_edit', 'destroy' => 'international_diploma_courses_delete']]);
        Route::post('international_diploma_courses_search', ['uses' => 'Admin\InternationalDiplomaCourseController@search', 'as' => 'international_diploma_courses_view']);
        Route::post('international_diploma_courses/publish', ['uses' => 'Admin\InternationalDiplomaCourseController@publish', 'as' => 'international_diploma_courses_publish']);

        Route::resource('diplomas_targets', 'Admin\DiplomasTargetsController', ['names' => ['create' => 'diplomas_targets_add', 'store' => 'diplomas_targets_add', 'index' => 'diplomas_targets', 'edit' => 'diplomas_targets_edit', 'update' => 'diplomas_targets_edit', 'destroy' => 'diplomas_targets_delete']]);
        Route::post('diplomas_targets_search', ['uses' => 'Admin\DiplomasTargetsController@search', 'as' => 'diplomas_targets_view']);
        Route::post('diplomas_targets/publish', ['uses' => 'Admin\DiplomasTargetsController@publish', 'as' => 'diplomas_targets_publish']);

        Route::resource('diploma_user_courses', 'Admin\DiplomaUserCourseController', ['names' => ['create' => 'diploma_user_courses_add', 'store' => 'diploma_user_courses_add', 'index' => 'diploma_user_courses', 'edit' => 'diploma_user_courses_edit', 'update' => 'diploma_user_courses_edit', 'destroy' => 'diploma_user_courses_delete']]);
        Route::post('diploma_user_courses_search', ['uses' => 'Admin\DiplomaUserCourseController@search', 'as' => 'diploma_user_courses_view']);
        Route::post('diploma_user_courses/publish', ['uses' => 'Admin\DiplomaUserCourseController@publish', 'as' => 'diploma_user_courses_publish']);
        Route::post('diploma_user_courses/delete_all', ['uses' => 'Admin\DiplomaUserCourseController@delete_all', 'as' => 'diploma_user_courses_delete_all']);
        Route::get('diploma_user_courses/copy/{id}', ['uses' => 'Admin\DiplomaUserCourseController@copy', 'as' => 'diploma_user_courses_copy']);
        Route::post('diploma_user_courses/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'diploma_user_courses_add']);

        Route::resource('international_diploma_user_courses', 'Admin\InternationalDiplomaUserCourseController', ['names' => ['create' => 'international_diploma_user_courses_add', 'store' => 'international_diploma_user_courses_add', 'index' => 'international_diploma_user_courses', 'edit' => 'international_diploma_user_courses_edit', 'update' => 'international_diploma_user_courses_edit', 'destroy' => 'international_diploma_user_courses_delete'],'parameters' => [
            'international_diploma_user_courses' => 'iduc'
        ]]);
        Route::post('international_diploma_user_courses_search', ['uses' => 'Admin\InternationalDiplomaUserCourseController@search', 'as' => 'international_diploma_user_courses_view']);
        Route::post('international_diploma_user_courses/publish', ['uses' => 'Admin\InternationalDiplomaUserCourseController@publish', 'as' => 'international_diploma_user_courses_publish']);
        Route::get('international_diploma_user_courses/copy/{id}', ['uses' => 'Admin\InternationalDiplomaUserCourseController@copy', 'as' => 'international_diploma_user_courses_copy']);
        Route::post('international_diploma_user_courses/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'international_diploma_user_courses_add']);

        Route::resource('events', 'Admin\EventController', ['names' => ['create' => 'events_add', 'store' => 'events_add', 'index' => 'events', 'edit' => 'events_edit', 'update' => 'events_edit', 'destroy' => 'events_delete']]);
        Route::post('events_search', ['uses' => 'Admin\EventController@search', 'as' => 'events_view']);
        Route::post('events/publish', ['uses' => 'Admin\EventController@publish', 'as' => 'events_publish']);

        Route::resource('faq', 'Admin\FaqController', ['names' => ['create' => 'faq_add', 'store' => 'faq_add', 'index' => 'faq', 'edit' => 'faq_edit', 'update' => 'faq_edit', 'destroy' => 'faq_delete']]);
        Route::post('faq_search', ['uses' => 'Admin\FaqController@search', 'as' => 'faq_view']);
        Route::post('faq/publish', ['uses' => 'Admin\FaqController@publish', 'as' => 'faq_publish']);

        Route::resource('site_faq', 'Admin\SiteFaqController', ['names' => ['create' => 'site_faq_add', 'store' => 'site_faq_add', 'index' => 'site_faq', 'edit' => 'site_faq_edit', 'update' => 'site_faq_edit', 'destroy' => 'site_faq_delete']]);
        Route::post('site_faq_search', ['uses' => 'Admin\SiteFaqController@search', 'as' => 'site_faq_view']);
        Route::post('site_faq/publish', ['uses' => 'Admin\SiteFaqController@publish', 'as' => 'site_faq_publish']);

        Route::resource('site_faq_type', 'Admin\SiteFaqTypeController', ['names' => ['create' => 'site_faq_type_add', 'store' => 'site_faq_type_add', 'index' => 'site_faq_type', 'edit' => 'site_faq_type_edit', 'update' => 'site_faq_type_edit', 'destroy' => 'site_faq_type_delete']]);
        Route::post('site_faq_type_search', ['uses' => 'Admin\SiteFaqTypeController@search', 'as' => 'site_faq_type_view']);
        Route::post('site_faq_type/publish', ['uses' => 'Admin\SiteFaqTypeController@publish', 'as' => 'site_faq_type_publish']);

        Route::resource('installment_payment', 'Admin\InstallmentPaymentController', ['names' => ['create' => 'installment_payment_add', 'store' => 'installment_payment_add', 'index' => 'installment_payment', 'edit' => 'installment_payment_edit', 'update' => 'installment_payment_edit', 'destroy' => 'installment_payment_delete']]);
        Route::post('installment_payment_search', ['uses' => 'Admin\InstallmentPaymentController@search', 'as' => 'installment_payment_view']);
        Route::post('installment_payment/publish', ['uses' => 'Admin\InstallmentPaymentController@publish', 'as' => 'installment_payment_publish']);

        Route::resource('user_contractid', 'Admin\UserContractidController', ['names' => ['create' => 'user_contractid_add', 'store' => 'user_contractid_add', 'index' => 'user_contractid', 'edit' => 'user_contractid_edit', 'update' => 'user_contractid_edit', 'destroy' => 'user_contractid_delete']]);
        Route::post('user_contractid_search', ['uses' => 'Admin\UserContractidController@search', 'as' => 'user_contractid_view']);
        Route::post('user_contractid/publish', ['uses' => 'Admin\UserContractidController@publish', 'as' => 'user_contractid_publish']);

        Route::resource('user_contractid_notifications', 'Admin\UserContractidNotificationsController', ['names' => ['create' => 'user_contractid_notifications_add', 'store' => 'user_contractid_notifications_add', 'index' => 'user_contractid_notifications', 'edit' => 'user_contractid_notifications_edit', 'update' => 'user_contractid_notifications_edit', 'destroy' => 'user_contractid_notifications_delete']]);
        Route::post('user_contractid_notifications_search', ['uses' => 'Admin\UserContractidNotificationsController@search', 'as' => 'user_contractid_notifications_view']);
        Route::post('user_contractid_notifications/publish', ['uses' => 'Admin\UserContractidNotificationsController@publish', 'as' => 'user_contractid_notifications_publish']);

        Route::resource('company', 'Admin\CompanyController', ['names' => ['create' => 'company_add', 'store' => 'company_add', 'index' => 'company', 'edit' => 'company_edit', 'update' => 'company_edit', 'destroy' => 'company_delete']]);
        Route::post('company_search', ['uses' => 'Admin\CompanyController@search', 'as' => 'company_view']);
        Route::post('company/publish', ['uses' => 'Admin\CompanyController@publish', 'as' => 'company_publish']);

        Route::resource('recruitment_employee_job_apply', 'Admin\RecruitmentEmployeeJobApplyController', ['names' => ['create' => 'recruitment_employee_job_apply_add', 'store' => 'recruitment_employee_job_apply_add', 'index' => 'recruitment_employee_job_apply', 'edit' => 'recruitment_employee_job_apply_edit', 'update' => 'recruitment_employee_job_apply_edit', 'destroy' => 'recruitment_employee_job_apply_delete']]);
        Route::post('recruitment_employee_job_apply_search', ['uses' => 'Admin\RecruitmentEmployeeJobApplyController@search', 'as' => 'recruitment_employee_job_apply_view']);
        Route::post('recruitment_employee_job_apply/publish', ['uses' => 'Admin\RecruitmentEmployeeJobApplyController@publish', 'as' => 'recruitment_employee_job_apply_publish']);

        Route::resource('recruitment_companies', 'Admin\RecruitmentCompanyController', ['names' => ['create' => 'recruitment_companies_add', 'store' => 'recruitment_companies_add', 'index' => 'recruitment_companies', 'edit' => 'recruitment_companies_edit', 'update' => 'recruitment_companies_edit', 'destroy' => 'recruitment_companies_delete']]);
        Route::post('recruitment_companies_search', ['uses' => 'Admin\RecruitmentCompanyController@search', 'as' => 'recruitment_companies_view']);
        Route::post('recruitment_companies/publish', ['uses' => 'Admin\RecruitmentCompanyController@publish', 'as' => 'recruitment_companies_publish']);
        Route::post('recruitment_companies/autoCompleteCountries', ['uses' => 'Admin\CountryController@autoCompleteCountries', 'as' => 'recruitment_companies_add']);
        Route::post('recruitment_companies/autoCompleteCities', ['uses' => 'Admin\CityController@autoCompleteCities', 'as' => 'recruitment_companies_add']);
        Route::post('recruitment_companies/autoCompleteStates', ['uses' => 'Admin\StateController@autoCompleteStates', 'as' => 'recruitment_companies_add']);

        Route::resource('courses_offers', 'Admin\CoursesOffersController', ['names' => ['create' => 'courses_offers_add', 'store' => 'courses_offers_add', 'index' => 'courses_offers', 'edit' => 'courses_offers_edit', 'update' => 'courses_offers_edit', 'destroy' => 'courses_offers_delete']]);
        Route::post('courses_offers_search', ['uses' => 'Admin\CoursesOffersController@search', 'as' => 'courses_offers_view']);
        Route::post('courses_offers/publish', ['uses' => 'Admin\CoursesOffersController@publish', 'as' => 'courses_offers_publish']);

        Route::resource('courses_sections', 'Admin\CoursesSectionsController', ['names' => ['create' => 'courses_sections_add', 'store' => 'courses_sections_add', 'index' => 'courses_sections', 'edit' => 'courses_sections_edit', 'update' => 'courses_sections_edit', 'destroy' => 'courses_sections_delete']]);
        Route::post('courses_sections_search', ['uses' => 'Admin\CoursesSectionsController@search', 'as' => 'courses_sections_view']);
        Route::post('courses_sections/publish', ['uses' => 'Admin\CoursesSectionsController@publish', 'as' => 'courses_sections_publish']);

        Route::resource('course_curriculum', 'Admin\CourseCurriculumController', ['names' => ['create' => 'course_curriculum_add', 'store' => 'course_curriculum_add', 'index' => 'course_curriculum', 'edit' => 'course_curriculum_edit', 'update' => 'course_curriculum_edit', 'destroy' => 'course_curriculum_delete']]);
        Route::post('course_curriculum_search', ['uses' => 'Admin\CourseCurriculumController@search', 'as' => 'course_curriculum_view']);
        Route::post('course_curriculum/publish', ['uses' => 'Admin\CourseCurriculumController@publish', 'as' => 'course_curriculum_publish']);
        Route::post('course_curriculum/getSectionsByCourseId', ['uses' => 'Admin\CourseCurriculumController@getSectionsByCourseId', 'as' => 'course_curriculum_add']);


        Route::resource('request_courses', 'Admin\RequestCourseController', ['names' => ['index' => 'request_courses']]);
        Route::post('request_courses_search', ['uses' => 'Admin\RequestCourseController@search', 'as' => 'request_courses_view']);

        Route::resource('books_requests', 'Admin\BooksRequestsController', ['names' => ['index' => 'books_requests']]);
        Route::post('books_requests_search', ['uses' => 'Admin\BooksRequestsController@search', 'as' => 'books_requests_view']);

        Route::resource('company_request', 'Admin\CompanyRequestController', ['names' => ['index' => 'company_request','create' => 'company_request_add', 'store' => 'company_request_add', 'edit' => 'company_request_edit', 'update' => 'company_request_edit', 'destroy' => 'company_request_delete']]);
        Route::post('company_request_search', ['uses' => 'Admin\CompanyRequestController@search', 'as' => 'company_request_view']);
        Route::post('company_request/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'company_request_add']);

        Route::resource('session_courses_views', 'Admin\SessionCoursesViewsController', ['names' => ['create' => 'session_courses_views_add', 'store' => 'session_courses_views_add', 'index' => 'session_courses_views', 'edit' => 'session_courses_views_edit', 'update' => 'session_courses_views_edit', 'destroy' => 'session_courses_views_delete']]);
        Route::post('session_courses_views_search', ['uses' => 'Admin\SessionCoursesViewsController@search', 'as' => 'session_courses_views_view']);
        Route::post('session_courses_views/publish', ['uses' => 'Admin\SessionCoursesViewsController@publish', 'as' => 'session_courses_views_publish']);
        Route::post('session_courses_views/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'session_courses_views_add']);

        Route::resource('session_webinars_views', 'Admin\SessionWebinarsViewsController', ['names' => ['create' => 'session_webinars_views_add', 'store' => 'session_webinars_views_add', 'index' => 'session_webinars_views', 'edit' => 'session_webinars_views_edit', 'update' => 'session_webinars_views_edit', 'destroy' => 'session_webinars_views_delete']]);
        Route::post('session_webinars_views_search', ['uses' => 'Admin\SessionWebinarsViewsController@search', 'as' => 'session_webinars_views_view']);
        Route::post('session_webinars_views/publish', ['uses' => 'Admin\SessionWebinarsViewsController@publish', 'as' => 'session_webinars_views_publish']);
        Route::post('session_webinars_views/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'session_webinars_views_add']);

        Route::resource('companies_admins', 'Admin\CompaniesAdminsController', ['names' => ['create' => 'companies_admins_add', 'store' => 'companies_admins_add', 'index' => 'companies_admins', 'edit' => 'companies_admins_edit', 'update' => 'companies_admins_edit', 'destroy' => 'companies_admins_delete']]);
        Route::post('companies_admins_search', ['uses' => 'Admin\CompaniesAdminsController@search', 'as' => 'companies_admins_view']);
        Route::post('companies_admins/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'companies_admins_add']);

        Route::resource('apple_users', 'Admin\AppleUsersController', ['names' => ['create' => 'apple_users_add', 'store' => 'apple_users_add', 'index' => 'apple_users', 'edit' => 'apple_users_edit', 'update' => 'apple_users_edit', 'destroy' => 'apple_users_delete']]);
        Route::post('apple_users_search', ['uses' => 'Admin\AppleUsersController@search', 'as' => 'apple_users_view']);
        Route::post('apple_users/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'apple_users_add']);

        Route::resource('apple_products', 'Admin\AppleProductsController', ['names' => ['create' => 'apple_products_add', 'store' => 'apple_products_add', 'index' => 'apple_products', 'edit' => 'apple_products_edit', 'update' => 'apple_products_edit', 'destroy' => 'apple_products_delete']]);
        Route::post('apple_products_search', ['uses' => 'Admin\AppleProductsController@search', 'as' => 'apple_products_view']);

        Route::resource('apple_users_charge_transactions', 'Admin\AppleUsersChargeTransactionsController', ['names' => ['create' => 'apple_users_charge_transactions_add', 'store' => 'apple_users_charge_transactions_add', 'index' => 'apple_users_charge_transactions', 'edit' => 'apple_users_charge_transactions_edit', 'update' => 'apple_users_charge_transactions_edit', 'destroy' => 'apple_users_charge_transactions_delete']]);
        Route::post('apple_users_charge_transactions_search', ['uses' => 'Admin\AppleUsersChargeTransactionsController@search', 'as' => 'apple_users_charge_transactions_view']);
        Route::post('apple_users_charge_transactions/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'companies_admins_add']);

        Route::resource('session_diplomas_views', 'Admin\SessionDiplomasViewsController', ['names' => ['index' => 'session_diplomas_views']]);
        Route::post('session_diplomas_views_search', ['uses' => 'Admin\SessionDiplomasViewsController@search', 'as' => 'session_diplomas_views_view']);

        Route::resource('users_sessions', 'Admin\UsersSessionsController', ['names' => ['index' => 'users_sessions']]);
        Route::post('users_sessions_search', ['uses' => 'Admin\UsersSessionsController@search', 'as' => 'users_sessions_view']);

        Route::resource('abuse', 'Admin\AbuseController', ['names' => ['index' => 'abuse']]);
        Route::post('abuse_search', ['uses' => 'Admin\AbuseController@search', 'as' => 'abuse_view']);

        Route::resource('session_successtories_views', 'Admin\SessionSuccesstoriesViewsController', ['names' => ['create' => 'session_successtories_views_add', 'store' => 'session_successtories_views_add', 'index' => 'session_successtories_views', 'edit' => 'session_successtories_views_edit', 'update' => 'session_successtories_views_edit', 'destroy' => 'session_successtories_views_delete']]);
        Route::post('session_successtories_views_search', ['uses' => 'Admin\SessionSuccesstoriesViewsController@search', 'as' => 'session_successtories_views_view']);
        Route::post('session_successtories_views/publish', ['uses' => 'Admin\SessionSuccesstoriesViewsController@publish', 'as' => 'session_successtories_views_publish']);
        Route::post('session_successtories_views/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'session_successtories_views_add']);

        Route::resource('courses_views', 'Admin\CoursesViewsController', ['names' => ['create' => 'courses_views_add', 'store' => 'courses_views_add', 'index' => 'courses_views', 'edit' => 'courses_views_edit', 'update' => 'courses_views_edit', 'destroy' => 'courses_views_delete']]);
        Route::post('courses_views_search', ['uses' => 'Admin\CoursesViewsController@search', 'as' => 'courses_views_view']);
        Route::post('courses_views/publish', ['uses' => 'Admin\CoursesViewsController@publish', 'as' => 'courses_views_publish']);
        Route::post('courses_views/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'courses_views_add']);

        Route::resource('international_diplomas_views', 'Admin\InternationalDiplomasViewsController', ['names' => ['create' => 'international_diplomas_views_add', 'store' => 'international_diplomas_views_add', 'index' => 'international_diplomas_views', 'edit' => 'international_diplomas_views_edit', 'update' => 'international_diplomas_views_edit', 'destroy' => 'international_diplomas_views_delete']]);
        Route::post('international_diplomas_views_search', ['uses' => 'Admin\InternationalDiplomasViewsController@search', 'as' => 'international_diplomas_views_view']);
        Route::post('international_diplomas_views/publish', ['uses' => 'Admin\InternationalDiplomasViewsController@publish', 'as' => 'international_diplomas_views_publish']);
        Route::post('international_diplomas_views/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'international_diplomas_views_add']);

        Route::resource('my_courses', 'Admin\MyCoursesController', ['names' => ['create' => 'my_courses_add', 'store' => 'my_courses_add', 'index' => 'my_courses', 'edit' => 'my_courses_edit', 'update' => 'my_courses_edit', 'destroy' => 'my_courses_delete']]);
        Route::post('my_courses_search', ['uses' => 'Admin\MyCoursesController@search', 'as' => 'my_courses_view']);
        Route::post('my_courses/publish', ['uses' => 'Admin\MyCoursesController@publish', 'as' => 'my_courses_publish']);
        Route::post('my_courses/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'my_courses_add']);
        Route::get('my_courses/copy/{id}', ['uses' => 'Admin\MyCoursesController@copy', 'as' => 'my_courses_copy']);

        Route::resource('automation', 'Admin\AutomationController', ['names' => ['create' => 'automation_add', 'store' => 'automation_add', 'index' => 'automation', 'edit' => 'automation_edit', 'update' => 'automation_edit', 'destroy' => 'automation_delete']]);
        Route::post('automation_search', ['uses' => 'Admin\AutomationController@search', 'as' => 'automation_view']);
        Route::post('automation/publish', ['uses' => 'Admin\AutomationController@publish', 'as' => 'automation_publish']);
        Route::post('automation/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'automation_add']);

        Route::resource('dctsl', 'Admin\DiplomasChargeTransactionSuspendLogController', ['names' => ['create' => 'diplomas_charge_transaction_suspend_log_add', 'store' => 'diplomas_charge_transaction_suspend_log_add', 'index' => 'diplomas_charge_transaction_suspend_log', 'edit' => 'diplomas_charge_transaction_suspend_log_edit', 'update' => 'diplomas_charge_transaction_suspend_log_edit', 'destroy' => 'diplomas_charge_transaction_suspend_log_delete']]);
        Route::post('dctsl_search', ['uses' => 'Admin\DiplomasChargeTransactionSuspendLogController@search', 'as' => 'diplomas_charge_transaction_suspend_log_view']);
        Route::post('dctsl/publish', ['uses' => 'Admin\DiplomasChargeTransactionSuspendLogController@publish', 'as' => 'diplomas_charge_transaction_suspend_log_publish']);
        Route::post('dctsl/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'diplomas_charge_transaction_suspend_log_add']);

        Route::resource('idctsl', 'Admin\InternationalDiplomasChargeTransactionSuspendLogController', ['names' => ['create' => 'international_diplomas_charge_transaction_suspend_log_add', 'store' => 'international_diplomas_charge_transaction_suspend_log_add', 'index' => 'international_diplomas_charge_transaction_suspend_log', 'edit' => 'international_diplomas_charge_transaction_suspend_log_edit', 'update' => 'international_diplomas_charge_transaction_suspend_log_edit', 'destroy' => 'international_diplomas_charge_transaction_suspend_log_delete']]);
        Route::post('idctsl_search', ['uses' => 'Admin\InternationalDiplomasChargeTransactionSuspendLogController@search', 'as' => 'international_diplomas_charge_transaction_suspend_log_view']);
        Route::post('idctsl/publish', ['uses' => 'Admin\InternationalDiplomasChargeTransactionSuspendLogController@publish', 'as' => 'international_diplomas_charge_transaction_suspend_log_publish']);
        Route::post('idctsl/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'international_diplomas_charge_transaction_suspend_log_add']);

        Route::resource('mctsl', 'Admin\MedicalChargeTransactionSuspendLogController', ['names' => ['create' => 'medical_charge_transaction_suspend_log_add', 'store' => 'medical_charge_transaction_suspend_log_add', 'index' => 'medical_charge_transaction_suspend_log', 'edit' => 'medical_charge_transaction_suspend_log_edit', 'update' => 'medical_charge_transaction_suspend_log_edit', 'destroy' => 'medical_charge_transaction_suspend_log_delete']]);
        Route::post('mctsl_search', ['uses' => 'Admin\MedicalChargeTransactionSuspendLogController@search', 'as' => 'medical_charge_transaction_suspend_log_view']);
        Route::post('mctsl/publish', ['uses' => 'Admin\MedicalChargeTransactionSuspendLogController@publish', 'as' => 'medical_charge_transaction_suspend_log_publish']);
        Route::post('mctsl/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'medical_charge_transaction_suspend_log_add']);

        Route::resource('courses_curriculum_certificates', 'Admin\CoursesCurriculumCertificatesController', ['names' => ['create' => 'courses_curriculum_certificates_add', 'store' => 'courses_curriculum_certificates_add', 'index' => 'courses_curriculum_certificates', 'edit' => 'courses_curriculum_certificates_edit', 'update' => 'courses_curriculum_certificates_edit', 'destroy' => 'courses_curriculum_certificates_delete']]);
        Route::post('courses_curriculum_certificates_search', ['uses' => 'Admin\CoursesCurriculumCertificatesController@search', 'as' => 'courses_curriculum_certificates_view']);
        Route::post('courses_curriculum_certificates/publish', ['uses' => 'Admin\CoursesCurriculumCertificatesController@publish', 'as' => 'courses_curriculum_certificates_publish']);
        Route::get('courses_curriculum_certificates/copy/{id}', ['uses' => 'Admin\CoursesCurriculumCertificatesController@copy', 'as' => 'courses_curriculum_certificates_copy']);
        Route::post('courses_curriculum_certificates/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'courses_curriculum_certificates_add']);
        Route::post('courses_curriculum_certificates/autoCompleteAnswersIds', ['uses' => 'Admin\CoursesCurriculumCertificatesController@autoCompleteAnswersIds', 'as' => 'courses_curriculum_certificates_add']);

        Route::resource('mba_certificates', 'Admin\MbaCertificatesController', ['names' => ['create' => 'mba_certificates_add', 'store' => 'mba_certificates_add', 'index' => 'mba_certificates', 'edit' => 'mba_certificates_edit', 'update' => 'mba_certificates_edit', 'destroy' => 'mba_certificates_delete']]);
        Route::post('mba_certificates_search', ['uses' => 'Admin\MbaCertificatesController@search', 'as' => 'mba_certificates_view']);
        Route::post('mba_certificates/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'mba_certificates_add']);

        Route::resource('users_curriculum_answers', 'Admin\UsersCurriculumAnswersController', ['names' => ['create' => 'users_curriculum_answers_add', 'store' => 'users_curriculum_answers_add', 'index' => 'users_curriculum_answers', 'edit' => 'users_curriculum_answers_edit', 'update' => 'users_curriculum_answers_edit', 'destroy' => 'users_curriculum_answers_delete']]);
        Route::post('users_curriculum_answers_search', ['uses' => 'Admin\UsersCurriculumAnswersController@search', 'as' => 'users_curriculum_answers_view']);
        Route::post('users_curriculum_answers/publish', ['uses' => 'Admin\UsersCurriculumAnswersController@publish', 'as' => 'users_curriculum_answers_publish']);
        Route::post('users_curriculum_answers/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'users_curriculum_answers_add']);

        Route::resource('recruitment_employees', 'Admin\RecruitmentEmployeesController', ['names' => ['create' => 'recruitment_employees_add', 'store' => 'recruitment_employees_add', 'index' => 'recruitment_employees', 'edit' => 'recruitment_employees_edit', 'update' => 'recruitment_employees_edit', 'destroy' => 'recruitment_employees_delete']]);
        Route::post('recruitment_employees_search', ['uses' => 'Admin\RecruitmentEmployeesController@search', 'as' => 'recruitment_employees_view']);
        Route::post('recruitment_employees/publish', ['uses' => 'Admin\RecruitmentEmployeesController@publish', 'as' => 'recruitment_employees_publish']);
        Route::post('recruitment_employees/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'recruitment_employees_add']);
        Route::post('recruitment_employees/autoCompleteCountries', ['uses' => 'Admin\CountryController@autoCompleteCountries', 'as' => 'recruitment_employees_add']);
        Route::post('recruitment_employees/autoCompleteCities', ['uses' => 'Admin\CityController@autoCompleteCities', 'as' => 'recruitment_employees_add']);
        Route::post('recruitment_employees/autoCompleteStates', ['uses' => 'Admin\StateController@autoCompleteStates', 'as' => 'recruitment_employees_add']);

        Route::resource('recruit', 'Admin\RecruitController', ['names' => ['create' => 'recruit_add', 'store' => 'recruit_add', 'index' => 'recruit', 'edit' => 'recruit_edit', 'update' => 'recruit_edit', 'destroy' => 'recruit_delete']]);
        Route::post('recruit_search', ['uses' => 'Admin\RecruitController@search', 'as' => 'recruit_view']);
        Route::post('recruit/publish', ['uses' => 'Admin\RecruitController@publish', 'as' => 'recruit_publish']);
        Route::post('recruit/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'recruit_add']);
        Route::post('recruit/autoCompleteCountries', ['uses' => 'Admin\CountryController@autoCompleteCountries', 'as' => 'recruit_add']);
        Route::post('recruit/autoCompleteCities', ['uses' => 'Admin\CityController@autoCompleteCities', 'as' => 'recruit_add']);
        Route::post('recruit/autoCompleteStates', ['uses' => 'Admin\StateController@autoCompleteStates', 'as' => 'recruit_add']);


        Route::resource('payment_transaction', 'Admin\PaymentTransactionController', ['names' => ['create' => 'payment_transaction_add', 'store' => 'payment_transaction_add', 'index' => 'payment_transaction', 'edit' => 'payment_transaction_edit', 'update' => 'payment_transaction_edit', 'destroy' => 'payment_transaction_delete']]);
        Route::post('payment_transaction_search', ['uses' => 'Admin\PaymentTransactionController@search', 'as' => 'payment_transaction_view']);
        Route::post('payment_transaction/publish', ['uses' => 'Admin\PaymentTransactionController@publish', 'as' => 'payment_transaction_publish']);

        Route::resource('support_transaction', 'Admin\SupportTransactionController', ['names' => ['create' => 'support_transaction_add', 'store' => 'support_transaction_add', 'index' => 'support_transaction', 'edit' => 'support_transaction_edit', 'update' => 'support_transaction_edit', 'destroy' => 'support_transaction_delete']]);
        Route::post('support_transaction_search', ['uses' => 'Admin\SupportTransactionController@search', 'as' => 'support_transaction_view']);
        //Route::post('support_transaction/publish', ['uses' => 'Admin\SupportTransactionController@publish', 'as' => 'support_transaction_publish']);
        Route::post('support_transaction/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'support_transaction_add']);

        Route::resource('dct', 'Admin\DiplomasChargeTransactionController', ['names' => ['create' => 'diplomas_charge_transaction_add', 'store' => 'diplomas_charge_transaction_add', 'index' => 'diplomas_charge_transaction', 'edit' => 'diplomas_charge_transaction_edit', 'update' => 'diplomas_charge_transaction_edit', 'destroy' => 'diplomas_charge_transaction_delete']]);
        Route::post('dct_search', ['uses' => 'Admin\DiplomasChargeTransactionController@search', 'as' => 'diplomas_charge_transaction_view']);
        Route::post('dct/publish', ['uses' => 'Admin\DiplomasChargeTransactionController@publish', 'as' => 'diplomas_charge_transaction_publish']);
        Route::post('dct/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'diplomas_charge_transaction_add']);
        Route::get('dct/copy/{id}', ['uses' => 'Admin\DiplomasChargeTransactionController@copy', 'as' => 'diplomas_charge_transaction_copy']);
        Route::post('dct/add_courses/{id}', ['uses' => 'Admin\DiplomasChargeTransactionController@addCourses', 'as' => 'diplomas_charge_transaction_add_courses']);

        Route::resource('idct', 'Admin\InternationalDiplomasChargeTransactionController', ['names' => ['create' => 'international_diplomas_charge_transaction_add', 'store' => 'international_diplomas_charge_transaction_add', 'index' => 'international_diplomas_charge_transaction', 'edit' => 'international_diplomas_charge_transaction_edit', 'update' => 'international_diplomas_charge_transaction_edit', 'destroy' => 'international_diplomas_charge_transaction_delete']]);
        Route::post('idct_search', ['uses' => 'Admin\InternationalDiplomasChargeTransactionController@search', 'as' => 'international_diplomas_charge_transaction_view']);
        Route::post('idct/publish', ['uses' => 'Admin\InternationalDiplomasChargeTransactionController@publish', 'as' => 'international_diplomas_charge_transaction_publish']);
        Route::post('idct/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'international_diplomas_charge_transaction_add']);
        Route::get('idct/copy/{id}', ['uses' => 'Admin\InternationalDiplomasChargeTransactionController@copy', 'as' => 'international_diplomas_charge_transaction_copy']);
//        Route::post('dct/add_courses/{id}', ['uses' => 'Admin\DiplomasChargeTransactionController@addCourses', 'as' => 'diplomas_charge_transaction_add_courses']);

        Route::resource('dcct', 'Admin\DiplomasCompaniesChargeTransactionController', ['names' => ['create' => 'diplomas_companies_charge_transaction_add', 'store' => 'diplomas_companies_charge_transaction_add', 'index' => 'diplomas_companies_charge_transaction', 'edit' => 'diplomas_companies_charge_transaction_edit', 'update' => 'diplomas_companies_charge_transaction_edit', 'destroy' => 'diplomas_companies_charge_transaction_delete']]);
        Route::post('dcct_search', ['uses' => 'Admin\DiplomasCompaniesChargeTransactionController@search', 'as' => 'diplomas_companies_charge_transaction_view']);
//        Route::post('dct/publish', ['uses' => 'Admin\DiplomasCompaniesChargeTransactionController@publish', 'as' => 'diplomas_companies_charge_transaction_publish']);
        Route::post('dcct/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'diplomas_companies_charge_transaction_add']);
        Route::get('dcct/copy/{id}', ['uses' => 'Admin\DiplomasCompaniesChargeTransactionController@copy', 'as' => 'diplomas_companies_charge_transaction_copy']);
        Route::post('dcct/add_courses/{id}', ['uses' => 'Admin\DiplomasCompaniesChargeTransactionController@addCourses', 'as' => 'diplomas_companies_charge_transaction_add_courses']);

        Route::resource('cct', 'Admin\CompaniesChargeTransactionController', ['names' => [ 'index' => 'companies_charge_transaction', 'destroy' => 'companies_charge_transaction_delete']]);
        Route::post('cct_search', ['uses' => 'Admin\CompaniesChargeTransactionController@search', 'as' => 'companies_charge_transaction_view']);

        Route::resource('lcct', 'Admin\LiteversionCompaniesChargeTransactionController', ['names' => [ 'index' => 'lite_version_companies_charge_transaction', 'destroy' => 'lite_version_companies_charge_transaction_delete']]);
        Route::post('lcct_search', ['uses' => 'Admin\LiteversionCompaniesChargeTransactionController@search', 'as' => 'lite_version_companies_charge_transaction_view']);

        Route::resource('mcct', 'Admin\MbaCompaniesChargeTransactionController', ['names' => [ 'index' => 'mba_companies_charge_transaction', 'destroy' => 'mba_companies_charge_transaction_delete']]);
        Route::post('mcct_search', ['uses' => 'Admin\MbaCompaniesChargeTransactionController@search', 'as' => 'mba_companies_charge_transaction_view']);

        Route::resource('charge_transaction', 'Admin\ChargeTransactionController', ['names' => ['create' => 'charge_transaction_add', 'store' => 'charge_transaction_add', 'index' => 'charge_transaction', 'edit' => 'charge_transaction_edit', 'update' => 'charge_transaction_edit', 'destroy' => 'charge_transaction_delete']]);
        Route::post('charge_transaction_search', ['uses' => 'Admin\ChargeTransactionController@search', 'as' => 'charge_transaction_view']);
        Route::post('charge_transaction/publish', ['uses' => 'Admin\ChargeTransactionController@publish', 'as' => 'charge_transaction_publish']);
        Route::get('charge_transaction/copy/{id}', ['uses' => 'Admin\ChargeTransactionController@copy', 'as' => 'charge_transaction_copy']);
        Route::post('charge_transaction/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'charge_transaction_add']);

        Route::resource('contactus', 'Admin\ContactusController', ['names' => ['index' => 'contactus']]);
        Route::post('contactus_search', ['uses' => 'Admin\ContactusController@search', 'as' => 'contactus_view']);

        Route::resource('webinars_views', 'Admin\WebinarsViewsController', ['names' => ['create' => 'webinars_views_add', 'store' => 'webinars_views_add', 'index' => 'webinars_views', 'edit' => 'webinars_views_edit', 'update' => 'webinars_views_edit', 'destroy' => 'webinars_views_delete']]);
        Route::post('webinars_views_search', ['uses' => 'Admin\WebinarsViewsController@search', 'as' => 'webinars_views_view']);
        Route::post('webinars_views/publish', ['uses' => 'Admin\WebinarsViewsController@publish', 'as' => 'webinars_views_publish']);
        Route::post('webinars_views/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'webinars_views_add']);

        Route::resource('successtories_views', 'Admin\SuccesstoriesViewsController', ['names' => ['create' => 'successtories_views_add', 'store' => 'successtories_views_add', 'index' => 'successtories_views', 'edit' => 'successtories_views_edit', 'update' => 'successtories_views_edit', 'destroy' => 'successtories_views_delete']]);
        Route::post('successtories_views_search', ['uses' => 'Admin\SuccesstoriesViewsController@search', 'as' => 'successtories_views_view']);
        Route::post('successtories_views/publish', ['uses' => 'Admin\SuccesstoriesViewsController@publish', 'as' => 'successtories_views_publish']);
        Route::post('successtories_views/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'successtories_views_add']);

        //our_partner
        Route::resource('our_partner', 'Admin\OurPartnerController', ['names' => ['create' => 'our_partner_add', 'store' => 'our_partner_add', 'index' => 'our_partner', 'edit' => 'our_partner_edit', 'update' => 'our_partner_edit', 'destroy' => 'our_partner_delete']]);
        Route::post('our_partner_search', ['uses' => 'Admin\OurPartnerController@search', 'as' => 'our_partner_view']);
        Route::post('our_partner/publish', ['uses' => 'Admin\OurPartnerController@publish', 'as' => 'our_partner_publish']);

        Route::resource('our_partner_flags', 'Admin\OurPartnerFlagsController', ['names' => ['create' => 'our_partner_flags_add', 'store' => 'our_partner_flags_add', 'index' => 'our_partner_flags', 'edit' => 'our_partner_flags_edit', 'update' => 'our_partner_flags_edit', 'destroy' => 'our_partner_flags_delete']]);
        Route::post('our_partner_flags_search', ['uses' => 'Admin\OurPartnerFlagsController@search', 'as' => 'our_partner_flags_view']);
        Route::post('our_partner_flags/publish', ['uses' => 'Admin\OurPartnerFlagsController@publish', 'as' => 'our_partner_flags_publish']);


        Route::resource('tpay_price_book', 'Admin\TpayPriceBookController', ['names' => ['create' => 'tpay_price_book_add', 'store' => 'tpay_price_book_add', 'index' => 'tpay_price_book', 'edit' => 'tpay_price_book_edit', 'update' => 'tpay_price_book_edit', 'destroy' => 'tpay_price_book_delete']]);
        Route::post('tpay_price_book_search', ['uses' => 'Admin\TpayPriceBookController@search', 'as' => 'tpay_price_book_view']);
        Route::post('tpay_price_book/publish', ['uses' => 'Admin\TpayPriceBookController@publish', 'as' => 'tpay_price_book_publish']);


        Route::resource('survey', 'Admin\Survey1Controller', ['names' => ['index' => 'survey']]);
        Route::post('survey_search', ['uses' => 'Admin\Survey1Controller@search', 'as' => 'survey_view']);
        Route::resource('survey_clients', 'Admin\Survey1ClientsController', ['names' => ['index' => 'survey_clients']]);
        Route::post('survey_clients_search', ['uses' => 'Admin\Survey1ClientsController@search', 'as' => 'survey_clients_view']);

        Route::resource('feedback', 'Admin\FeedbackPopupController', ['names' => ['index' => 'feedback', 'edit' => 'feedback_edit', 'update' => 'feedback_edit']]);
        Route::post('feedback_search', ['uses' => 'Admin\FeedbackPopupController@search', 'as' => 'feedback_view']);

        Route::resource('rating', 'Admin\RatingPopupController', ['names' => ['index' => 'rating']]);
        Route::post('rating_search', ['uses' => 'Admin\RatingPopupController@search', 'as' => 'rating_view']);

        /*START medical_charge_transactions*/
        Route::resource('medical_charge_transactions', 'Admin\MedicalChargeTransactionsController', ['names' => ['create' => 'medical_charge_transactions_add', 'store' => 'medical_charge_transactions_add', 'index' => 'medical_charge_transactions', 'edit' => 'medical_charge_transactions_edit', 'update' => 'medical_charge_transactions_edit', 'destroy' => 'medical_charge_transactions_delete']]);
        Route::post('medical_charge_transactions_search', ['uses' => 'Admin\MedicalChargeTransactionsController@search', 'as' => 'medical_charge_transactions_view']);
        Route::post('medical_charge_transactions/activation', ['uses' => 'Admin\MedicalChargeTransactionsController@activation', 'as' => 'medical_charge_transactions_active']);
        Route::post('medical_charge_transactions/copy', ['uses' => 'Admin\MedicalChargeTransactionsController@copy', 'as' => 'medical_charge_transactions_copy']);

        /*END medical_charge_transactions*/

        Route::resource('medical_categories_charge_transactions', 'Admin\MedicalCategoriesChargeTransactionsController', ['names' => ['create' => 'medical_categories_charge_transactions_add', 'store' => 'medical_categories_charge_transactions_add', 'index' => 'medical_categories_charge_transactions', 'edit' => 'medical_categories_charge_transactions_edit', 'update' => 'medical_categories_charge_transactions_edit', 'destroy' => 'medical_categories_charge_transactions_delete'],'parameters' => [
            'medical_categories_charge_transactions' => 'transaction'
        ]]);
        Route::post('medical_categories_charge_transactions_search', ['uses' => 'Admin\MedicalCategoriesChargeTransactionsController@search', 'as' => 'medical_categories_charge_transactions_view']);
        Route::post('medical_categories_charge_transactions/activation', ['uses' => 'Admin\MedicalCategoriesChargeTransactionsController@activation', 'as' => 'medical_categories_charge_transactions_active']);
        Route::post('medical_categories_charge_transactions/copy', ['uses' => 'Admin\MedicalCategoriesChargeTransactionsController@copy', 'as' => 'medical_categories_charge_transactions_copy']);

        Route::resource('international_categories', 'Admin\InternationalCategoriesController', ['names' => ['create' => 'international_categories_add', 'store' => 'international_categories_add', 'index' => 'international_categories', 'edit' => 'international_categories_edit', 'update' => 'international_categories_edit', 'destroy' => 'international_categories_delete']]);
        Route::post('international_categories_search', ['uses' => 'Admin\InternationalCategoriesController@search', 'as' => 'international_categories_view']);
        Route::post('international_categories/publish', ['uses' => 'Admin\InternationalCategoriesController@publish', 'as' => 'international_categories_publish']);

        /*START lite_version_charge_transaction*/
        Route::resource('lite_version_charge_transaction', 'Admin\LiteVersionChargeTransactionController', ['names' => ['create' => 'lite_version_charge_transaction_add', 'store' => 'lite_version_charge_transaction_add', 'index' => 'lite_version_charge_transaction', 'edit' => 'lite_version_charge_transaction_edit', 'update' => 'lite_version_charge_transaction_edit', 'destroy' => 'lite_version_charge_transaction_delete']]);
        Route::post('lite_version_charge_transaction_search', ['uses' => 'Admin\LiteVersionChargeTransactionController@search', 'as' => 'lite_version_charge_transaction_view']);
        Route::post('lite_version_charge_transaction/activation', ['uses' => 'Admin\LiteVersionChargeTransactionController@activation', 'as' => 'lite_version_charge_transaction_active']);
        /*END lite_version_charge_transaction*/


        /*START diplomas_courses_user_plan*/
        Route::resource('diplomas_courses_user_plan', 'Admin\DiplomasCoursesUserPlanController', ['names' => ['create' => 'diplomas_courses_user_plan_add', 'store' => 'diplomas_courses_user_plan_add', 'index' => 'diplomas_courses_user_plan', 'edit' => 'diplomas_courses_user_plan_edit', 'update' => 'diplomas_courses_user_plan_edit', 'destroy' => 'diplomas_courses_user_plan_delete']]);
        Route::post('diplomas_courses_user_plan_search', ['uses' => 'Admin\DiplomasCoursesUserPlanController@search', 'as' => 'diplomas_courses_user_plan_view']);
        Route::post('diplomas_courses_user_plan/activation', ['uses' => 'Admin\DiplomasCoursesUserPlanController@activation', 'as' => 'diplomas_courses_user_plan_active']);
        Route::post('diplomas_courses_user_plan/delete_all', ['uses' => 'Admin\DiplomasCoursesUserPlanController@delete_all', 'as' => 'diplomas_courses_user_plan_delete_all']);
        Route::post('diplomas_courses_user_plan/delete_selected', ['uses' => 'Admin\DiplomasCoursesUserPlanController@delete_selected', 'as' => 'diplomas_courses_user_plan_delete_selected']);
        /*END diplomas_courses_user_plan*/

        Route::resource('international_diplomas_courses_user_plan', 'Admin\InternationalDiplomasCoursesUserPlanController', ['names' => ['create' => 'international_diplomas_courses_user_plan_add', 'store' => 'international_diplomas_courses_user_plan_add', 'index' => 'international_diplomas_courses_user_plan', 'edit' => 'international_diplomas_courses_user_plan_edit', 'update' => 'international_diplomas_courses_user_plan_edit', 'destroy' => 'international_diplomas_courses_user_plan_delete'],'parameters' => [
            'international_diplomas_courses_user_plan' => 'idcup'
        ]]);
        Route::post('international_diplomas_courses_user_plan_search', ['uses' => 'Admin\InternationalDiplomasCoursesUserPlanController@search', 'as' => 'international_diplomas_courses_user_plan_view']);
        Route::post('international_diplomas_courses_user_plan/activation', ['uses' => 'Admin\InternationalDiplomasCoursesUserPlanController@activation', 'as' => 'international_diplomas_courses_user_plan_active']);
        Route::post('international_diplomas_courses_user_plan/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'international_diplomas_courses_user_plan_add']);

        /*START mba_courses_user_plan*/
        Route::resource('mba_courses_user_plan', 'Admin\MbaCoursesUserPlanController', ['names' => ['create' => 'mba_courses_user_plan_add', 'store' => 'mba_courses_user_plan_add', 'index' => 'mba_courses_user_plan', 'edit' => 'mba_courses_user_plan_edit', 'update' => 'mba_courses_user_plan_edit', 'destroy' => 'mba_courses_user_plan_delete']]);
        Route::post('mba_courses_user_plan_search', ['uses' => 'Admin\MbaCoursesUserPlanController@search', 'as' => 'mba_courses_user_plan_view']);
        Route::post('mba_courses_user_plan/activation', ['uses' => 'Admin\MbaCoursesUserPlanController@activation', 'as' => 'mba_courses_user_plan_active']);
        Route::post('mba_courses_user_plan/autoCompleteUsers', ['uses' => 'Admin\NormalUserController@autoCompleteUsers', 'as' => 'mba_courses_user_plan_add']);

        /*END mba_courses_user_plan*/

        Route::resource('promotion_code_used', 'Admin\PromotionCodeUsedController', ['names' => [ 'index' => 'promotion_code_used_view']]);
        Route::get('promotion_code_used_report1',['uses' => 'Admin\PromotionCodeUsedController@report1', 'as' => 'promotion_code_used_report1'] );
        Route::get('promotion_code_used_report2',['uses' => 'Admin\PromotionCodeUsedController@report2', 'as' => 'promotion_code_used_report2'] );
        Route::post('promotion_code_used_search', ['uses' => 'Admin\PromotionCodeUsedController@search', 'as' => 'promotion_code_used_view']);
        Route::post('promotion_code_used_search1', ['uses' => 'Admin\PromotionCodeUsedController@search1', 'as' => 'promotion_code_used_report1']);
        Route::post('promotion_code_used_search2', ['uses' => 'Admin\PromotionCodeUsedController@search2', 'as' => 'promotion_code_used_report2']);
        Route::get('promotion_code_used_report1_export', ['uses' => 'Admin\PromotionCodeUsedController@report1_export', 'as' => 'promotion_code_used_report1']);
        Route::get('promotion_code_used_report2_export', ['uses' => 'Admin\PromotionCodeUsedController@report2_export', 'as' => 'promotion_code_used_report2']);

        Route::resource('promotion_code', 'Admin\PromotionCodeController', ['names' => [ 'create' => 'promotion_code_add', 'store' => 'promotion_code_add']]);
        Route::post('promotion_code/getPriceByType', ['uses' => 'Admin\PromotionCodeController@getPriceByType', 'as' => 'promotion_code_add']);
        Route::post('promotion_code/generateCode', ['uses' => 'Admin\PromotionCodeController@generateCode', 'as' => 'promotion_code_add']);


    });


});

Route::get('survey_result','SurveyResultController@index');
Route::post('survey_result_parse','SurveyResultController@parse');
Route::post('survey_result_export','SurveyResultController@export');

Route::get('export', 'ExportController@index');
Route::post('export', 'ExportController@export');
Route::post('search/export', 'ExportController@search');
Route::get('exportCoursesPercentage', 'ExportController@exportCoursesPercentage');

Route::get('admin/get_sections/{id}', ['uses' => 'Admin\CoursesResourcesController@get_sections', 'as' => 'get_sections']);
Route::post('sendSurveyEmail', 'SurveyResultController@sendSurveyEmail');

Route::get('sendMbaTemplates', 'MbaTemplatesController@index');
