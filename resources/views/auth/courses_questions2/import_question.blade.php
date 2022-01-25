@if($question->type=='true_false')
    <div class="row">
        <div class="col-lg-10">
            <div class="form-group col-lg-8">
                <label for="questions_name_{{$question->id}}">Question</label>
                <input type="text" class="form-control" name="questions[name][{{$question->id}}]"
                       value="{{$question->name}}" id="questions_name_{{$question->id}}"
                       placeholder="Enter Question">
                @if($questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                           value="{{$question->name_en}}" id="questions_name_{{$question->id}}"
                           placeholder="Enter English Question">
                @endif
            </div>
            <div class="form-group col-lg-2">
                <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                    <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 35px; width:auto;" src="@if($question->image){{assetURL('exams_question/'.$question->image)}}@else {{assetURL('none.png')}} @endif"
                                                                    alt="" width="100%"/></a> <button class="btn btn-primary" title="Change"
                                                  onclick="javascript:changeImage(this)" type="button"  type="button"
                                                  style="width:100%; padding:0px;"><i
                                class="glyphicon glyphicon-edit"></i> </button> <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove"
                                                                              onClick="javascript:removeFile(this)"
                                                                              style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                        <i class="glyphicon glyphicon-remove"></i></a>
                    <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw"
                       style="position: absolute;left: 25%;top: 35%;display: none"></i>
                </div>
                <input type="file" data-image_id="{{$question->id}}" onchange="doChangeImage(this)" name="image[{{$question->id}}]"
                       style="display: none">

            </div>

            <div class="col-lg-2" style="margin-top: 24px;">
                <div class="true">
                    <input type="radio" name="questions[answers][{{$question->id}}]" value="1"
                           {{ $details->answer == 1 ? 'checked="checked"' : ''}} id="questions_answers_{{$question->id}}_true">
                    <label for="questions_answers_{{$question->id}}_true"><span></span></label>
                </div>
                <div class="false">
                    <input type="radio" name="questions[answers][{{$question->id}}]" value="0"
                           {{$details->answer == 0 ? 'checked="checked"' : ''}}  id="questions_answers_{{$question->id}}_false">
                    <label for="questions_answers_{{$question->id}}_false"><span></span></label>
                </div>
            </div>
        </div>
        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{$question->id}}"
                data-type="{{$question->type}}" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
        </button>
    </div>
@elseif($question->type=='chose_multiple')
    <div class="">
        <div class="form-group col-lg-8"><label for="questions_name_{{$question->id}}">Question</label> <input
                    type="text" class="form-control" value="{{$question->name}}"
                    name="questions[name][{{$question->id}}]" id="questions_name_{{$question->id}}"
                    placeholder="Enter Question">

            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" value="{{$question->name_en}}"
                        name="questions[name_en][{{$question->id}}]" id="questions_name_{{$question->id}}"
                        placeholder="Enter English Question">
            @endif

        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 35px; width:auto;" src="@if($question->image){{assetURL('exams_question/'.$question->image)}}@else {{assetURL('none.png')}} @endif"
                                                                alt="" width="100%"/></a>
                        <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i> </button>
                <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove" onClick="javascript:removeFile(this)"
                   style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i></a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw"
                   style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" data-image_id="{{$question->id}}" onchange="doChangeImage(this)" name="image[{{$question->id}}]"
                   style="display: none">

        </div>
        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{$question->id}}" style="margin-top: 24px;">
            <i class="glyphicon glyphicon-trash"></i>
        </button>
        @if(isset($details))
            @foreach($details as $i=>$value)
                <div class="form-group col-lg-6"><label
                            for="chose_multiple_{{$question->id}}_{{$i+1}}">Choice {{$i+1}}</label>
                    <div class="input-group"><span class="input-group-addon chose"> <input type="checkbox"
                                                                                           id="chose_multiple_{{$question->id}}_{{$i+1}}"
                                                                                           value="1"
                                                                                           {{$value->answer == 1 ? 'checked="checked"' : '' }}  name="chose_question_answer[{{$question->id}}][{{$i}}]"> <label
                                    for="chose_multiple_{{$question->id}}_{{$i+1}}"> <span></span> </label> </span>
                        <input type="text" class="form-control" name="questions[answers][{{$question->id}}][{{$i}}]"
                               value="{{$value->name}}"
                               id="chose_multiple_{{$question->id}}_{{$i+1}}"
                               placeholder="Enter Choice {{$i+1}}">
                        @if($questions_type=='arabic_and_english')
                            <input type="text" class="form-control" name="questions_en[answers][{{$question->id}}][{{$i}}]"
                                   value="{{$value->name_en}}"
                                   id="chose_multiple_{{$question->id}}_{{$i+1}}"
                                   placeholder="Enter English Choice {{$i+1}}">
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@elseif($question->type=='chose_single')
    <div>
        <div class="form-group col-lg-8"><label for="questions_name_{{$question->id}}">Question</label><input
                    type="text" class="form-control" name="questions[name][{{$question->id}}]"
                    value="{{$question->name}}" id="questions_name_{{$question->id}}"
                    placeholder="Enter Question">
            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                        value="{{$question->name_en}}" id="questions_name_{{$question->id}}"
                        placeholder="Enter English Question">
            @endif
        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"> <img style="max-height: 35px; width:auto;" src="@if($question->image){{assetURL('exams_question/'.$question->image)}}@else {{assetURL('none.png')}} @endif"
                                                                 alt="" width="100%"/></a>
                        <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i> </button> <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove"
                                                                                onClick="javascript:removeFile(this)"
                                                                                style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i></a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw"
                   style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" data-image_id="{{$question->id}}" onchange="doChangeImage(this)" name="image[{{$question->id}}]"
                   style="display: none">

        </div>
        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{$question->id}}"
                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
        </button>
        @if(isset($details))
            @foreach($details as $i=>$value)
                <div class="form-group col-lg-6"><label for="chose_single_{{$question->id}}_{{$i+1}}">Choice
                        {{$i+1}}</label>
                    <div class="input-group"><span class="input-group-addon chose"><input type="radio"
                                                                                          id="chose_single_{{$question->id}}_{{$i+1}}"
                                                                                          value="{{$i+1}}"
                                                                                          {{$value->answer == 1 ? 'checked="checked"' : ''}}  name="chose_question_answer[{{$question->id}}]"><label
                                    for="chose_single_{{$question->id}}_{{$i+1}}"><span></span></label></span><input
                                type="text" class="form-control" name="questions[answers][{{$question->id}}][]"
                                value="{{$value->name}}" id="chose_single_{{$question->id}}_{{$i+1}}"
                                placeholder="Enter Choice {{$i+1}}">
                        @if($questions_type=='arabic_and_english')
                            <input type="text" class="form-control" name="questions_en[answers][{{$question->id}}][]"
                                    value="{{$value->name_en}}" id="chose_single_{{$question->id}}_{{$i+1}}"
                                    placeholder="Enter English Choice {{$i+1}}">
                        @endif
                        <span class="input-group-addon "> <a href="#"
                                                                                                         class="removeAnswer"><i
                                        class="glyphicon glyphicon-trash"></i></a></span></div>
                </div>
            @endforeach
        @endif

    </div>
@elseif($question->type=='chose_single_with_images')
    <div>
        <div class="form-group col-lg-8"><label for="questions_name_{{$question->id}}">Question</label><input
                    type="text" class="form-control" name="questions[name][{{$question->id}}]"
                    value="{{$question->name}}" id="questions_name_{{$question->id}}"
                    placeholder="Enter Question">
            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                        value="{{$question->name_en}}" id="questions_name_{{$question->id}}"
                        placeholder="Enter English Question">
            @endif
        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 35px; width:auto;" src="@if($question->image){{assetURL('exams_question/'.$question->image)}}@else {{assetURL('none.png')}} @endif"
                                                                alt="" width="100%"/></a>
                        <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i> </button> <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove"
                                                                                onClick="javascript:removeFile(this)"
                                                                                style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i></a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw"
                   style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" data-image_id="{{$question->id}}" onchange="doChangeImage(this)" name="image[{{$question->id}}]"
                   style="display: none">

        </div>
        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{$question->id}}"
                 style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
        </button>
        @if(isset($details))
            @foreach($details as $i=>$value)
                <div class="form-group col-lg-6"><label
                            for="chose_single_with_images_{{$question->id}}_{{$i+1}}">Choice {{$i+1}}
                        </label>
                    <div class="input-group"><span class="input-group-addon chose"><input type="radio"
                                                                                          id="chose_single_with_images_{{$question->id}}_{{$i+1}}"
                                                                                          value="{{$i+1}}"  {{$value->answer == 1 ? 'checked="checked"' : ''}}  name="chose_question_answer[{{$question->id}}]"><label
                                    for="chose_single_with_images_{{$question->id}}_{{$i+1}}"><span></span></label></span><a class="image-link" href="#image-modal" data-toggle="modal"><img
                                    style="max-height:34px; width:auto;" src="{{assetURL($value->image)}}"></a><input type="hidden" class="form-control"
                                                                                 name="questions[images_answers][{{$question->id}}][]"
                                                                                 value="{{$value->image}}"
                                                                                 id="chose_single_with_image_{{$question->id}}_{{$i+1}}">
                        <span
                                class="input-group-addon "> <a href="#" class="removeAnswer"><i
                                        class="glyphicon glyphicon-trash"></i></a></span></div>
                </div>
            @endforeach
        @endif
    </div>
@elseif($question->type=='chose_multiple_with_images')
    <div class="">
        <div class="form-group col-lg-8"><label for="questions_name_{{$question->id}}">Question</label> <input
                    type="text" class="form-control" value="{{$question->name}}"
                    name="questions[name][{{$question->id}}]" id="questions_name_{{$question->id}}"
                    placeholder="Enter Question">
            @if($questions_type=='arabic_and_english')
                <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                       value="{{$question->name_en}}" id="questions_name_{{$question->id}}"
                       placeholder="Enter English Question">
            @endif
        </div>
        <div class="form-group col-lg-2">
            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 35px; width:auto;" src="@if($question->image){{assetURL('exams_question/'.$question->image)}}@else {{assetURL('none.png')}} @endif"
                                                                alt="" width="100%"/></a>
                        <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                    <i class="glyphicon glyphicon-edit"></i> </button> <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove"
                                                                                onClick="javascript:removeFile(this)"
                                                                                style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                    <i class="glyphicon glyphicon-remove"></i></a>
                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw"
                   style="position: absolute;left: 25%;top: 35%;display: none"></i>
            </div>
            <input type="file" data-image_id="{{$question->id}}" onchange="doChangeImage(this)" name="image[{{$question->id}}]"
                   style="display: none">

        </div>
        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{$question->id}}"
                 style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
        </button>
        @if(isset($details))
            @foreach($details as $i=>$value)
                <div class="form-group col-lg-6"><label
                            for="chose_multiple_with_images_{{$question->id}}_{{$i+1}}">Choice
                        {{$i+1}}</label>
                    <div class="input-group"><span class="input-group-addon chose"> <input type="checkbox"
                                                                                           id="chose_multiple_with_images_{{$question->id}}_{{$i+1}}"
                                                                                           value="1"  {{$value->answer === 1 ? 'checked="checked"' : ''}} name="chose_question_answer[{{$question->id}}][{{$i}}]"> <label
                                    for="chose_multiple_with_images_{{$question->id}}_{{$i+1}}"> <span></span> </label> </span>
                        <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height:34px; width:auto;" src="{{assetURL($value->image)}}"></a><input type="hidden" class="form-control"
                                                                                       name="questions[images_answers][{{$question->id}}][]"
                                                                                       value="{{$value->image}}"
                                                                                       id="chose_multiple_with_image_{{$question->id}}_{{$i+1}}">

                    </div>
                </div>
            @endforeach
        @endif
    </div>

@endif