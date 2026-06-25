<?php

namespace App\Http\Requests\Admin;

use App\Enums\ActivityStatus;
// Removed ActivityType enum
use App\Enums\ActivityVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $visibility = $this->input('visibility');
        if ($visibility instanceof ActivityVisibility) {
            $visibility = $visibility->value;
        }

        if ($visibility !== 'GROUP') {
            $this->merge(['visibility_group_id' => null]);
        }
        if ($visibility !== 'ROLE') {
            $this->merge(['visibility_role_id' => null]);
        }

        if (!$this->has('is_registration_required')) {
            $this->merge(['is_registration_required' => false]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'activity_type_id'    => ['required', 'exists:activity_types,id'],
            'status'              => ['required', new Enum(ActivityStatus::class)],
            'visibility'          => ['required', new Enum(ActivityVisibility::class)],
            'visibility_group_id' => ['required_if:visibility,GROUP', 'nullable', 'exists:groups,id'],
            'visibility_role_id'  => ['required_if:visibility,ROLE', 'nullable', 'exists:roles,id'],
            'start_time'          => ['required', 'date'],
            'end_time'            => ['required', 'date', 'after_or_equal:start_time'],
            'location'            => ['nullable', 'string', 'max:255'],
            'capacity'            => ['nullable', 'integer', 'min:1'],
            'responsible_id'      => ['nullable', 'exists:users,id'],
            'cancellation_reason' => ['required_if:status,CANCELLED', 'nullable', 'string'],
            'is_registration_required' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Customize the error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'cancellation_reason.required_if' => 'Le motif d\'annulation est obligatoire lorsque le statut est ANNULÉ.',
        ];
    }
}
