<li class="col-lg-12 question-object" id="question_{{$question->id}}" data-id="{{$question->id}}">
    <div class="col-md-12">
        <div class="form-group col-lg-6">
            <p class="question">{{ $question->name }}</p>
            @if($course_question->questions_type=='arabic_and_english')
                <p class="question_en">{{ $question->name_en }}</p>
            @endif
            <input type="hidden" name="type" value="chose_multiple_with_images">
            <input type="hidden" name="question_id" value="{{$question->id}}">
        </div>
        <div class="form-group col-lg-4">
            @if($question->image)
                <img class="question_image" style="height: 80px;" src="{{ assetURL('exams_question/'.$question->image) }}" alt="">
            @endif
        </div>
        <div class="col-lg-2" style="padding: 0;">
            <button class="btn btn-success col-lg-6 edit_question" data-type="chose_multiple_with_images" style="border-radius: 8px 0 0 8px !important;"><i class="glyphicon glyphicon-edit"></i>
            </button>
            <button class="btn btn-danger col-lg-6 remove_question" data-type="chose_multiple_with_images" style="border-radius: 0 8px 8px 0 !important;"><i class="glyphicon glyphicon-trash"></i>
            </button>
        </div>
    </div>
    @foreach($question->CurriculumQuestionsDetails as $key => $detail)
        <div class="form-group col-lg-6">
            <label for="chose_multiple[{{$detail->order+1}}]">{{ Lang::get('main.choice') . ' ' . ($key+1)}}
            </label>
            <div class="input-group">
            <span class="input-group-addon chose">
            <input type="checkbox" class="choices_values" id="chose_multiple[{{$detail->id}}]" value="{{$detail->id}}" name="chose_multiple[{{$detail->id}}]" @if($detail->answer)  checked @endif disabled>
            <label for="chose_multiple[{{$detail->id}}]"><span></span></label>
            </span>
                <img class="choices_name" style="width: 100;height: 80px;" src="{{assetURL($detail->image) }}">
                <input type="hidden" class="form-control" name="answers_images[{{$detail->id}}]" value="{{ $detail->image }}" id="chose_multiple[{{$detail->id}}]" placeholder="{{Lang::get('main.enter').Lang::get('main.choice_')}}" disabled>
            </div>
        </div>
    @endforeach
</li>
