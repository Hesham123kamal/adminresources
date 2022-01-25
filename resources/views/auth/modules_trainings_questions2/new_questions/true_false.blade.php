<div class="question_body">
    <div class="form-group col-md-6">
        <label for="question_ar">{{ Lang::get('main.question') }}</label>
        <input type="text" class="form-control" name="question_ar" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
        <input type="text" class="form-control" name="question_en" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
        <input type="hidden" name="type" value="true_false">
    </div>
    <div class="col-lg-2">
        <label for="difficulty_type">{{ Lang::get('main.difficulty_type') }}</label>
        <select style="width:100%" class="sel2" id="difficulty_type" name="difficulty">
            <option value="easy">{{ Lang::get('main.easy') }}</option>
            <option value="normal">{{ Lang::get('main.normal') }}</option>
            <option value="hard">{{ Lang::get('main.hard') }}</option>
        </select>
    </div>
    <div class="col-md-3 text-center">
        <div class="true">
            <input type="radio" id="answers_true" checked name="answers" value="1">
            <label for="answers_true">
                <span></span>
            </label>
        </div>
        <div class="false">
            <input type="radio" id="answers_false" name="answers" value="0">
            <label for="answers_false">
                <span></span>
            </label>
        </div>
    </div>
    <span class="btn btn-danger col-lg-1 remove_question" data-type="'+type+'" style="height: 40px;width: 44px;border-radius: 50% !important;font-size: 18px;"><i class="glyphicon glyphicon-remove"></i></span>
</div>
