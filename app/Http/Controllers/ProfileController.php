<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Traits\OptimizesImages;

class ProfileController extends Controller
{
    use OptimizesImages;

    /**
     * Display the user's profile and settings form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $completionPercentage = $this->calculateCompletionPercentage($user);

        // Calcul des cotisations annuelles (Février à Novembre)
        $startOfYear = \Carbon\Carbon::parse(date('Y') . '-02-01')->startOfDay();
        $endOfYear = \Carbon\Carbon::parse(date('Y') . '-11-30')->endOfDay();

        $totalSundaysInYear = 0;
        $dateIt = $startOfYear->copy()->next(\Carbon\Carbon::SUNDAY);
        if ($startOfYear->isSunday()) {
            $dateIt = $startOfYear->copy();
        }
        while ($dateIt->lte($endOfYear)) {
            $totalSundaysInYear++;
            $dateIt->addWeek();
        }

        $expectedContribution = $user->weekly_contribution ? $user->weekly_contribution * $totalSundaysInYear : 0;
        $paidContribution = $user->contributions()->whereBetween('date', [$startOfYear, $endOfYear])->sum('amount');

        return view('profile.edit', [
            'user' => $user,
            'completionPercentage' => $completionPercentage,
            'expectedContribution' => $expectedContribution,
            'paidContribution' => $paidContribution,
            'totalSundaysInYear' => $totalSundaysInYear
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name'             => ['required', 'string', 'max:255'],
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'                  => ['nullable', 'string', 'max:20'],
            'birth_date'             => ['nullable', 'date'],
            'education_field'        => ['nullable', 'string', 'max:255'],
            'professional_status'    => ['nullable', 'string', 'max:255'],
            'current_profession'     => ['nullable', 'string', 'max:255'],
            'education_level'        => ['nullable', 'string', 'max:255'],
            'residence_municipality' => ['nullable', 'string', 'max:255'],
            'residence_neighborhood' => ['nullable', 'string', 'max:255'],
            'church_service'         => ['nullable', 'string', 'max:255'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Update Avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:51200'] // 50MB max
        ]);

        $user = $request->user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $path = $this->optimizeAndStoreImage($request->file('photo'), 'avatars');
        $user->update(['photo' => $path]);

        return back()->with('status', 'avatar-updated');
    }

    /**
     * Update Cover Photo
     */
    public function updateCover(Request $request)
    {
        $request->validate([
            'cover_photo' => ['required', 'image', 'max:51200']
        ]);

        $user = $request->user();

        if ($user->cover_photo) {
            Storage::disk('public')->delete($user->cover_photo);
        }

        $path = $this->optimizeAndStoreImage($request->file('cover_photo'), 'covers');
        $user->update(['cover_photo' => $path]);

        return back()->with('status', 'cover-updated');
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateCompletionPercentage($user): int
    {
        $fields = [
            'name',
            'first_name',
            'email',
            'phone',
            'birth_date',
            'photo',
            'cover_photo',
            'education_field',
            'professional_status',
            'current_profession',
            'education_level',
            'residence_municipality',
            'residence_neighborhood',
            'church_service',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($user->{$field})) {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }
}
