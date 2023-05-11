<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NoteRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'note'=>'required',
            'note.code'=>'string',
            'note.description'=>'required',
            'note.currency'=>'in:USD,PEN',
            //Emisor object
            'company'=>['required'],
            'company.name'=>['required','string'],
            'company.ruc'=>['required','max:11','min:11'],
                //Emisor address object
                'company.address'=>['required'],
                'company.address.department'=>['required','string'],
                'company.address.province'=>['required','string'],
                'company.address.district'=>['required','string'],
                'company.address.urbanization'=>['string'],
                'company.address.address'=>['required','string'],
            //Customer object
            'customer'=>['required'],
            'customer.name'=>['required','string'],
            'customer.phone'=>['string','nullable'],
            'customer.ruc'=>['required','max:11','min:11'],
                //Customer address object
                'customer.address'=>['required'],
                'customer.address.department'=>['string','nullable'],
                'customer.address.province'=>['string','nullable'],
                'customer.address.district'=>['string','nullable'],
                'customer.address.urbanization'=>['string','nullable'],
                'customer.address.address'=>['string','nullable'],
            //Invoice object
            // 'invoice'=>['required'],
            'invoice.serie'=>['required'],
            'invoice.correlative'=>['required'],
            
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ],403));
    }
}
