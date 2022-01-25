<div class="question_body">
    <div class="row" style="margin: 0;">
        <div class="form-group col-md-6">
            <label for="question_ar">{{ Lang::get('main.question') }}</label>
            <input type="text" class="form-control" name="question_ar" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
            <input type="text" class="form-control" name="question_en" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
        </div>
        <div class="col-md-3">
            <label for="difficulty_type">{{ Lang::get('main.difficulty_type') }}</label>
            <select class="sel2" id="difficulty_type" name="difficulty" style="width:100%">
                <option value="easy">{{ Lang::get('main.easy') }}</option>
                <option value="normal">{{ Lang::get('main.normal') }}</option>
                <option value="hard">{{ Lang::get('main.hard') }}</option>
            </select>
        </div>
        <button class="btn btn-danger col-lg-1 col-md-offset-2 remove_question" data-type="'+type+'" style="margin-top: 20px;height: 40px;width: 44px;border-radius: 50% !important;font-size: 18px;"><i class="glyphicon glyphicon-remove"></i></button>
    </div>
    @for($i=0; $i < 4; $i++)
        <div class="form-group col-lg-6">
            <label for="chose_multiple_{{$i}}">{{ Lang::get('main.choice').' '.($i+1)}}</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="checkbox" id="chose_multiple_{{$i}}" value="{{$i}}" name="chose_multiple[{{$i}}]">
                    <label for="chose_multiple_{{$i}}"><span></span></label>
                </span>
                <input type="text" class="form-control" name="answers_text[{{$i}}]" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar').' '.($i+1) }}">
                <input type="text" class="form-control" name="answers_text_en[{{$i}}]" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' '.($i+1) }}">
                <span class="input-group-addon">
                    <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a>
                </span>
            </div>
        </div>
    @endfor
</div>
