<?php

namespace App\Http\Requests\Api\V1\Owner;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CalendarRequest extends FormRequest
{

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ];
    }

    /**
     * @param Validator $validator
     *
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->input('from') || !$this->input('to')) {
                return;
            }

            $from = Carbon::parse($this->input('from'));
            $to   = Carbon::parse($this->input('to'));

            if ($from->diffInDays($to) > 31) {
                $validator->errors()->add('to', 'Range cannot exceed 31 days.');
            }
        });
    }
}
