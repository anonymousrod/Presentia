<?php

namespace App\Http\Requests;

use App\Enums\RegistrationStatus;
use App\Rules\RegistrationDeadline;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreRegistrationRequest extends FormRequest
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
        $activity = $this->route('activity');

        $statusRules = [
            'required',
            new Enum(RegistrationStatus::class),
        ];

        if ($activity && $activity->start_time && $activity->start_time->gt(now())) {
            $statusRules[] = new RegistrationDeadline($activity);
        }

        return [
            'status' => $statusRules,
            'justification' => [
                'required_if:status,' . RegistrationStatus::ABSENT_JUSTIFIED->value,
                'nullable',
                'string',
                'min:5',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'justification.required_if' => 'Le motif est obligatoire lorsque vous indiquez être absent.',
            'justification.min' => 'Le motif doit faire au moins 5 caractères.',
            'justification.max' => 'Le motif ne peut pas dépasser 255 caractères.',
        ];
    }
}
