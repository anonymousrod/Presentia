<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'email'      => ['nullable', 'email', 'max:255', 'unique:users,email', 'required_without:phone'],
            'phone'      => ['nullable', 'string', 'max:255', 'unique:users,phone', 'required_without:email'],
            'birth_date' => ['nullable', 'date'],
            'weekly_contribution' => ['nullable', 'integer', 'min:0'],
            'church_service' => ['nullable', 'string', 'max:255'],
            'additional_info' => ['nullable', 'array'],
            'additional_info.*.title' => ['required_with:additional_info', 'string', 'max:255'],
            'additional_info.*.value' => ['required_with:additional_info', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'             => 'Le nom est obligatoire.',
            'first_name.required'       => 'Le prénom est obligatoire.',
            'email.email'               => 'L\'adresse email doit être valide.',
            'email.unique'              => 'Cette adresse email est déjà utilisée.',
            'email.required_without'    => 'L\'adresse email est obligatoire si le téléphone n\'est pas renseigné.',
            'phone.unique'              => 'Ce numéro de téléphone est déjà utilisé.',
            'phone.required_without'    => 'Le numéro de téléphone est obligatoire si l\'email n\'est pas renseigné.',
            'birth_date.date'           => 'La date de naissance doit être une date valide.',
            'weekly_contribution.integer' => 'La cotisation doit être un nombre entier.',
            'weekly_contribution.min'     => 'La cotisation ne peut pas être négative.',
        ];
    }
}
