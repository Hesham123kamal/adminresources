<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
            عرض اختبارات
            {{ $certificate->diploma_name }}
        للعميل
            {{ $user->FullName }}
        </title>
		<meta charset="UTF-8">
		<meta name=description content="">
		<meta name=viewport content="width=device-width, initial-scale=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Bootstrap CSS -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://www.e3melbusiness.com/assets/css/circle.css">
        <link rel="stylesheet" href="{{ asset('css/view_result.css') }}">
        <link rel="stylesheet" href="{{ asset('css/view_result_print.css') }}" screen="print">
	</head>
	<body>

            @foreach($curriculum_answers as $answer)
            <div class="container" dir="rtl">
            	<div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                        <img src="https://www.e3melbusiness.com/assets/images/logo.png" alt="شعار إعمل بيزنس" title="شعار إعمل بيزنس">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <ol class="breadcrumb">
                            <li>
                                {{ $certificate->diploma_name }}
                            </li>
                            <li class="active">كورس التسويق</li>

                            <li class="active pull-left ">{{ $user->FullName }}</li>
                            <li class="pull-left no-slash">
                                اسم العميل
                            </li>

                        </ol>
                    </div>
                </div>

                @foreach(\App\UsersCurriculumQuestions::where('answer_id',$answer->id)->get() as $question)
                    @if($question->type=='true_false')
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <h3>
                            <span class="label label-info">ضع علامة (√) أمام الاجابة الصحيحة وعلامة (×) أمام الاجابة الخطأ</span>
                        </h3>
                        <h4 style="line-height: 27px;">
                            @if($question->answer==1)
                            <span style="font-size: 25pt;color: green;font-weight: bold;">√</span>
                            @else
                                <span style="font-size: 25pt;color: red;font-weight: bold;">×</span>
                            @endif
                            {{ $question->name }}
                            <div class="true">
                                @if($question->CurriculumQuestionsDetails->user_answer==1)
                                <span class="show-in-print" style="font-size: 12pt;color: green;font-weight: bold;">√</span>
                                @endif
                                <input disabled="" type="radio" name="questions[{{ $answer->id }}][{{ $question->id }}]" @if($question->CurriculumQuestionsDetails->user_answer==1) checked="checked" @endif value="1" id="question-41834-true">
                                <label for="question-{{ $question->id }}-true">
                                    <span></span>
                                </label>
                            </div>
                            <div class="false">
                                @if($question->CurriculumQuestionsDetails->user_answer==0)
                                <span class="show-in-print" style="font-size: 12pt;color: red;font-weight: bold;">×</span>
                                @endif
                                <input disabled="" type="radio" name="questions[{{ $answer->id }}][{{ $question->id }}]" @if($question->CurriculumQuestionsDetails->user_answer==0) checked="checked" @endif value="0" id="question-41834-false">
                                <label for="question-{{ $question->id }}-false">
                                    <span></span>
                                </label>
                            </div>
                        </h4>
                    </div>
                    @else
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <h3><span class="label label-info">اختار الاجابة الصحيحة</span></h3>
                        <h4 style="line-height: 27px;">
                            @if($question->answer==1)
                                <span style="font-size: 25pt;color: green;font-weight: bold;">√</span>
                            @else
                                <span style="font-size: 25pt;color: red;font-weight: bold;">×</span>
                            @endif
                            {{ $question->name }}
                        </h4>
                        @foreach($question->CurriculumQuestionsDetails as $detail)
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right">
                                <div class="chose">
                                    @if($detail->user_answer)
                                    <span class="show-in-print" style="font-size: 12pt;color: green;font-weight: bold;">√</span>
                                    @endif
                                    <input disabled @if($question->type=='chose_multiple') type="checkbox" @else type="radio" @endif name="questions[{{ $question->id }}][{{ $detail->id }}]" value="1" @if($detail->user_answer) checked="checked" @endif id="question-{{ $question->id }}-details-{{ $detail->id }}">
                                    <label for="question-{{ $question->id }}-details-{{ $detail->id }}">
                                        <span></span>
                                        {{ $detail->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                        <div class="clearfix"></div>
                @endforeach
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  col-lg-offset-3 col-md-offset-3 col-sm-offset-0 col-xs-offset-0 text-center">
                        <?php $percentage=round(($answer->right_answers/($answer->right_answers+$answer->wrong_answers))*100,1)?>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-0 col-xs-offset-0">
                            <div class="c100 padcir{{ intval($percentage) }} big {{ ($percentage>=50)?'green':'orange' }}">
                                <span>{{ $percentage }}%</span>
                                <div class="slice">
                                    <div class="bar"></div>
                                    <div class="fill"></div>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div id="report-details">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right color1">الوقت المستغرق</div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right color2 bold">
                                    <span id="duration-time">{{ $answer->duration_time }}</span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right color1">عدد الإجابات الصحيحة</div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right color2">
                                    <span class="small">{{ $answer->quetions_numbers }}/</span><span class="bold">{{ $answer->right_answers }}</span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right color1">عدد الإجابات الخطأ</div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right color2">
                                    <span class="small">{{ $answer->quetions_numbers }}/</span><span class="bold">{{ $answer->wrong_answers }}</span>
                                </div>
                            </div>

                        </div>

                        <!--<p>عدد الاسئلة <span></span></p>
                        <p>عدد الاجابات الصحيحة <span></span></p>
                        <p>عدد الاجابات الخطاة <span></span></p>-->
                    </div>
                </div>

            </div>
                <div class="clearfix"></div>
                <div class="clearfix" style="height: 20px;"></div>
                <div class="clearfix"></div>
                <hr>
                <div class="clearfix"></div>
                <div class="clearfix" style="height: 20px;"></div>
                <div class="clearfix"></div>
            @endforeach

		<!-- jQuery -->
		<script src="//code.jquery.com/jquery.js"></script>
		<!-- Bootstrap JavaScript -->
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	</body>
</html>
