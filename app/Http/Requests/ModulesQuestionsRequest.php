<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;


class ModulesQuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules['question_ar']    = 'required';
        $rules['question_en']    = 'required';
        $rules['difficulty']     = 'required';
        $rules['type']           = 'required';
        $rules['chose_single']   = 'required_if:type,chose_single';
        $rules['chose_multiple'] = 'required_if:type,chose_multiple|array|min:2';

        if($this->request->has('answers_text')) {
            $rules['answers_text'] = 'min:2';
            foreach($this->request->get('answers_text') as $key => $val) {
                $rules['answers_text.'.$key] = 'required';
            }
        }

        if($this->request->has('answers_text_en')) {
            $rules['answers_text_en'] = 'min:2';
            foreach($this->request->get('answers_text_en') as $key => $val) {
                $rules['answers_text_en.'.$key] = 'required';
            }
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'question_ar.required' => Lang::get('main.the_question_arabic_field_is_required'),
            'question_en.required' => Lang::get('main.the_question_english_field_is_required'),
            'answers_text.*.required' => Lang::get('main.enter_arabic_answer_text'),
            'answers_text.min' => Lang::get('main.at_least_2_choices'),
            'answers_text_en.*.required' => Lang::get('main.enter_english_answer_text'),
            'answers_text_en.min' => Lang::get('main.at_least_2_choices'),
            'chose_single.required_if' => Lang::get('main.single_choice_check'),
            'chose_multiple.required_if' => Lang::get('main.multiple_choice_check'),
            'chose_multiple.min' => Lang::get('main.at_least_2_choices_multiple'),
        ];
    }
}
