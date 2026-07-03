<?php

namespace App\Http\Requests;

use App\Rules\TanzaniaPhoneNumber;
use App\Support\TanzaniaPhoneNumber as PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHRRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // later you restrict by role
    }

    public function rules(): array
    {
        $hr = $this->route('hr');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($hr?->id)],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20', new TanzaniaPhoneNumber],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => PhoneNumber::normalize($this->input('phone')),
        ]);
    }
}
