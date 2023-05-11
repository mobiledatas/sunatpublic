<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BillRequest extends FormRequest
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
            // 'invoice.number'=>['required','alpha_dash'],
            // 'invoice.correlative'=>['required','string'],
            'invoice.date_emission'=>['required','date'],
            'invoice.currency'=>['required','string','in:USD,PEN'],
            'invoice.payment_method'=>['required','string','in:CONT,CRED'],
            'invoice.subtotal'=>['numeric'],
            'invoice.advanced'=>['required','numeric'],
            'invoice.discount'=>['required','numeric'],
            'invoice.sale_value'=>['numeric'],
            'invoice.isc'=>['required','numeric'],
            'invoice.igv'=>['numeric'],
            'invoice.isbper'=>['numeric'],
            'invoice.other_charges'=>['required','numeric'],
            // 'invoice.other_tributes'=>['required','numeric'],
            'invoice.legends' =>['required','array'],
            'invoice.legends.*.code'=>['required','string'],
            'invoice.legends.*.value'=>['required','string'],
                //Invoice items
                'invoice.items'=>['required','array'],
                'invoice.items.*.quantity'=>['required','integer'],
                'invoice.items.*.measurement_unit'=>['required','string'],
                'invoice.items.*.description'=>['required','string'],
                'invoice.items.*.unit_value'=>['required','numeric'],
                'invoice.items.*.icbper'=>['required','numeric'],
            'invoice.quotes'=>['required_if:invoice.payment_method,CRED','array'],
            'invoice.quotes.*.amount'=>['required_if:invoice.payment_method,CRED','numeric'],
            'invoice.quotes.*.payment_date'=>['required_if:invoice.payment_method,CRED','date'],
            'invoice.quotes.*.currency'=>['required_if:invoice.payment_method,CRED','string','min:3'],
            //Detraccion
            'invoice.detraction.cod_bien_detraction'=>['digits:3'],
            'invoice.detraction.cod_medio_pago'=>['digits:3'],
            'invoice.detraction.bank_account'=>['alpha_dash']
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

    public function messages() //OPTIONAL
    {
        return [
        ];
    }
}
