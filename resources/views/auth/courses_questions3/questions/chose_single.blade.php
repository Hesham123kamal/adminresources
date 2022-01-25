<li class="col-lg-12 question-object" id="question_{{$question->id}}" data-id="{{$question->id}}">
    <div class="col-md-12">
        <div class="form-group col-lg-6">
            <p class="question">{{ $question->name }}</p>
            @if($course_question->questions_type=='arabic_and_english')
                <p class="question_en">{{ $question->name_en }}</p>
            @endif
            <input type="hidden" name="type" value="chose_single">
            <input type="hidden" name="question_id" value="{{$question->id}}">
        </div>
        <div class="form-group col-lg-4">
            @if($question->image)
                <img class="question_image" style="height: 80px;" src="{{assetURL('exams_question/'.$question->image)}}" alt="">
            @endif
        </div>
        <div class="col-lg-2" style="padding: 0;">
            <button class="btn btn-success col-lg-6 edit_question" data-type="chose_single" style="border-radius: 8px 0 0 8px !important;"><i class="glyphicon glyphicon-edit"></i>
            </button>
            <button class="btn btn-danger col-lg-6 remove_question" data-type="chose_single" style="border-radius: 0 8px 8px 0 !important;"><i class="glyphicon glyphicon-trash"></i>
            </button>
        </div>
    </div>
    @foreach($question->CurriculumQuestionsDetails as $key => $detail)
        <div class="form-group col-lg-6 single_choice_section">
            <label for="chose_single">{{ Lang::get('main.choice').' '.($key+1)}}</label>
            <div class="input-group">
                <span class="input-group-addon chose">
                    <input type="radio" class="choices_values" name="chose_single_{{$detail->id}}" @if($detail->answer) checked @endif value="{{ $detail->id }}" disabled>
                    <label for="chose_single"><span></span></label>
                </span>
                <input type="text" class="form-control choices_name" name="answers_text[{{$detail->id}}]" value="{{ $detail->name }}" data-order="{{$detail->order+1}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice')}}" disabled>

                @if($course_question->questions_type=='arabic_and_english')
                    <input type="text" class="form-control" name="answers_text_en[{{$detail->id}}]" value="{{$detail->name_en}}" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en')}}" disabled>
                @endif
            </div>
        </div>
    @endforeach
</li>
