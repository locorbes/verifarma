<?php

namespace app\Http\Requests;

class FindPharmacyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ];
    }
}