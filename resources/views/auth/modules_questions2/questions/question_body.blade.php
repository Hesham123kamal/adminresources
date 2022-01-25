@if(count($modules_questions))
    <ol style="font-size: 18px;">
        @foreach($modules_questions as $question)
            @include('auth.modules_questions2.questions.'.$question->type, ['question' => $question])
        @endforeach
    </ol>
@endif
