<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SEOSiteMapRequest extends FormRequest
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
        foreach($this->request->get('priority') as $key => $val)
        {
            $rules['priority.'.$key] = 'required|numeric|max:1|min:0.1';
        }

        return $rules;
    }
}
