<li class="question_body col-md-12" style="padding: 0;" data-id="{{$question->id}}">
    <input type="hidden" name="question_id" value="{{$question->id}}">
    <div class="col-md-6">
        <p style="margin-bottom: 0;">{{ $question->name_ar }}</p>
        <p style="margin-bottom: 0;">{{ $question->name_en }}</p>
    </div>
    <div class="col-lg-2">
        <span class="difficulty" style="padding: 5px 10px;border: 1px solid #D3D3D3;font-size: 14px;">{{ $question->difficulty_type }}</span>
    </div>
    <div class="col-md-2 text-center" style="padding:0;">
        <div class="true">
            <input type="radio" name="answers_{{ $question->id }}" @if($question->ModulesQuestionsDetails->answer == 1) checked="checked" @endif value="1" id="answers_{{ $question->id }}_true" disabled>
            <label for="answers_{{ $question->id }}_true">
                <span></span>
            </label>
        </div>
        <div class="false">
            <input type="radio" name="answers_{{ $question->id }}" @if($question->ModulesQuestionsDetails->answer == 0) checked="checked" @endif value="0" id="answers_{{ $question->id }}_false" disabled>
            <label for="answers_{{ $question->id }}_false">
                <span></span>
            </label>
        </div>
    </div>
    <div class="col-md-2 row" style="padding: 0;">
        <button class="btn btn-success col-md-6 edit_question" data-type="true_false" style="border-radius: 8px 0 0 8px !important;">
            <i class="glyphicon glyphicon-edit"></i>
        </button>
        <button class="btn btn-danger col-md-6 remove_question" data-type="true_false" style="border-radius: 0 8px 8px 0 !important;">
            <i class="glyphicon glyphicon-trash"></i>
        </button>
    </div>
</li>
