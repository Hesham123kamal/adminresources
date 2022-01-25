<div class="question-container">
    <div class="form-group col-lg-7">
        <label for="question">{{ Lang::get('main.question') }}</label>
        <input type="text" class="form-control" name="question" placeholder="{{ Lang::get('main.enter') }} {{ Lang::get('main.question') }}">
        @if($course_question->questions_type == 'arabic_and_english')
            <input type="text" class="form-control" name="questions_en" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
        @endif
    </div>
    <div class="form-group col-lg-3">
        <input type="file" style="width: 100%;margin-top: 30px;" name="image">
    </div>
    <button class="btn btn-danger col-lg-offset-1 remove_question" data-type="chose_single" style="margin-top: 24px;height: 40px;
    width: 44px;border-radius: 50% !important;font-size: 18px;">
        <i class="glyphicon glyphicon-remove"></i>
    </button>
    @for($i = 0; $i < 4; $i++)
    <div class="form-group col-lg-6">
        <label for="chose_single_{{$i}}">{{ Lang::get('main.choice') }} {{$i+1}}</label>
        <div class="input-group">
            <span class="input-group-addon chose">
                <input type="radio" id="chose_single_{{$i}}" value="{{$i}}" name="chose_single">
                <label for="chose_single_{{$i}}"><span></span></label>
            </span>
            <input type="text" class="form-control" name="answers_text[{{$i}}]" placeholder="{{ Lang::get('main.enter') }} {{ Lang::get('main.choice') }} {{$i+1}}">
            @if($course_question->questions_type == 'arabic_and_english')
                <input type="text" class="form-control" name="answers_text_en[{{$i}}]" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en'). $i+1 }}">
            @endif
            <span class="input-group-addon">
                <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a>
            </span>
        </div>
    </div>
    @endfor
</div>
