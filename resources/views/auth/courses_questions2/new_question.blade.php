@if($type=='true_false')
    <div class="row">
        <div class="col-lg-10">
            <div class="form-group col-lg-8">
                <label for="questions_name_{{$id}}">{{ Lang::get('main.question') }}</label>
                <input type="text" class="form-control" name="questions[name][{{$id}}]" id="questions_name_{{$id}}"  placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions[name_en][{{$id}}]" id="questions_name_{{$id}}"  placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                @endif
            </div>
            <div class="form-group col-lg-2">
                <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                    <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width:auto;" src="{{ assetURL('none.png') }}" alt="" width="100%" /></a>
                    <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                        <i class="glyphicon glyphicon-edit"></i>
                    </button>
                    <a class="remove-img hidden" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                        <i class="glyphicon glyphicon-remove"></i>
                    </a>
                    <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
                </div>
                <input type="file" name="image[{{$id}}]" data-image_id="{{$id}}" onchange="doChangeImage(this)" style="display: none">
            </div>
            <div class="col-lg-2" style="margin-top: 24px;">
                <div class="true">
                    <input type="radio" name="questions[answers][{{$id}}]" checked="checked" value="1" id="questions_answers_{{$id}}_true">
                    <label for="questions_answers_{{$id}}_true">
                        <span></span>
                    </label>
                </div>
                <div class="false">
                    <input type="radio" name="questions[answers][{{$id}}]" value="0" id="questions_answers_{{$id}}_false">
                    <label for="questions_answers_{{$id}}_false">
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
        <button class="btn btn-danger col-lg-2 remove_question" style="margin-top: 24px;">
            <i class="glyphicon glyphicon-trash"></i>
        </button>
    </div>
@elseif($type=='chose_multiple')
    <div class="">
        <div class="form-group col-lg-8">
            <label for="questions_name_{{$id}}">{{ Lang::get('main.question') }}</label>
            <input type="text" class="form-control" name="questions[name][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" name="questions[name_en][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
            @endif
        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width:auto;" src="{{ assetURL('none.png') }}" alt="" width="100%" /></a>
                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i>
                </button>
                <a class="remove-img hidden" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" name="image[{{$id}}]" data-image_id="{{$id}}" onchange="doChangeImage(this)" style="display: none">
        </div>
        <button class="btn btn-danger col-lg-2 remove_question" style="margin-top: 24px;">
            <i class="glyphicon glyphicon-trash"></i>
        </button>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_{{$id}}_1">Choice 1</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_{{$id}}_1" value="1" name="chose_question_answer[{{$id}}][0]">
                    <label for="chose_multiple_{{$id}}_1">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_{{$id}}_1" placeholder="Enter Choice 1">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_multiple_{{$id}}_1" placeholder="Enter English Choice 1">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_{{$id}}_2">Choice 2</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_{{$id}}_2" value="1" name="chose_question_answer[{{$id}}][1]">
                    <label for="chose_multiple_{{$id}}_2">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_{{$id}}_2" placeholder="Enter Choice 2">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_multiple_{{$id}}_2" placeholder="Enter English Choice 2">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_{{$id}}_3">Choice 3</label>
            <div class="input-group"> <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_{{$id}}_3" value="1" name="chose_question_answer[{{$id}}][2]">
                    <label for="chose_multiple_{{$id}}_3">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_{{$id}}_3" placeholder="Enter Choice 3">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_multiple_{{$id}}_3" placeholder="Enter English Choice 3">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_{{$id}}_4">Choice 4</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_{{$id}}_4" value="1" name="chose_question_answer[{{$id}}][3]">
                    <label for="chose_multiple_{{$id}}_4">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_{{$id}}_4" placeholder="Enter Choice 4">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_multiple_{{$id}}_4" placeholder="Enter English Choice 4">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>
@elseif($type=='chose_single')
    <div class="">
        <div class="form-group col-lg-8">
            <label for="questions_name_{{$id}}">{{ Lang::get('main.question') }}</label>
            <input type="text" class="form-control" name="questions[name][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" name="questions[name_en][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
            @endif
        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width:auto;" src="{{ assetURL('none.png') }}" alt="" width="100%" /></a>
                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i>
                </button>
                <a class="remove-img hidden" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" name="image[{{$id}}]" data-image_id="{{$id}}" onchange="doChangeImage(this)" style="display: none">
        </div>
        <button class="btn btn-danger col-lg-2 remove_question" style="margin-top: 24px;">
            <i class="glyphicon glyphicon-trash"></i>
        </button>
        <div class="form-group col-lg-6">
            <label for="chose_single_{{$id}}_1">Choice 1</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_{{$id}}_1" value="1" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_{{$id}}_1">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_{{$id}}_1" placeholder="Enter Choice 1">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_single_{{$id}}_1" placeholder="Enter English Choice 1">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_single_{{$id}}_2">Choice 2</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_{{$id}}_2" value="2" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_{{$id}}_2">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_{{$id}}_2" placeholder="Enter Choice 2">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_single_{{$id}}_2" placeholder="Enter English Choice 2">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_single_{{$id}}_3">Choice 3</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_{{$id}}_3" value="3" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_{{$id}}_3">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_{{$id}}_3" placeholder="Enter Choice 3">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_single_{{$id}}_3" placeholder="Enter English Choice 3">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_single_{{$id}}_4">Choice 4</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_{{$id}}_4" value="4" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_{{$id}}_4">
                        <span></span>
                    </label>
                </span>
                <input type="text" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_{{$id}}_4" placeholder="Enter Choice 4">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions_en[answers][{{$id}}][]" id="chose_single_{{$id}}_4" placeholder="Enter English Choice 4">
                @endif
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>
@elseif($type=='chose_single_with_images')
    <div class="">
        <div class="form-group col-lg-8">
            <label for="questions_name_{{$id}}">{{ Lang::get('main.question') }}</label>
            <input type="text" class="form-control" name="questions[name][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" name="questions[name_en][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
            @endif
        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width:auto;" src="{{ assetURL('none.png') }}" alt="" width="100%" /></a>
                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i>
                </button>
                <a class="remove-img hidden" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" name="image[{{$id}}]" data-image_id="{{$id}}" onchange="doChangeImage(this)" style="display: none">
        </div>
        <button class="btn btn-danger col-lg-2 remove_question" style="margin-top: 24px;">
            <i class="glyphicon glyphicon-trash"></i>
        </button>
        <div class="form-group col-lg-6">
            <label for="chose_single_with_images_{{$id}}_1">Choice 1</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_with_images_{{$id}}_1" value="1" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_with_images_{{$id}}_1">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_with_images_{{$id}}_1" placeholder="Enter Choice 1">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_single_with_images_{{$id}}_2">Choice 2</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_with_images_{{$id}}_2" value="2" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_with_images_{{$id}}_2">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_with_images_{{$id}}_2" placeholder="Enter Choice 2">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_single_with_images_{{$id}}_3">Choice 3</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_with_images_{{$id}}_3" value="3" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_with_images_{{$id}}_3">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_with_images_{{$id}}_3" placeholder="Enter Choice 3">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_single_with_images_{{$id}}_4">Choice 4</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" id="chose_single_with_images_{{$id}}_4" value="4" name="chose_question_answer[{{$id}}]">
                    <label for="chose_single_with_images_{{$id}}_4">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_single_with_images_{{$id}}_4" placeholder="Enter Choice 4">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>
@elseif($type=='chose_multiple_with_images')
    <div class="">
        <div class="form-group col-lg-8">
            <label for="questions_name_{{$id}}">{{ Lang::get('main.question') }}</label>
            <input type="text" class="form-control" name="questions[name][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" name="questions[name_en][{{$id}}]" id="questions_name_{{$id}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
            @endif
        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width:auto;" src="{{ assetURL('none.png') }}" alt="" width="100%" /></a>
                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i>
                </button>
                <a class="remove-img hidden" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" name="image[{{$id}}]" data-image_id="{{$id}}" onchange="doChangeImage(this)" style="display: none">
        </div>
        <button class="btn btn-danger col-lg-2 remove_question" style="margin-top: 24px;">
            <i class="glyphicon glyphicon-trash"></i>
        </button>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_with_images_{{$id}}_1">Choice 1</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_with_images_{{$id}}_1" value="1" name="chose_question_answer[{{$id}}][0]">
                    <label for="chose_multiple_with_images_{{$id}}_1">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_with_images_{{$id}}_1" placeholder="Enter Choice 1">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_with_images_{{$id}}_2">Choice 2</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_with_images_{{$id}}_2" value="1" name="chose_question_answer[{{$id}}][1]">
                    <label for="chose_multiple_with_images_{{$id}}_2">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_with_images_{{$id}}_2" placeholder="Enter Choice 2">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_with_images_{{$id}}_3">Choice 3</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_with_images_{{$id}}_3" value="1" name="chose_question_answer[{{$id}}][2]">
                    <label for="chose_multiple_with_images_{{$id}}_3">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_with_images_{{$id}}_3" placeholder="Enter Choice 3">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <label for="chose_multiple_with_images_{{$id}}_4">Choice 4</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_with_images_{{$id}}_4" value="1" name="chose_question_answer[{{$id}}][3]">
                    <label for="chose_multiple_with_images_{{$id}}_4">
                        <span></span>
                    </label>
                </span>
                <input type="file" class="form-control" name="questions[answers][{{$id}}][]" id="chose_multiple_with_images_{{$id}}_4" placeholder="Enter Choice 4">
                <input type="hidden" class="form-control" name="questions[answers_e][{{$id}}][]">
                <span class="input-group-addon ">
                    <a href="#" class="removeAnswer">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>
@endif