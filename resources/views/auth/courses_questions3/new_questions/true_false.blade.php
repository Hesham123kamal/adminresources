<div class="question-container row">
    <div class="form-group col-lg-6">
        <label for="question">{{ Lang::get('main.question') }}</label>
        <input type="text" class="form-control" name="question" id="question" placeholder="{{ Lang::get('main.enter') }} {{ Lang::get('main.question') }}">
        @if($course_question->questions_type == 'arabic_and_english')
            <input type="text" class="form-control" name="questions_en" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
        @endif
    </div>
    <div class="form-group col-lg-3">
        <input type="file" name="image" style="width: 100%;margin-top: 30px;">
    </div>
    <div class="col-lg-2" style="margin-top: 24px;padding: 0">
        <div class="true"><input type="radio" name="answers" checked="checked" value="1" id="answers_true"><label for="answers_true"><span></span></label></div>
        <div class="false"><input type="radio" name="answers" value="0" id="answers_false"><label for="answers_false"><span></span></label></div>
    </div>
    <button class="btn btn-danger col-lg-1 remove_question" data-type="true_false" style="margin-top: 24px;height: 40px;
   width: 43px;border-radius: 50% !important;font-size: 18px"><i class="glyphicon glyphicon-remove"></i></button>
</div>
