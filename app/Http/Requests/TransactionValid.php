<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionValid extends FormRequest
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
        return [
            
            'date' => 'required|max:10|date_format:Y-m-d',
            'agent_code' => 'required|max:10',
            'tag_name' => 'required|max:15',
            // 'bankQuery' => 'required',
        ];
    }
}
