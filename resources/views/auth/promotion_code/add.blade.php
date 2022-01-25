<?php
/**
 * Created by PhpStorm.
 * User: Mohammed.Hamza
 * Date: 18-Dec-18
 * Time: 02:54 PM
 */
?>
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
                <a href="{{ URL('/admin/promotion_code') }}">{{ Lang::get('main.promotion_code') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.promotion_code') }}
        <small>{{ Lang::get('main.add') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    <style>
        .form-group {
            margin-left: 0px !important;
            margin-right: 0px !important;
        }
        #generate_promo_code{
            margin-top:25px;
        }
    </style>
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.promotion_code') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['class'=>"form-horizontal",'id'=>"addPromotionForm"]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div id="messages"></div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="type" for="type">{{ Lang::get('main.type') }} <span
                                    class="required"> * </span></label>
                        <select name="type" class=" form-control form-filter" id="type">
                            <option value="">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>
                            <option @if(old('type')=='diplomas') selected @endif value="diplomas">Diplomas</option>
                            <option @if(old('type')=='life_time') selected @endif value="life_time">Life Time</option>
                            <option @if(old('type')=='mba') selected @endif value="mba">MBA</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-9">
                        <label class="control-label" for="promotion_code">{{ Lang::get('main.promotion_code') }}  <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('promotion_code')}}" id="promotion_code" name="promotion_code" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.promotion_code') }}">
                        </div>
                    </div>
                    <div class="text-center col-lg-3">
                        <button type="button" class="btn blue" id="generate_promo_code">{{ Lang::get('main.generate') }}</button>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="price">{{ Lang::get('main.price') }} </label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input disabled="disabled" type="text" class="form-control" value="" id="price" name="price">
                        </div>
                    </div>
                    <div class="form-group col-lg-9">
                        <label class="control-label" for="price_after_discount">{{ Lang::get('main.price_after_discount') }} </label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('price_after_discount')}}" id="price_after_discount" name="price_after_discount">
                        </div>
                    </div>
                    <div class="form-group col-lg-3">
                        <label class="control-label" for="discount">{{ Lang::get('main.discount') }}  <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text"  min="1" max="60" class="form-control" value="{{old('discount')}}" id="discount" name="discount">
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="text-center col-lg-12">
                        <button type="submit" id="submitPromo" class="btn green">{{ Lang::get('main.add') }}</button>
                    </div>
                </div>


                <div class="clearfix"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('scriptCode')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <script>
        var available_discount=60;
        $(document).ready(function(){

            var token = "{{ csrf_token() }}";
            $('#type').change(function(){
                $.ajax({
                    type: "POST",
                    url: "{{ URL('admin/promotion_code/getPriceByType') }}",
                    data: {"type": $(this).val(),"_token": token},
                    success: function(data){
                        $('#price').val(data);
                    }
                });
            })

            $('#generate_promo_code').click(function(){
                $('#generate_promo_code').html('Loading..');
                $.ajax({
                    type: "POST",
                    url: "{{ URL('admin/promotion_code/generateCode') }}",
                    data: {"_token": token},
                    success: function(data){
                        $('#promotion_code').val(data);
                        $('#generate_promo_code').html("{{ Lang::get('main.generate') }}");
                        $('#promotion_code-error').parent().removeClass('has-error');
                        $('#promotion_code-error').remove();

                    }
                });
            })

            $('#discount').change(function(){
                if( $('#price').val() && !isNaN($('#discount').val())){
                    if($('#discount').val() > window.available_discount ){
                        $('#discount').val(window.available_discount);
                    }
                    if($('#discount').val() <=0 ){
                        $('#discount').val('1');
                    }
                    var price=$('#price').val();
                    $('#price_after_discount').val(price-(price*($('#discount').val()/100)));
                }
                $('#discount').parent().removeClass('has-error');
                $('#discount-error').remove();
            })

            $('#price_after_discount').change(function(){
                var price_after_discount= $('#price_after_discount').val();
                if(!isNaN(price_after_discount)) {
                    var price = $('#price').val();
                    var discount = 100 - (price_after_discount * 100) / price;
                    if (discount > window.available_discount || discount <= 0) {
                        console.log(window.available_discount, discount);

                        $('#discount').val(window.available_discount);
                        $('#discount').change();
                    } else {

                        $('#discount').val(discount);
                    }
                    $('#discount').parent().removeClass('has-error');
                    $('#discount-error').remove();

                }
            })


            function CopyToClipboard(containerid) {
                if (document.selection) {
                    var range = document.body.createTextRange();
                    range.moveToElementText(document.getElementById(containerid));
                    range.select().createTextRange();
                    document.execCommand("Copy");
                } else if (window.getSelection) {
                    var range = document.createRange();
                    range.selectNode(document.getElementById(containerid));
                    window.getSelection().addRange(range);
                    document.execCommand("Copy");
                }
            }

            new ClipboardJS('#copyCode');


            $.validator.addMethod(
                    /* The value you can use inside the email object in the validator. */
                "regex",

                    /* The function that tests a given string against a given regEx. */
                function(value, element, regexp)  {
                    /* Check if the value is truthy (avoid null.constructor) & if it's not a RegEx. (Edited: regex --> regexp)*/

                    if (regexp && regexp.constructor != RegExp) {
                        /* Create a new regular expression using the regex argument. */
                        regexp = new RegExp(regexp);
                    }

                    /* Check whether the argument is global and, if so set its last index to 0. */
                    else if (regexp.global) regexp.lastIndex = 0;

                    /* Return whether the element is optional or the result of the validation. */
                    return this.optional(element) || regexp.test(value);
                }
            );
            var formGroup = $('.form-group');
            $('#addPromotionForm').validate({
                rules: {
                    type: {
                        required: true
                    },
                    promotion_code: {
                        required: true,
                        minlength: 6
                    },
                    discount: {
                        required: true,
                        number: true,
                        min: 1,
                        max: 60
                    },
                },
                messages:{
                },
                highlight: function (element) {
                    $(element).parent().removeClass('control-label').addClass('has-error');
                },
                success: function (element) {
                    console.log(element);
                    element.addClass('valid');
//                    $('.form-control').removeClass('has-error');
                    formGroup.removeClass('has-error');
                    formGroup.parent().removeClass('has-error').addClass('has-success');
                    $('#'+$(element).attr('id')).remove();
                },
                submitHandler: function () {
                    $("#submitPromo").html('Adding..');
                    $("#submitPromo").attr('disabled', 'disabled');
                    $.ajax({
                        type: 'post',
                        url: '{{ URL('admin/promotion_code') }}',
                        data: $('#addPromotionForm').serialize(),
                        success: function (data) {
                            $("#messages").html(data.message);
                            $("#submitPromo").removeAttr('disabled');
                            $("#submitPromo").html('Add');
                            if (data.success) {
                                $("#addPromotionForm")[0].reset();
                            }

                            $('form-control').removeClass('has-error');
                            formGroup.removeClass('has-error');
                            formGroup.parent().removeClass('has-error').addClass('has-success');
                            $('#'+$(element).attr('id')).remove();
                        }
                    });
                }
            });

        })

    </script>
@endsection

