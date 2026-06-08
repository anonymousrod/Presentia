<?php

namespace App\Rules;

use Closure;
use App\Models\Activity;
use Illuminate\Contracts\Validation\ValidationRule;

class RegistrationDeadline implements ValidationRule
{
    protected Activity $activity;

    /**
     * Create a new rule instance.
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->activity->start_time->subHours(2)->lt(now())) {
            $fail("L'action n'est plus possible à moins de 2 heures du début de l'activité.");
        }
    }
}
