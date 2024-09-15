<?php

namespace App\Http\Requests;

use App\Rules\IsConstraint;
use App\Rules\IsVersion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'constraint' => ['required', 'string', new IsConstraint],
            'version' => ['required', 'string', new IsVersion],
        ];
    }
}
