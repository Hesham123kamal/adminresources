<ul class="page-sidebar-menu @if(getUserSystem('backend_sidebar_menu_style')=='light') page-sidebar-menu-light @endif  page-header-fixed @if(Request::path()=='admin/notifications') page-sidebar-menu-closed @endif"
    data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
    <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
    <li class="sidebar-toggler-wrapper hide">
        <div class="sidebar-toggler"><span></span></div>
    </li>    <!-- END SIDEBAR TOGGLER BUTTON -->
    <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
    <li class="sidebar-search-wrapper">        <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
        <!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
        <!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
        <!--<form class="sidebar-search  " action="page_general_search_3.html" method="POST">            <a href="javascript:;" class="remove">                <i class="icon-close"></i>            </a>            <div class="input-group">                <input type="text" class="form-control" placeholder="Search...">                <span class="input-group-btn">                    <a href="javascript:;" class="btn submit">                        <i class="icon-magnifier"></i>                    </a>                </span>            </div>        </form>-->
        <!-- END RESPONSIVE QUICK SEARCH FORM -->    </li>
    <li class="nav-item  @if(Request::path()=='admin') start active open @endif">
        <a href="{{ URL('admin') }}" class="nav-link nav-toggle">
            <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.dashboard') }}</span>
            <span class="selected"></span>
        </a>
    </li>
    @if(PerUser('profiles') || PerUser('users') || PerUser('employee') )
        <li class="nav-item @if(in_array(Route::currentRouteName(),['profiles','profiles_add','profiles_edit',
    'users','users_add','users_edit',
    'employee','employee_add','employee_edit'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.system') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['profiles','profiles_add','profiles_edit',
                'users','users_add','users_edit',
                'employee','employee_add','employee_edit'])) style="display: block;" @endif>
                @if(PerUser('profiles'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['profiles','profiles_add','profiles_edit'])) start active open @endif">
                        <a href="{{ URL('admin/profiles') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.profiles') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('users'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['users','users_add','users_edit'])) start active open @endif">
                        <a href="{{ URL('admin/users') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.users') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('employee'))
                    <li class="nav-item   @if(in_array(Route::currentRouteName(),['employee','employee_add','employee_edit'])) start active open @endif">
                        <a href="{{ URL('admin/employee') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.employees') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    @if(PerUser('quality'))
        <li class="nav-item @if(in_array(Route::currentRouteName(),['quality','mba_progress'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.quality') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['quality','mba_progress'])) style="display: block;" @endif>
                @if(PerUser('mba_progress'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['mba_progress'])) start active open @endif">
                        <a href="{{ URL('admin/mba_progress') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.mba_progress') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    @if(PerUser('normal_user') || PerUser('users_suspend') || PerUser('users_sessions') || PerUser('users_suspend_liteversion'))
        <li class="nav-item @if(in_array(Route::currentRouteName(),['normal_user','normal_user_add','normal_user_edit',
    'users_suspend','users_sessions',
    'users_suspend_liteversion','users_suspend_liteversion_add','users_suspend_liteversion_edit'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.users') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['normal_user','normal_user_add','normal_user_edit',
    'users_suspend','users_sessions','users_suspend_liteversion','users_suspend_liteversion_add','users_suspend_liteversion_edit'])) style="display: block;" @endif>
                @if(PerUser('normal_user'))
                    <li class="nav-item   @if(in_array(Route::currentRouteName(),['normal_user','normal_user_add','normal_user_edit'])) start active open @endif">
                        <a href="{{ URL('admin/normal_user') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.normal_users') }}</span>
                        </a>
                    </li>
                @endif

                @if(PerUser('users_suspend'))
                    <li class="nav-item   @if(in_array(Route::currentRouteName(),['users_suspend'])) start active open @endif">
                        <a href="{{ URL('admin/users_suspend') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.users_suspend') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('users_suspend_liteversion'))
                    <li class="nav-item   @if(in_array(Route::currentRouteName(),['users_suspend_liteversion','users_suspend_liteversion_add','users_suspend_liteversion_edit'])) start active open @endif">
                        <a href="{{ URL('admin/users_suspend_liteversion') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.users_suspend_liteversion') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('users_sessions'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['users_sessions'])) start active open @endif">
                        <a href="{{ URL('admin/users_sessions') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.users_sessions') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    @if(PerUser('articles') || PerUser('author'))
    <li class="nav-item @if(in_array(Route::currentRouteName(),['articles','articles_add','articles_edit',
    'author','author_add','author_edit'])) open @endif">
    <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.articles') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['articles','articles_add','articles_edit',
                'author','author_add','author_edit'])) style="display: block;" @endif>
            @if(PerUser('articles'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['articles','articles_add','articles_edit'])) start active open @endif">
                    <a href="{{ URL('admin/articles') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.articles') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('author'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['author','author_add','author_edit'])) start active open @endif">
                    <a href="{{ URL('admin/author') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.author') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('initiative_sections'))
        <li class="nav-item @if(in_array(Route::currentRouteName(),['initiative_sections','initiative_sections_add','initiative_sections_edit'
        ,'initiative_videos','initiative_videos_add','initiative_videos_edit' ,'initiative_articles','initiative_articles_add','initiative_articles_edit'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.initiative') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['initiative_sections','initiative_sections_add','initiative_sections_edit'
                ,'initiative_videos','initiative_videos_add','initiative_videos_edit','initiative_articles','initiative_articles_add','initiative_articles_edit'])) style="display: block;" @endif>
                @if(PerUser('initiative_sections'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['initiative_sections','initiative_sections_add','initiative_sections_edit'])) start active open @endif">
                        <a href="{{ URL('admin/initiative_sections') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.initiative_sections') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('initiative_videos'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['initiative_videos','initiative_videos_add','initiative_videos_edit'])) start active open @endif">
                        <a href="{{ URL('admin/initiative_videos') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.initiative_videos') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('initiative_articles'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['initiative_articles','initiative_articles_add','initiative_articles_edit'])) start active open @endif">
                        <a href="{{ URL('admin/initiative_articles') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.initiative_articles') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(PerUser('books') || PerUser('books_requests')|| PerUser('books_playlist'))
        <li class="nav-item
    @if(in_array(Route::currentRouteName(),['books','books_add','books_edit',
    'books_playlist','books_playlist_add','books_playlist_edit',
    'books_requests']))
                open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.books') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['books','books_add','books_edit',
                'books_playlist','books_playlist_add','books_playlist_edit',
                'books_requests'])) style="display: block;" @endif>
                @if(PerUser('books'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['books','books_add','books_edit'])) start active open @endif">
                        <a href="{{ URL('admin/books') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.books') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('books_requests'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['books_requests'])) start active open @endif">
                        <a href="{{ URL('admin/books_requests') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.books_requests') }}</span>
                        </a>
                    </li>
                @endif
                    @if(PerUser('books_playlist'))
                        <li class="nav-item  @if(in_array(Route::currentRouteName(),['books_playlist','books_playlist_add','books_playlist_edit'])) start active open @endif">
                            <a href="{{ URL('admin/books_playlist') }}" class="nav-link ">
                                <i class="fa fa-users"></i>
                                <span class="title">{{ Lang::get('main.books_playlist') }}</span>
                            </a>
                        </li>
                    @endif
            </ul>
        </li>
    @endif


    @if(PerUser('tags'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['tags','tags_add','tags_edit'])) start active open @endif">
            <a href="{{ URL('admin/tags') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.tags') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('live'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['live','live_add','live_edit'])) start active open @endif">
            <a href="{{ URL('admin/live') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.live') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('old_urls'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['old_urls','old_urls_add','old_urls_edit'])) start active open @endif">
            <a href="{{ URL('admin/old_urls') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.old_urls') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('courses') || PerUser('courses_resources') || PerUser('courses_questions') || PerUser('request_courses') || PerUser('courses_offers') || PerUser('courses_sections') || PerUser('course_curriculum') || PerUser('my_courses') || PerUser('courses_curriculum_certificates') || PerUser('users_curriculum_answers') || PerUser('courses_QandA'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['courses','courses_add','courses_edit',
    'courses_resources','courses_resources_add','courses_resources_edit',
    'courses_questions','courses_questions_add','courses_questions_edit',
    'request_courses','request_courses_add','request_courses_edit',
    'courses_offers','courses_offers_add','courses_offers_edit',
    'courses_sections','courses_sections_add','courses_sections_edit',
    'course_curriculum','course_curriculum_add','course_curriculum_edit',
    'my_courses','my_courses_add','my_courses_edit',
    'courses_curriculum_certificates','courses_curriculum_certificates_add','courses_curriculum_certificates_edit',
    'users_curriculum_answers','users_curriculum_answers_add','users_curriculum_answers_edit','courses_QandA','courses_QandA_edit']))
            open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.courses') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['courses','courses_add','courses_edit',
                'courses_resources','courses_resources_add','courses_resources_edit',
                'courses_questions','courses_questions_add','courses_questions_edit',
                'request_courses','request_courses_add','request_courses_edit',
                'courses_offers','courses_offers_add','courses_offers_edit',
                'courses_sections','courses_sections_add','courses_sections_edit',
                'course_curriculum','course_curriculum_add','course_curriculum_edit',
                'my_courses','my_courses_add','my_courses_edit',
                'courses_curriculum_certificates','courses_curriculum_certificates_add','courses_curriculum_certificates_edit',
                'users_curriculum_answers','users_curriculum_answers_add','users_curriculum_answers_edit','courses_QandA','courses_QandA_edit'])) style="display: block;" @endif>
            @if(PerUser('courses'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses','courses_add','courses_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('courses_resources'))
                <li class="nav-item @if(in_array(Route::currentRouteName(),['courses_resources','courses_resources_add','courses_resources_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_resources') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_resources') }}</span>
                    </a>
                </li>
            @endif
            {{-- @if(PerUser('courses_questions'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_questions','courses_questions_add','courses_questions_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_questions') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_questions') }}</span>
                    </a>
                </li>
            @endif --}}
           {{-- @if(PerUser('courses_questions'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_questions','courses_questions_add','courses_questions_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_questions2') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_questions') }}</span>
                    </a>
                </li>
            @endif--}}
            {{--@if(PerUser('courses_questions'))--}}
                {{--<li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_questions','courses_questions_add','courses_questions_edit'])) start active open @endif">--}}
                    {{--<a href="{{ URL('admin/courses_questions') }}" class="nav-link ">--}}
                        {{--<i class="fa fa-users"></i>--}}
                        {{--<span class="title">{{ Lang::get('main.courses_questions') }}</span>--}}
                    {{--</a>--}}
                {{--</li>--}}
            {{--@endif--}}
            @if(PerUser('courses_questions'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_questions','courses_questions_add','courses_questions_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_questions3') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="badge badge-danger"> Beta </span>
                        <span class="title">{{ Lang::get('main.courses_questions') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('request_courses'))
                <li class="nav-item @if(in_array(Route::currentRouteName(),['request_courses','request_courses_add','request_courses_edit'])) start active open @endif">
                    <a href="{{ URL('admin/request_courses') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.request_courses') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('courses_offers'))
                <li class="nav-item @if(in_array(Route::currentRouteName(),['courses_offers','courses_offers_add','courses_offers_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_offers') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_offers') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('courses_sections'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_sections','courses_sections_add','courses_sections_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_sections') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_sections') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('course_curriculum'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['course_curriculum','course_curriculum_add','course_curriculum_edit'])) start active open @endif">
                    <a href="{{ URL('admin/course_curriculum') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.course_curriculum') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('my_courses'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['my_courses','my_courses_add','my_courses_edit'])) start active open @endif">
                    <a href="{{ URL('admin/my_courses') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.my_courses') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('courses_curriculum_certificates'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_curriculum_certificates','courses_curriculum_certificates_add','courses_curriculum_certificates_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_curriculum_certificates') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_curriculum_certificates') }}</span>
                    </a>
                </li>
            @endif

            @if(PerUser('users_curriculum_answers'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['users_curriculum_answers','users_curriculum_answers_add','users_curriculum_answers_edit'])) start active open @endif">
                    <a href="{{ URL('admin/users_curriculum_answers') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.users_curriculum_answers') }}</span>
                    </a>
                </li>
            @endif
                @if(PerUser('courses_QandA'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_QandA','courses_QandA_view','courses_QandA_edit'])) start active open @endif">
                        <a href="{{ URL('admin/courses_QandA') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.courses_QandA') }}</span>
                        </a>
                    </li>
                @endif
        </ul>
    </li>
    @endif

    @if(PerUser('diplomas') || PerUser('diploma_certificates') || PerUser('diploma_courses') || PerUser('diploma_user_courses') || PerUser('diplomas_charge_transaction_suspend_log') || PerUser('diplomas_charge_transaction') || PerUser('diplomas_targets') || PerUser('diplomas_courses_user_plan'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['diplomas','diplomas_add','diplomas_edit',
        'diploma_certificates','diploma_certificates_add','diploma_certificates_edit',
        'diploma_courses','diploma_courses_add','diploma_courses_edit',
        'diploma_user_courses','diploma_user_courses_add','diploma_user_courses_edit',
        'diplomas_charge_transaction_suspend_log','diplomas_charge_transaction_suspend_log_add','diplomas_charge_transaction_suspend_log_edit',
        'diplomas_charge_transaction','diplomas_charge_transaction_add','diplomas_charge_transaction_edit',
         'diplomas_companies_charge_transaction','diplomas_companies_charge_transaction_add','diplomas_companies_charge_transaction_edit',
        'diplomas_targets','diplomas_targets_add','diplomas_targets_edit',
        'diplomas_courses_user_plan','diplomas_courses_user_plan_add','diplomas_courses_user_plan_edit',
        ])) open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.diplomas') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"     @if(in_array(Route::currentRouteName(),['diplomas','diplomas_add','diplomas_edit',
        'diploma_certificates','diploma_certificates_add','diploma_certificates_edit',
        'diploma_courses','diploma_courses_add','diploma_courses_edit',
        'diploma_user_courses','diploma_user_courses_add','diploma_user_courses_edit',
        'diplomas_charge_transaction_suspend_log','diplomas_charge_transaction_suspend_log_add','diplomas_charge_transaction_suspend_log_edit',
        'diplomas_charge_transaction','diplomas_charge_transaction_add','diplomas_charge_transaction_edit',
        'diplomas_companies_charge_transaction','diplomas_companies_charge_transaction_add','diplomas_companies_charge_transaction_edit',
        'diplomas_targets','diplomas_targets_add','diplomas_targets_edit',
        'diplomas_courses_user_plan','diplomas_courses_user_plan_add','diplomas_courses_user_plan_edit',
        ])) style="display: block;" @endif>
            @if(PerUser('diplomas'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diplomas','diplomas_add','diplomas_edit'])) start active open @endif">
                    <a href="{{ URL('admin/diplomas') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diplomas') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('diploma_certificates'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diploma_certificates','diploma_certificates_add','diploma_certificates_edit'])) start active open @endif">
                    <a href="{{ URL('admin/diploma_certificates') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diploma_certificates') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('diploma_courses'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diploma_courses','diploma_courses_add','diploma_courses_edit'])) start active open @endif">
                    <a href="{{ URL('admin/diploma_courses') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diploma_courses') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('diploma_user_courses'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diploma_user_courses','diploma_user_courses_add','diploma_user_courses_edit'])) start active open @endif">
                    <a href="{{ URL('admin/diploma_user_courses') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diploma_user_courses') }}</span>
                    </a>
                </li>
            @endif

                @if(PerUser('diplomas_courses_user_plan'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['diplomas_courses_user_plan','diplomas_courses_user_plan_add','diplomas_courses_user_plan_edit'])) start active open @endif">
                        <a href="{{ URL('admin/diplomas_courses_user_plan') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.diplomas_courses_user_plan') }}</span>
                        </a>
                    </li>
                @endif

            @if(PerUser('diplomas_charge_transaction_suspend_log'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diplomas_charge_transaction_suspend_log','diplomas_charge_transaction_suspend_log_add','diplomas_charge_transaction_suspend_log_edit'])) start active open @endif">
                    <a href="{{ URL('admin/dctsl') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diplomas_charge_transaction_suspend_log') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('diplomas_charge_transaction'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diplomas_charge_transaction','diplomas_charge_transaction_add','diplomas_charge_transaction_edit'])) start active open @endif">
                    <a href="{{ URL('admin/dct') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diplomas_charge_transaction') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('diplomas_companies_charge_transaction'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diplomas_companies_charge_transaction','diplomas_companies_charge_transaction_add','diplomas_companies_charge_transaction_edit'])) start active open @endif">
                    <a href="{{ URL('admin/dcct') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diplomas_companies_charge_transaction') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('diplomas_targets'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['diplomas_targets','diplomas_targets_add','diplomas_targets_edit'])) start active open @endif">
                    <a href="{{ URL('admin/diplomas_targets') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.diplomas_targets') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('international_diplomas') || PerUser('international_diplomas_views') || PerUser('international_diploma_certificates') || PerUser('international_diploma_courses')  || PerUser('international_diplomas_charge_transaction_suspend_log') || PerUser('international_diplomas_charge_transaction')|| PerUser('international_categories')|| PerUser('international_diplomas_courses_user_plan') || PerUser('international_diploma_user_courses') )
        <li class="nav-item
    @if(in_array(Route::currentRouteName(),['international_diplomas','international_diplomas_add','international_diplomas_edit',
        'international_diploma_certificates','international_diploma_certificates_add','international_diploma_certificates_edit',
        'international_diploma_courses','international_diploma_courses_add','international_diploma_courses_edit',
        'international_diplomas_charge_transaction_suspend_log','international_diplomas_charge_transaction_suspend_log_add','international_diplomas_charge_transaction_suspend_log_edit',
        'international_diplomas_charge_transaction','international_diplomas_charge_transaction_add','international_diplomas_charge_transaction_edit',
        'international_diplomas_views','international_diplomas_views_add','international_international_diplomas_views_edit',
        'international_categories','international_categories_add','international_categories_edit',
        'international_diplomas_courses_user_plan','international_diplomas_courses_user_plan_add','international_diplomas_courses_user_plan_edit',
        'international_diploma_user_courses','international_diploma_user_courses_add','international_diploma_user_courses_edit',
        ])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.international_diplomas') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"     @if(in_array(Route::currentRouteName(),['international_diplomas','international_diplomas_add','international_diplomas_edit',
        'international_diploma_certificates','international_diploma_certificates_add','international_diploma_certificates_edit',
        'international_diploma_courses','international_diploma_courses_add','international_diploma_courses_edit',
        'international_diplomas_charge_transaction_suspend_log','international_diplomas_charge_transaction_suspend_log_add','international_diplomas_charge_transaction_suspend_log_edit',
        'international_diplomas_charge_transaction','international_diplomas_charge_transaction_add','international_diplomas_charge_transaction_edit',
        'international_diplomas_views','international_diplomas_views_add','international_international_diplomas_views_edit',
        'international_categories','international_categories_add','international_categories_edit',
        'international_diplomas_courses_user_plan','international_diplomas_courses_user_plan_add','international_diplomas_courses_user_plan_edit',
        'international_diploma_user_courses','international_diploma_user_courses_add','international_diploma_user_courses_edit',
        ])) style="display: block;" @endif>
                @if(PerUser('international_diplomas'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diplomas','international_diplomas_add','international_diplomas_edit'])) start active open @endif">
                        <a href="{{ URL('admin/international_diplomas') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diplomas') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('international_diploma_certificates'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diploma_certificates','international_diploma_certificates_add','international_diploma_certificates_edit'])) start active open @endif">
                        <a href="{{ URL('admin/international_diploma_certificates') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diploma_certificates') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('international_diploma_courses'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diploma_courses','international_diploma_courses_add','international_diploma_courses_edit'])) start active open @endif">
                        <a href="{{ URL('admin/international_diploma_courses') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diploma_courses') }}</span>
                        </a>
                    </li>
                @endif

                @if(PerUser('international_diplomas_charge_transaction_suspend_log'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diplomas_charge_transaction_suspend_log','international_diplomas_charge_transaction_suspend_log_add','international_diplomas_charge_transaction_suspend_log_edit'])) start active open @endif">
                        <a href="{{ URL('admin/idctsl') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diplomas_charge_transaction_suspend_log') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('international_diplomas_charge_transaction'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diplomas_charge_transaction','international_diplomas_charge_transaction_add','international_diplomas_charge_transaction_edit'])) start active open @endif">
                        <a href="{{ URL('admin/idct') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diplomas_charge_transaction') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('international_diplomas_views'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diplomas_views','international_diplomas_views_add','international_diplomas_views_edit'])) start active open @endif">
                        <a href="{{ URL('admin/international_diplomas_views') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diplomas_views') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('international_categories'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_categories','international_categories_add','international_categories_edit'])) start active open @endif">
                        <a href="{{ URL('admin/international_categories') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_categories') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('international_diplomas_courses_user_plan'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diplomas_courses_user_plan','international_diplomas_courses_user_plan_add','international_diplomas_courses_user_plan_edit'])) start active open @endif">
                        <a href="{{ URL('admin/international_diplomas_courses_user_plan') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diplomas_courses_user_plan') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('international_diploma_user_courses'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['international_diploma_user_courses','international_diploma_user_courses_add','international_diploma_user_courses_edit'])) start active open @endif">
                        <a href="{{ URL('admin/international_diploma_user_courses') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.international_diploma_user_courses') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(PerUser('instructors') || PerUser('become_instructor'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['instructors','instructors_add','instructors_edit',
        'become_instructor','become_instructor_add','become_instructor_edit'])) open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.instructors') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['instructors','instructors_add','instructors_edit',
            'become_instructor','become_instructor_add','become_instructor_edit'])) style="display: block;" @endif>
            @if(PerUser('instructors'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['instructors','instructors_add','instructors_edit'])) start active open @endif">
                    <a href="{{ URL('admin/instructors') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.instructors') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('become_instructor'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['become_instructor','become_instructor_add','become_instructor_edit'])) start active open @endif">
                    <a href="{{ URL('admin/become_instructor') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.become_instructor') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('survey') || PerUser('survey_clients'))
        <li class="nav-item @if(in_array(Route::currentRouteName(),['survey','survey_add','survey_edit','survey_clients','survey_clients_add','survey_clients_edit'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.survey') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu" @if(in_array(Route::currentRouteName(),['survey','survey_add','survey_edit','survey_clients','survey_clients_add','survey_clients_edit'])) style="display: block;" @endif>

                @if(PerUser('survey_clients'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['survey_clients','survey_clients_add','survey_clients_edit'])) start active open @endif">
                        <a href="{{ URL('admin/survey_clients') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.survey_clients') }}</span>
                        </a>
                    </li>
                @endif

                @if(PerUser('survey'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['survey','survey_add','survey_edit'])) start active open @endif">
                        <a href="{{ URL('admin/survey') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.survey') }}</span>
                        </a>
                    </li>
                @endif



            </ul>
        </li>
    @endif

    @if(PerUser('mlm_requests'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['mlm_requests','mlm_requests_send'])) start active open @endif">
            <a href="{{ URL('admin/mlm_requests') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.mlm_requests') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('our_products'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['our_products','our_products_add','our_products_edit'])) start active open @endif">
            <a href="{{ URL('admin/our_products') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.our_products') }}</span>
            </a>
        </li>
    @endif


    @if(PerUser('our_products_courses'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['our_products_courses','our_products_courses_add','our_products_courses_edit'])) start active open @endif">
            <a href="{{ URL('admin/our_products_courses') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.our_products_courses') }}</span>
            </a>
        </li>
    @endif
    @if(PerUser('modules') || PerUser('modules_questions') || PerUser('modules_trainings') || PerUser('modules_trainings_questions') || PerUser('modules_projects') || PerUser('modules_users_projects') || PerUser('mba_charge_transaction') || PerUser('modules_helper_courses') || PerUser('modules_courses') || PerUser('modules_users_summary') || PerUser('mba_certificates')|| PerUser('mba_courses_user_plan'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['modules','modules_add','modules_edit',
        'modules_questions','modules_questions_add','modules_questions_edit',
        'modules_trainings','modules_trainings_add','modules_trainings_edit',
        'modules_trainings_questions','modules_trainings_questions_add','modules_trainings_questions_edit',
        'modules_projects','modules_projects_add','modules_projects_edit',
        'modules_users_projects','modules_users_projects_add','modules_users_projects_edit',
        'mba_charge_transaction','mba_charge_transaction_add','mba_charge_transaction_edit',
        'modules_helper_courses','modules_helper_courses_add','modules_helper_courses_edit',
        'modules_courses','modules_courses_add','modules_courses_edit',
        'mba_certificates','mba_certificates_add','mba_certificates_edit',
        'modules_users_summary','modules_users_summary_edit',
        'mba_courses_user_plan','mba_courses_user_plan_add','mba_courses_user_plan_edit'
    ])) open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.mba') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['modules','modules_add','modules_edit',
           'modules_questions','modules_questions_add','modules_questions_edit',
           'modules_trainings','modules_trainings_add','modules_trainings_edit',
           'modules_trainings_questions','modules_trainings_questions_add','modules_trainings_questions_edit',
           'modules_projects','modules_projects_add','modules_projects_edit',
           'modules_users_projects','modules_users_projects_add','modules_users_projects_edit',
           'mba_charge_transaction','mba_charge_transaction_add','mba_charge_transaction_edit',
           'modules_helper_courses','modules_helper_courses_add','modules_helper_courses_edit',
           'modules_courses','modules_courses_add','modules_courses_edit',
           'mba_certificates','mba_certificates_add','mba_certificates_edit',
           'modules_users_summary','modules_users_summary_edit',
           'mba_courses_user_plan','mba_courses_user_plan_add','mba_courses_user_plan_edit'
       ])) style="display: block;" @endif>
            @if(PerUser('modules'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules','modules_add','modules_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.modules') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('modules_courses'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_courses','modules_courses_add','modules_courses_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_courses') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.modules_courses') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('modules_questions'))
                {{--<li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_questions','modules_questions_add','modules_questions_edit'])) start active open @endif">--}}
                    {{--<a href="{{ URL('admin/modules_questions') }}" class="nav-link ">--}}
                        {{--<i class="fa fa-users"></i>--}}
                        {{--<span class="title">{{ Lang::get('main.modules_questions') }}</span>--}}
                    {{--</a>--}}
                {{--</li>--}}
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_questions','modules_questions_add','modules_questions_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_questions2') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="badge badge-danger"> Beta </span>
                        <span class="title">{{ Lang::get('main.modules_questions') }} </span>
                    </a>
                </li>
            @endif

            @if(PerUser('modules_trainings'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_trainings','modules_trainings_add','modules_trainings_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_trainings') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.modules_trainings') }}</span>
                    </a>
                </li>
            @endif

            {{--@if(PerUser('modules_trainings_questions'))--}}
                {{--<li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_trainings_questions','modules_trainings_questions_add','modules_trainings_questions_edit'])) start active open @endif">--}}
                    {{--<a href="{{ URL('admin/modules_trainings_questions') }}" class="nav-link ">--}}
                        {{--<i class="fa fa-users"></i>--}}
                        {{--<span class="title">{{ Lang::get('main.modules_trainings_questions') }}</span>--}}
                    {{--</a>--}}
                {{--</li>--}}
            {{--@endif--}}
            @if(PerUser('modules_trainings_questions'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_trainings_questions','modules_trainings_questions_add','modules_trainings_questions_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_trainings_questions2') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="badge badge-danger"> Beta </span>
                        <span class="title">{{ Lang::get('main.modules_trainings_questions') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('modules_projects'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_projects','modules_projects_add','modules_projects_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_projects') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.modules_projects') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('modules_users_projects'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_users_projects','modules_users_projects_add','modules_users_projects_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_users_projects') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.modules_users_projects') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('modules_helper_courses'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_helper_courses','modules_helper_courses_add','modules_helper_courses_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_helper_courses') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.modules_helper_courses') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('mba_charge_transaction'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['mba_charge_transaction','mba_charge_transaction_add','mba_charge_transaction_edit'])) start active open @endif">
                    <a href="{{ URL('admin/mba_charge_transaction') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.mba_charge_transaction') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('modules_users_summary'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['modules_users_summary','modules_users_summary_edit'])) start active open @endif">
                    <a href="{{ URL('admin/modules_users_summary') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.modules_users_summary') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('mba_certificates'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['mba_certificates','mba_certificates_add','mba_certificates_edit'])) start active open @endif">
                    <a href="{{ URL('admin/mba_certificates') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.mba_certificates') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('mba_courses_user_plan'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['mba_courses_user_plan','mba_courses_user_plan_add','mba_courses_user_plan_edit'])) start active open @endif">
                    <a href="{{ URL('admin/mba_courses_user_plan') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.mba_courses_user_plan') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('successstories'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['successstories','successstories_add','successstories_edit'])) start active open @endif">
            <a href="{{ URL('admin/successstories') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.successstories') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('webinars') || PerUser('webinar_resources'))
    <li class="nav-item
        @if(in_array(Route::currentRouteName(),['webinars','webinars_add','webinars_edit','webinar_resources','webinar_resources_add','webinar_resources_edit']))
            open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.webinars') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['webinars','webinars_add','webinars_edit','webinar_resources','webinar_resources_add','webinar_resources_edit'])) style="display: block;" @endif>
            @if(PerUser('webinar_resources'))
                <li class="nav-item @if(in_array(Route::currentRouteName(),['webinar_resources','webinar_resources_add','webinar_resources_edit'])) start active open @endif">
                    <a href="{{ URL('admin/webinar_resources') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.webinar_resources') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('webinars'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['webinars','webinars_add','webinars_edit'])) start active open @endif">
                    <a href="{{ URL('admin/webinars') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.webinars') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('static_pages'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['static_pages','static_pages_add','static_pages_edit'])) start active open @endif">
            <a href="{{ URL('admin/static_pages') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.static_pages') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('testimonials'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['testimonials','testimonials_add','testimonials_edit'])) start active open @endif">
            <a href="{{ URL('admin/testimonials') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.testimonials') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('promotion_code'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['promotion_code','promotion_code_add'])) start active open @endif">
            <a href="{{ URL('admin/promotion_code/create') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.promotion_code') }}</span>
            </a>
        </li>
    @endif



@if(PerUser('promotion_code_used_view') || PerUser('promotion_code_used_report1') || PerUser('promotion_code_used_report2'))
        <li class="nav-item
    @if(in_array(Route::currentRouteName(),['promotion_code_used_view','promotion_code_used_report1','promotion_code_used_report2']))
                open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.promotion_code_used') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['promotion_code_used_view','promotion_code_used_report1','promotion_code_used_report2'])) style="display: block;" @endif>
                @if(PerUser('promotion_code_used_view'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['promotion_code_used_view'])) start active open @endif">
                        <a href="{{ URL('admin/promotion_code_used') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.promotion_code_used') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('promotion_code_used_report1'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['promotion_code_used_report1'])) start active open @endif">
                        <a href="{{ URL('admin/promotion_code_used_report1') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.promotion_code_used_report1') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('promotion_code_used_report2'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['promotion_code_used_report2'])) start active open @endif">
                        <a href="{{ URL('admin/promotion_code_used_report2') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.promotion_code_used_report2') }}</span>
                        </a>
                    </li>
                @endif

            </ul>
        </li>
    @endif

    @if(PerUser('our_partner') || PerUser('partner_requests') || PerUser('our_partner_flags'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['our_partner','our_partner_add','our_partner_edit',
    'partner_requests','partner_requests_add','partner_requests_edit',
    'our_partner_flags','our_partner_flags_add','our_partner_flags_edit']))
            open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.partners') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['our_partner','our_partner_add','our_partner_edit',
            'partner_requests','partner_requests_add','partner_requests_edit',
            'our_partner_flags','our_partner_flags_add','our_partner_flags_edit'])) style="display: block;" @endif>
            @if(PerUser('partner_requests'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['partner_requests','partner_requests_add','partner_requests_edit'])) start active open @endif">
                    <a href="{{ URL('admin/partner_requests') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.partner_requests') }}</span>
                    </a>
                </li>
            @endif

            @if(PerUser('our_partner'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['our_partner','our_partner_add','our_partner_edit'])) start active open @endif">
                    <a href="{{ URL('admin/our_partner') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.our_partners') }}</span>
                    </a>
                </li>
            @endif

            @if(PerUser('our_partner_flags'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['our_partner_flags','our_partner_flags_add','our_partner_flags_edit'])) start active open @endif">
                    <a href="{{ URL('admin/our_partner_flags') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.our_partner_flags') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('events'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['events','events_add','events_edit'])) start active open @endif">
            <a href="{{ URL('admin/events') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.events') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('gallery'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['gallery','gallery_add','gallery_edit'])) start active open @endif">
            <a href="{{ URL('admin/gallery') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.gallery') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('mobile_notifications'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['mobile_notifications','mobile_notifications_add'])) start active open @endif">
            <a href="{{ URL('admin/mobile_notifications') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.mobile_notifications') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('tpay_price_book'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['tpay_price_book','tpay_price_book_add','tpay_price_book_edit'])) start active open @endif">
            <a href="{{ URL('admin/tpay_price_book') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.tpay_price_book') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('installment_payment'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['installment_payment','installment_payment_add','installment_payment_edit'])) start active open @endif">
            <a href="{{ URL('admin/installment_payment') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.installment_payment') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('faq'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['faq','faq_add','faq_edit'])) start active open @endif">
            <a href="{{ URL('admin/faq') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.faq') }}</span>
            </a>
        </li>
    @endif
    @if(PerUser('site_faq'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['site_faq','site_faq_add','site_faq_edit'])) start active open @endif">
            <a href="{{ URL('admin/site_faq') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.site_faq') }}</span>
            </a>
        </li>
    @endif
    @if(PerUser('site_faq_type'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['site_faq_type','site_faq_type_add','site_faq_type_edit'])) start active open @endif">
            <a href="{{ URL('admin/site_faq_type') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.site_faq_type') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('users_cvs'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['users_cvs'])) start active open @endif">
            <a href="{{ URL('admin/users_cvs') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.users_cvs') }}</span>
            </a>
        </li>
    @endif

    @if(PerUser('recruitment_jobs') || PerUser('recruitment_job_types') || PerUser('recruitment_jobs_types') || PerUser('recruitment_job_roles') || PerUser('recruitment_jobs_roles') || PerUser('recruitment_industries') || PerUser('recruitment_currencies') || PerUser('recruitment_companies') || PerUser('recruitment_employees') || PerUser('recruit') || PerUser('recruitment_employee_job_apply') || PerUser('recruit_users'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['recruitment_jobs','recruitment_jobs_add','recruitment_jobs_edit',
    'recruitment_job_types','recruitment_job_types_add','recruitment_job_types_edit',
    'recruitment_jobs_types','recruitment_jobs_types_add','recruitment_jobs_types_edit',
    'recruitment_job_roles','recruitment_job_roles_add','recruitment_job_roles_edit',
    'recruitment_jobs_roles','recruitment_jobs_roles_add','recruitment_jobs_roles_edit',
    'recruitment_industries','recruitment_industries_add','recruitment_industries_edit',
    'recruitment_currencies','recruitment_currencies_add','recruitment_currencies_edit',
    'recruitment_companies','recruitment_companies_add','recruitment_companies_edit',
    'recruitment_employees','recruitment_employees_add','recruitment_employees_edit',
    'recruit','recruit_add','recruit_edit',
    'recruitment_employee_job_apply','recruitment_employee_job_apply_add','recruitment_employee_job_apply_edit',
    'recruit_users','recruit_users_add','recruit_users_edit'])) open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.recruitment') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu" @if(in_array(Route::currentRouteName(),['recruitment_jobs','recruitment_jobs_add','recruitment_jobs_edit',
    'recruitment_job_types','recruitment_job_types_add','recruitment_job_types_edit',
    'recruitment_jobs_types','recruitment_jobs_types_add','recruitment_jobs_types_edit',
    'recruitment_job_roles','recruitment_job_roles_add','recruitment_job_roles_edit',
    'recruitment_jobs_roles','recruitment_jobs_roles_add','recruitment_jobs_roles_edit',
    'recruitment_industries','recruitment_industries_add','recruitment_industries_edit',
    'recruitment_currencies','recruitment_currencies_add','recruitment_currencies_edit',
    'recruitment_companies','recruitment_companies_add','recruitment_companies_edit',
    'recruitment_employees','recruitment_employees_add','recruitment_employees_edit',
    'recruit','recruit_add','recruit_edit',
    'recruitment_employee_job_apply','recruitment_employee_job_apply_add','recruitment_employee_job_apply_edit',
    'recruit_users','recruit_users_add','recruit_users_edit'])) style="display: block;" @endif>
            @if(PerUser('recruitment_jobs'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_jobs','recruitment_jobs_add','recruitment_jobs_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_jobs') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_jobs') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_job_types'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_job_types','recruitment_job_types_add','recruitment_job_types_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_job_types') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_job_types') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_jobs_types'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_jobs_types','recruitment_jobs_types_add','recruitment_jobs_types_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_jobs_types') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_jobs_types') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_job_roles'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_job_roles','recruitment_job_roles_add','recruitment_job_roles_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_job_roles') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_job_roles') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_jobs_roles'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_jobs_roles','recruitment_jobs_roles_add','recruitment_jobs_roles_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_jobs_roles') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_jobs_roles') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_industries'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_industries','recruitment_industries_add','recruitment_industries_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_industries') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_industries') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_currencies'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_currencies','recruitment_currencies_add','recruitment_currencies_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_currencies') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_currencies') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_companies'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_companies','recruitment_companies_add','recruitment_companies_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_companies') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_companies') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_employees'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_employees','recruitment_employees_add','recruitment_employees_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_employees') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_employees') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruit'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruit','recruit_add','recruit_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruit') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruit') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruitment_employee_job_apply'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruitment_employee_job_apply','recruitment_employee_job_apply_add','recruitment_employee_job_apply_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruitment_employee_job_apply') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruitment_employee_job_apply') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('recruit_users'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['recruit_users','recruit_users_add','recruit_users_edit'])) start active open @endif">
                    <a href="{{ URL('admin/recruit_users') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.recruit_users') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('payment_transaction') || PerUser('support_transaction') || PerUser('charge_transaction') )
        <li class="nav-item @if(in_array(Route::currentRouteName(),['payment_transaction','payment_transaction_add','payment_transaction_edit',
    'support_transaction','support_transaction_add','support_transaction_edit',
    'charge_transaction','charge_transaction_add','charge_transaction_edit',
    'lite_version_charge_transaction','lite_version_charge_transaction_add','lite_version_charge_transaction_edit'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.charge_transactions') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['payment_transaction','payment_transaction_add','payment_transaction_edit',
    'support_transaction','support_transaction_add','support_transaction_edit',
    'charge_transaction','charge_transaction_add','charge_transaction_edit',
    'lite_version_charge_transaction','lite_version_charge_transaction_add','lite_version_charge_transaction_edit'])) style="display: block;" @endif>
                @if(PerUser('payment_transaction'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['payment_transaction','payment_transaction_add','payment_transaction_edit'])) start active open @endif">
                        <a href="{{ URL('admin/payment_transaction') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.payment_transaction') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('support_transaction'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['support_transaction','support_transaction_add','support_transaction_edit'])) start active open @endif">
                        <a href="{{ URL('admin/support_transaction') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.support_transaction') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('charge_transaction'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['charge_transaction','charge_transaction_add','charge_transaction_edit'])) start active open @endif">
                        <a href="{{ URL('admin/charge_transaction') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.charge_transaction') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('lite_version_charge_transaction'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['lite_version_charge_transaction','lite_version_charge_transaction_add','lite_version_charge_transaction_edit'])) start active open @endif">
                        <a href="{{ URL('admin/lite_version_charge_transaction') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.lite_version_charge_transaction') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(PerUser('automation'))
        <li class="nav-item  @if(in_array(Route::currentRouteName(),['automation','automation_add','automation_edit'])) start active open @endif">
            <a href="{{ URL('admin/automation') }}" class="nav-link ">
                <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.automation') }}</span>
            </a>
        </li>
    @endif
    @if(PerUser('user_contractid') || PerUser('user_contractid_notifications') )
        <li class="nav-item @if(in_array(Route::currentRouteName(),['user_contractid','user_contractid_add','user_contractid_edit',
        'user_contractid_notifications','user_contractid_notifications_add','user_contractid_notifications_edit'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.user_contract') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['user_contractid','user_contractid_add','user_contractid_edit',
        'user_contractid_notifications','user_contractid_notifications_add','user_contractid_notifications_edit'])) style="display: block;" @endif>
                @if(PerUser('user_contractid'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['user_contractid','user_contractid_add','user_contractid_edit'])) start active open @endif">
                        <a href="{{ URL('admin/user_contractid') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.user_contractid') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('user_contractid_notifications'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['user_contractid_notifications','user_contractid_notifications_add','user_contractid_notifications_edit'])) start active open @endif">
                        <a href="{{ URL('admin/user_contractid_notifications') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.user_contractid_notifications') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(PerUser('company') || PerUser('company_request') || PerUser('companies_admins')
    || PerUser('companies_charge_transaction') || PerUser('lite_version_companies_charge_transaction') || PerUser('mba_companies_charge_transaction'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['company','company_add','company_edit','company_request','company_request_add','company_request_edit','companies_admins','companies_admins_add','companies_admins_edit'])) open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.companies') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['company','company_add','company_edit','company_request','company_request_add','company_request_edit','companies_admins','companies_admins_add','companies_admins_edit'])) style="display: block;" @endif>
            @if(PerUser('company'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['company','company_add','company_edit'])) start active open @endif">
                    <a href="{{ URL('admin/company') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.companies') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('company_request'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['company_request','company_request_add','company_request_edit'])) start active open @endif">
                    <a href="{{ URL('admin/company_request') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.company_request') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('companies_admins'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['companies_admins','companies_admins_add','companies_admins_edit'])) start active open @endif">
                    <a href="{{ URL('admin/companies_admins') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.companies_admins') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('companies_charge_transaction'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['companies_charge_transaction'])) start active open @endif">
                    <a href="{{ URL('admin/cct') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.companies_charge_transaction') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('lite_version_companies_charge_transaction'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['lite_version_companies_charge_transaction'])) start active open @endif">
                    <a href="{{ URL('admin/lcct') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.lite_version_companies_charge_transaction') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('mba_companies_charge_transaction'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['mba_companies_charge_transaction'])) start active open @endif">
                    <a href="{{ URL('admin/mcct') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.mba_companies_charge_transaction') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('apple_users') || PerUser('apple_products') || PerUser('apple_users_charge_transactions'))
        <li class="nav-item
    @if(in_array(Route::currentRouteName(),['apple_users','apple_users_add','apple_users_edit','apple_products','apple_products_add','apple_products_edit','apple_users_charge_transactions','apple_users_charge_transactions_add','apple_users_charge_transactions_edit'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.apple') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['apple_users','apple_users_add','apple_users_edit','apple_products','apple_products_add','apple_products_edit','apple_users_charge_transactions','apple_users_charge_transactions_add','apple_users_charge_transactions_edit'])) style="display: block;" @endif>
                @if(PerUser('apple_users'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['apple_users','apple_users_add','apple_users_edit'])) start active open @endif">
                        <a href="{{ URL('admin/apple_users') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.apple_users') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('apple_products'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['apple_products','apple_products_add','apple_products_edit'])) start active open @endif">
                        <a href="{{ URL('admin/apple_products') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.apple_products') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('apple_users_charge_transactions'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['apple_users_charge_transactions','apple_users_charge_transactions_add','apple_users_charge_transactions_edit'])) start active open @endif">
                        <a href="{{ URL('admin/apple_users_charge_transactions') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.apple_users_charge_transactions') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(PerUser('all_categories') || PerUser('sub_categories') || PerUser('categories') || PerUser('courses_categories') || PerUser('books_categories') || PerUser('webinars_categories') || PerUser('successtories_categories'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['all_categories','all_categories_add','all_categories_edit',
    'sub_categories','sub_categories_add','sub_categories_edit',
    'categories','categories_add','categories_edit',
    'courses_categories','courses_categories_add','courses_categories_edit',
    'books_categories','books_categories_add','books_categories_edit',
    'webinars_categories','webinars_categories_add','webinars_categories_edit',
    'successtories_categories','successtories_categories_add','successtories_categories_edit'
    ])) open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.all_categories') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu" @if(in_array(Route::currentRouteName(),['all_categories','all_categories_add','all_categories_edit',
    'sub_categories','sub_categories_add','sub_categories_edit',
    'categories','categories_add','categories_edit',
    'courses_categories','courses_categories_add','courses_categories_edit',
    'books_categories','books_categories_add','books_categories_edit',
    'webinars_categories','webinars_categories_add','webinars_categories_edit',
    'successtories_categories','successtories_categories_add','successtories_categories_edit'
    ])) style="display: block;" @endif>
            @if(PerUser('all_categories'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['all_categories','all_categories_add','all_categories_edit'])) start active open @endif">
                    <a href="{{ URL('admin/all_categories') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.all_categories') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('sub_categories'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['sub_categories','sub_categories_add','sub_categories_edit'])) start active open @endif">
                    <a href="{{ URL('admin/sub_categories') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.sub_categories') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('categories'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['categories','categories_add','categories_edit'])) start active open @endif">
                    <a href="{{ URL('admin/categories') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.categories') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('courses_categories'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_categories','courses_categories_add','courses_categories_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_categories') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_categories') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('books_categories'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['books_categories','books_categories_add','books_categories_edit'])) start active open @endif">
                    <a href="{{ URL('admin/books_categories') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.books_categories') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('webinars_categories'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['webinars_categories','webinars_categories_add','webinars_categories_edit'])) start active open @endif">
                    <a href="{{ URL('admin/webinars_categories') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.webinars_categories') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('successtories_categories'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['successtories_categories','successtories_categories_add','successtories_categories_edit'])) start active open @endif">
                    <a href="{{ URL('admin/successtories_categories') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.successtories_categories') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    @if(PerUser('contactus') ||PerUser('contactus_view') ||PerUser('session_courses_views') || PerUser('session_successtories_views') || PerUser('webinars_views') || PerUser('session_diplomas_views') || PerUser('session_webinars_views') || PerUser('courses_views') || PerUser('successtories_views') || PerUser('abuse')|| PerUser('blocked_users'))
    <li class="nav-item
    @if(in_array(Route::currentRouteName(),['contactus','contactus_view','session_courses_views','session_courses_views_add','session_courses_views_edit',
        'session_successtories_views','session_successtories_views_add','session_successtories_views_edit',
        'webinars_views','webinars_views_add','webinars_views_edit',
        'session_diplomas_views','session_diplomas_views_add','session_diplomas_views_edit',
        'session_webinars_views','session_webinars_views_add','session_webinars_views_edit',
        'courses_views','courses_views_add','courses_views_edit',
        'successtories_views','successtories_views_add','successtories_views_edit',
        'abuse','abuse_add','abuse_edit',
        'contactus','contactus_add','contactus_edit',
        'blocked_users'
        ])) open @endif">
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
            <span class="title">{{ Lang::get('main.reports') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu" @if(in_array(Route::currentRouteName(),['contactus','contactus_view','session_courses_views','session_courses_views_add','session_courses_views_edit',
        'session_successtories_views','session_successtories_views_add','session_successtories_views_edit',
        'session_webinars_views','session_webinars_views_add','session_webinars_views_edit',
        'session_diplomas_views','session_diplomas_views_add','session_diplomas_views_edit',
        'webinars_views','webinars_views_add','webinars_views_edit',
        'courses_views','courses_views_add','courses_views_edit',
        'successtories_views','successtories_views_add','successtories_views_edit',
        'abuse','abuse_add','abuse_edit',
        'contactus','contactus_add','contactus_edit',
        'blocked_users'
        ])) style="display: block;" @endif>
            @if(PerUser('contactus'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['contactus','contactus_add','contactus_edit'])) start active open @endif">
                    <a href="{{ URL('admin/contactus') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.contactus') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('abuse'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['abuse','abuse_add','abuse_edit'])) start active open @endif">
                    <a href="{{ URL('admin/abuse') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.abuse') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('blocked_users'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['blocked_users'])) start active open @endif">
                    <a href="{{ URL('admin/blocked_users') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.blocked_users') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('session_courses_views'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['session_courses_views','session_courses_views_add','session_courses_views_edit'])) start active open @endif">
                    <a href="{{ URL('admin/session_courses_views') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.session_courses_views') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('session_successtories_views'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['session_successtories_views','session_successtories_views_add','session_successtories_views_edit'])) start active open @endif">
                    <a href="{{ URL('admin/session_successtories_views') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.session_successtories_views') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('session_webinars_views'))
                <li class="nav-item @if(in_array(Route::currentRouteName(),['session_webinars_views','session_webinars_views_add','session_webinars_views_edit'])) start active open @endif">
                    <a href="{{ URL('admin/session_webinars_views') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.session_webinars_views') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('session_diplomas_views'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['session_diplomas_views','session_diplomas_views_add','session_diplomas_views_edit'])) start active open @endif">
                    <a href="{{ URL('admin/session_diplomas_views') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.session_diplomas_views') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('courses_views'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['courses_views','courses_views_add','courses_views_edit'])) start active open @endif">
                    <a href="{{ URL('admin/courses_views') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.courses_views') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('successtories_views'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['successtories_views','successtories_views_add','successtories_views_edit'])) start active open @endif">
                    <a href="{{ URL('admin/successtories_views') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.successtories_views') }}</span>
                    </a>
                </li>
            @endif
            @if(PerUser('webinars_views'))
                <li class="nav-item  @if(in_array(Route::currentRouteName(),['webinars_views','webinars_views_add','webinars_views_edit'])) start active open @endif">
                    <a href="{{ URL('admin/webinars_views') }}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">{{ Lang::get('main.webinars_views') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif
    @if(PerUser('rating') || PerUser('feedback') )
        <li class="nav-item @if(in_array(Route::currentRouteName(),['feedback','feedback_edit',
    'rating'])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.feedback') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu"
                @if(in_array(Route::currentRouteName(),['feedback','feedback_edit',
                'rating'])) style="display: block;" @endif>
                @if(PerUser('feedback'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['feedback','feedback_edit'])) start active open @endif">
                        <a href="{{ URL('admin/feedback') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.feedback_popup') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('rating'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['rating'])) start active open @endif">
                        <a href="{{ URL('admin/rating') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.rating_popup') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(PerUser('medical_charge_transactions') || PerUser('medical_categories') || PerUser('medical_categories_charge_transactions')|| PerUser('medical_sup_categories') || PerUser('demo_medical_logs') || PerUser('medical_charge_transaction_suspend_log'))
        <li class="nav-item
    @if(in_array(Route::currentRouteName(),['medical_charge_transactions','medical_charge_transactions_add','medical_charge_transactions_edit',
        'medical_categories','medical_categories_add','medical_categories_edit',
        'medical_categories_charge_transactions','medical_categories_charge_transactions_add','medical_categories_charge_transactions_edit',
        'demo_medical_logs','demo_medical_logs_add','demo_medical_logs_edit',
        'medical_sup_categories','medical_sup_categories_add','medical_sup_categories_edit',
        'medical_charge_transaction_suspend_log','medical_charge_transaction_suspend_log_add','medical_charge_transaction_suspend_log_edit'
        ])) open @endif">
            <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-users"></i>
                <span class="title">{{ Lang::get('main.medical') }}</span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu" @if(in_array(Route::currentRouteName(),['medical_charge_transactions','medical_charge_transactions_add','medical_charge_transactions_edit',
       'medical_categories','medical_categories_add','medical_categories_edit',
       'medical_categories_charge_transactions','medical_categories_charge_transactions_add','medical_categories_charge_transactions_edit',
       'demo_medical_logs','demo_medical_logs_add','demo_medical_logs_edit',
       'medical_sup_categories','medical_sup_categories_add','medical_sup_categories_edit',
        'medical_charge_transaction_suspend_log','medical_charge_transaction_suspend_log_add','medical_charge_transaction_suspend_log_edit'
        ])) style="display: block;" @endif>
                @if(PerUser('medical_charge_transactions'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['medical_charge_transactions','medical_charge_transactions_add','medical_charge_transactions_edit'])) start active open @endif">
                        <a href="{{ URL('admin/medical_charge_transactions') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.medical_charge_transactions') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('medical_categories'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['medical_categories','medical_categories_add','medical_categories_edit'])) start active open @endif">
                        <a href="{{ URL('admin/medical_categories') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.medical_categories') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('medical_sup_categories'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['medical_sup_categories','medical_sup_categories_add','medical_sup_categories_edit'])) start active open @endif">
                        <a href="{{ URL('admin/medical_sup_categories') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.medical_sup_categories') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('medical_categories_charge_transactions'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['medical_categories_charge_transactions','medical_categories_charge_transactions_add','medical_categories_charge_transactions_edit'])) start active open @endif">
                        <a href="{{ URL('admin/medical_categories_charge_transactions') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.medical_categories_charge_transactions') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('demo_medical_logs'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['demo_medical_logs','demo_medical_logs_add','demo_medical_logs_edit'])) start active open @endif">
                        <a href="{{ URL('admin/demo_medical_logs') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.demo_medical_logs') }}</span>
                        </a>
                    </li>
                @endif
                @if(PerUser('medical_charge_transaction_suspend_log'))
                    <li class="nav-item  @if(in_array(Route::currentRouteName(),['medical_charge_transaction_suspend_log','medical_charge_transaction_suspend_log_add','medical_charge_transaction_suspend_log_edit'])) start active open @endif">
                        <a href="{{ URL('admin/mctsl') }}" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title">{{ Lang::get('main.medical_charge_transaction_suspend_log') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(PerUser('system') || PerUser('app_settings_edit') || PerUser('seo_sitemap_edit') || PerUser('subscription_prices_edit'))
    <li class="nav-item" @if(in_array(Route::currentRouteName(),['system','app_settings_edit','seo_sitemap_edit','subscription_prices_edit'])) open @endif>
        <a href="javascript:;" class="nav-link nav-toggle"> <i class="fa fa-wrench"></i>
            <span class="title">{{ Lang::get('main.settings') }}</span>
            <span class="arrow open"></span>
        </a>
        <ul class="sub-menu"
            @if(in_array(Route::currentRouteName(),['system','app_settings_edit','seo_sitemap_edit','subscription_prices_edit'])) style="display: block;" @endif>
            @if(PerUser('system'))
            <li class="nav-item   @if(in_array(Route::currentRouteName(),['system'])) start active open @endif ">
                <a href="{{ URL('admin/system') }}" class="nav-link ">
                    <i class="icon-settings"></i>
                    <span class="title">{{ Lang::get('main.system') }}</span>
                </a>
            </li>
            @endif
            @if(PerUser('app_settings_edit'))
            <li class="nav-item   @if(in_array(Route::currentRouteName(),['app_settings_edit'])) start active open @endif  ">
                <a href="{{ URL('admin/app_settings') }}" class="nav-link ">
                    <i class="icon-settings"></i>
                    <span class="title">{{ Lang::get('main.app_settings') }}</span>
                </a>
            </li>
             @endif
             @if(PerUser('seo_sitemap_edit'))
            <li class="nav-item   @if(in_array(Route::currentRouteName(),['seo_sitemap_edit'])) start active open @endif  ">
                <a href="{{ URL('admin/seo_sitemap') }}" class="nav-link ">
                    <i class="icon-settings"></i>
                    <span class="title">{{ Lang::get('main.seo_sitemap') }}</span>
                </a>
            </li>
            @endif
            @if(PerUser('subscription_prices_edit'))
                <li class="nav-item   @if(in_array(Route::currentRouteName(),['subscription_prices_edit'])) start active open @endif  ">
                    <a href="{{ URL('admin/subscription_prices') }}" class="nav-link ">
                        <i class="icon-settings"></i>
                        <span class="title">{{ Lang::get('main.subscription_prices') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif


</ul>
