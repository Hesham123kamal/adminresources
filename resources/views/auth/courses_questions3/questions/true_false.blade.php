<li class="col-lg-12 question-object" id="question_{{$question->id}}" data-id="{{$question->id}}">
    <div class="form-group col-lg-5">
        <p class="question" id="question">{{ $question->name }}</p>
        @if($course_question->questions_type=='arabic_and_english')
            <p class="question_en" id="question_en">{{ $question->name_en }}</p>
        @endif
        <input type="hidden" name="type" value="true_false">
        <input type="hidden" name="question_id" value="{{$question->id}}">
    </div>
    <div class="form-group col-lg-2">
        @if($question->image)
            <img class="question_image" style="max-width: 100px;max-height: 80px;" src="{{assetURL('exams_question/'.$question->image)}}" alt="">
        @endif
    </div>
    @if($question->CurriculumQuestionsDetails)
    <div class="col-lg-3 text-center" style="padding: 0;">
        <div class="true">
            <input type="radio" name="answers[{{$question->CurriculumQuestionsDetails->id}}]" @if((isset($question->CurriculumQuestionsDetails->answer) && $question->CurriculumQuestionsDetails->answer == 1)) checked @endif value="1" id="answers_{{ $question->CurriculumQuestionsDetails->id }}_true" disabled>
            <label for="answers_{{ $question->CurriculumQuestionsDetails->id }}_true"><span></span></label>
        </div>
        <div class="false">
            <input type="radio" name="answers[{{$question->CurriculumQuestionsDetails->id}}]" @if((isset($question->CurriculumQuestionsDetails->answer) && $question->CurriculumQuestionsDetails->answer == 0)) checked @endif value="0" id="answers_{{ $question->CurriculumQuestionsDetails->id }}_false" disabled>
            <label for="questions_answers_{{ $question->CurriculumQuestionsDetails->id }}_false"><span></span></label>
        </div>
    </div>
    @else
        {{ 'error please edit this question' }}
    @endif
    <div class="col-lg-2" style="padding: 0;">
        <button class="btn btn-success col-md-6 edit_question" data-type="true_false" style="border-radius: 8px 0 0 8px !important;"><i class="glyphicon glyphicon-edit"></i></button>
        <button class="btn btn-danger col-md-6 remove_question" data-type="true_false" style="border-radius: 0 8px 8px 0 !important;"><i class="glyphicon glyphicon-trash"></i></button>
    </div>
</li>
