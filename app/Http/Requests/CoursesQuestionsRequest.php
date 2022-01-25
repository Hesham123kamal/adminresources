<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class CoursesQuestionsRequest extends FormRequest
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

        $rules['question'] = 'required';
        $rules['type'] = 'required';
        $rules['image'] = 'sometimes|image';
        $rules['chose_single'] = 'required_if:type,chose_single,chose_single_with_images';
        $rules['chose_multiple'] = 'required_if:type,chose_multiple,chose_multiple_with_images|array|min:2';

        if($this->request->get('question_id') == null) {
            $rules['answers_images'] = 'required_if:type,chose_single_with_images,chose_multiple_with_images|array|min:2|size:'.$this->request->get('image_count');
            $rules['answers_images_en'] = 'sometimes|required_if:type,chose_single_with_images,chose_multiple_with_images|array|min:2|size:'.$this->request->get('image_count');
        } else {
            $rules['answers_images_edit'] = 'required_if:type,chose_single_with_images,chose_multiple_with_images|array|min:2|size:'.count($this->request->get('answers_images_edit'));
        }

        if($this->request->has('answers_text')) {
            $rules['answers_text'] = 'min:2';
            foreach($this->request->get('answers_text') as $key => $val)
            {
                $rules['answers_text.'.$key] = 'required';
            }
        }
        if($this->request->has('answers_text_en')) {
            $rules['answers_text_en'] = 'min:2';
            foreach($this->request->get('answers_text_en') as $key => $val)
            {
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
            'question.required' => Lang::get('main.the_question_text_field_is_required'),
            'image.mimes' =>  Lang::get('main.the_question_image_extension_must_be'),
            'chose_single.required_if' => Lang::get('main.single_choice_check'),
            'answers_text.*.required' => Lang::get('main.enter_answer_text'),
            'answers_text.min' => Lang::get('main.at_least_2_choices'),
            'answers_text_en.*.required' => Lang::get('main.enter_answer_text'),
            'answers_text_en.min' => Lang::get('main.at_least_2_choices'),
            'chose_multiple.required_if' => Lang::get('main.multiple_choice_check'),
            'chose_multiple.min' => Lang::get('main.at_least_2_choices_multiple'),
            'answers_images.required_if' =>  Lang::get('main.questions_images_4_upload'),
            'answers_images.min' => Lang::get('main.at_least_2_choices'),
            'answers_images_en.required_if' => Lang::get('main.questions_images_4_upload'),
            'answers_images_en.min' => Lang::get('main.at_least_2_choices'),
        ];
    }
}
