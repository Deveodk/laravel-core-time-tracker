<?php

namespace DeveoDK\CoreTimeTracker\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewTimeTrack extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'email' => 'email|required'
        ];
    }

    /**
     * Give the data back
     * @return array
     */
    public function data()
    {
        return [
            'email' => $this->email,
        ];
    }
}
