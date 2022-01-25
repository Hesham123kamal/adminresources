@if(count($modules_trainings_questions))
    <ol style="font-size: 18px;">
        @foreach($modules_trainings_questions as $question)
            @include('auth.modules_trainings_questions2.questions.'.$question->type, ['question' => $question])
        @endforeach
    </ol>
@endif
