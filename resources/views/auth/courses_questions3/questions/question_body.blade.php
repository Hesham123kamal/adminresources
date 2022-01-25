@if(count($courses_questions))
    <ol style="font-size: 20px;">
        @foreach($courses_questions as $question)
            @include('auth.courses_questions3.questions.'.$question->type, ['question' => $question])
        @endforeach
    </ol>
@endif
