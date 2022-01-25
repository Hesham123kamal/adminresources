<li class="question_body col-md-12" style="padding: 0;" data-id="{{$question->id}}">
    <input type="hidden" name="question_id" value="{{$question->id}}">
    <div class="col-md-12" style="padding: 0;">
        <div class="col-md-6">
            <p style="margin-bottom: 0;">{{ $question->name_ar }}</p>
            <p style="margin-bottom: 0;">{{ $question->name_en }}</p>
        </div>
        <div class="col-md-2">
            <span class="difficulty" style="padding: 5px 20px;border: 1px solid #D3D3D3;">{{ $question->difficulty_type }}</span>
        </div>
        <div class="col-md-2 col-md-offset-2 row" style="padding: 0;">
            <button class="btn btn-success col-md-6 edit_question" data-type="chose_single" style="border-radius: 8px 0 0 8px !important;">
                <i class="glyphicon glyphicon-edit"></i>
            </button>
            <button class="btn btn-danger col-md-6 remove_question" data-type="chose_single" style="border-radius: 0 8px 8px 0 !important;">
                <i class="glyphicon glyphicon-trash"></i>
            </button>
        </div>
    </div>
    <div class="col-md-12" style="margin: 0;padding: 0;">
    @foreach($question->ModulesQuestionsDetails as $key => $detail)
            <div class="form-group col-lg-6">
                <label for="chose_single_{{ $detail->id }}">{{ Lang::get('main.choice').' '.($key+1) }}
                </label>
                <div class="input-group">
                    <span class="input-group-addon chose">
                        <input type="radio" class="choices_values" id="chose_single_{{ $detail->id }}" @if($detail->answer) checked="checked" @endif value="{{ $detail->id }}" name="chose_single[{{ $detail->id }}]" disabled>
                        <label for="chose_single_{{ $detail->id }}"><span></span></label>
                    </span>

                    <input type="text" class="form-control choices_ar" name="answers_text[{{ $detail->id }}]" value="{{$detail->name_ar}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} {{ $key+1 }}" disabled>
                    <input type="text" class="form-control choices_en" name="answers_text_en[{{ $detail->id }}]" value="{{ $detail->name_en }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} {{ $key+1 }}" disabled>
                </div>
            </div>
    @endforeach
    </div>
</li>
