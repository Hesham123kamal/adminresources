
{{--{{ print_r(json_decode($post->custom_views_projects)).dd() }}--}}
@extends('auth.layouts.app')
@section('pageTitle')
    <title>{{ Lang::get('main.home_page_title') }}</title>
@endsection
@section('contentHeader')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL('/admin') }}">{{ Lang::get('main.dashboard') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL('/admin/initiative_articles') }}">{{ Lang::get('main.initiative_articles') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $article->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.initiative_articles') }}
        <small>{{ Lang::get('main.edit') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
<style>
    .form-group{
        margin-left: 0px !important;
        margin-right: 0px !important;
    }
</style>
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.initiative_articles') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/initiative_articles/'.$article->id,'class'=>"form-horizontal",'files'=>true]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                </div>

                <div class="form-group col-lg-9">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$article->name}}" id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input @if($article->published=="yes") checked @endif type="checkbox" class="make-switch" name="published" data-size="small"
                           data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                           data-off-text="{{ Lang::get('main.unpublished') }}">
                </div>

                <div class="col-lg-12 form-group">
                    <label class="control-label" for="tag">{{ Lang::get('main.tags') }}</label>
                    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-tag">
                        {{ Lang::get('main.select_all')}}
                    </button>
                    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-tag">
                        {{ Lang::get('main.deselect_all')}}
                    </button>
                    {!! Form::select('tag[]', $tags, old('tag') ? old('tag') : $article->tag->pluck('id')->toArray(), ['class' => 'form-control form-filter select2', 'multiple' => 'multiple', 'id' => 'selectall-tag']) !!}
                    <p class="help-block"></p>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="meta_description">{{ Lang::get('main.meta_description') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$article->meta_description}}" id="meta_description" name="meta_description"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.meta_description') }}">
                    </div>
                </div>

                <div class="form-group col-lg-6">
                    <label for="section">{{ Lang::get('main.section') }}</label>
                    <select name="section" class="module_name select2 form-control form-filter"
                            id="section">
                        <option value=" ">{{Lang::get('main.select')}}{{Lang::get('main.section')}}</option>
                        @foreach($sections as $id=>$title)
                            <option @if($id==$article_section)  selected="selected" @endif value="{{$id}}">{{$title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label for="author">{{ Lang::get('main.author') }}</label>
                    <select name="author" class="module_name select2 form-control form-filter"
                            id="author">
                        <option value=" ">{{Lang::get('main.select')}}{{Lang::get('main.author')}}</option>
                        @foreach($authors as $author)
                            <option @if($author->id==$article->author_id)  selected="selected" @endif value="{{$author->id}}">{{$author->name}}</option>
                        @endforeach
                    </select>
                </div>

                @include('auth/description',['posts' =>[$article->description]])

                <div class="form-group col-lg-12">
                    <label class="control-label">{{Lang::get('main.pic')}}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img width="20%" src="{{assetURL($article->picpath) }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="picture">{{Lang::get('main.replace')}} {{Lang::get('main.pic')}}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" accept="image/*"
                               name="picture" placeholder="{{ Lang::get('main.enter').Lang::get('main.pic') }}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label for="author">{{ Lang::get('main.article_date') }}</label>
                    <div class="input-group date fromToDate margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control form-filter input-sm"
                               name="article_date" value="{{$article->article_date}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.article_date') }}">
                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="public_title">{{ Lang::get('main.public_title') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$article->public_title}}" id="public_title" name="public_title" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.public_title') }}">
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="url">{{ Lang::get('main.url') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$article->url}}" id="url" name="url" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="text-center col-lg-12">
                    <button type="submit" class="btn green">{{ Lang::get('main.save') }}</button>
                </div>
                <div class="clearfix"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function () {
            $('.fromToDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                orientation:"bottom"
            });
        });

        $("#selectbtn-tag").click(function(){
            $("#selectall-tag > option").prop("selected","selected");
            $("#selectall-tag").trigger("change");
        });
        $("#deselectbtn-tag").click(function(){
            $("#selectall-tag > option").prop("selected","");
            $("#selectall-tag").trigger("change");
        });

        $(document).ready(function () {
            $('.select2').select2();
        });

    </script>
@endsection