<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        $profile = Auth::user();
        $schools = School::get();

        return match ($profile->role) {
            'staff' => view('dashboards.staff.profile.editprofile', compact('profile','schools')),
            'admin' => view('dashboards.admin.profile.editprofile', compact('profile','schools')),
            'proprietor' => view('dashboards.proprietor.profile.edit',compact('profile','schools')),
            'super-admin' => view('dashboards.super-admin.profile.editprofile', compact('user','schools')),

            default => abort(403, 'Unauthorized'),
        };
    }

    public function show()
    {
        $user    = Auth::user();
        $profile = $user->profile;

        return view('profile.show', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user    = Auth::user();
        $profile = $user->profile;

        $request->validate([
            'fullname'            => ['required', 'string', 'max:255'],
            'email'           => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone'           => ['nullable', 'string', 'max:20'],
            'gender'          => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth'   => ['nullable', 'date', 'before:today'],
            'address'         => ['nullable', 'string', 'max:500'],
            'avatar'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarPath = $profile?->avatar;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it's not the default
            if ($avatarPath && $avatarPath !== 'profile-images/default-avatar.png') {
                $fullPath = public_path($avatarPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $file      = $request->file('avatar');
            $filename  = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile-images'), $filename);
            $avatarPath = 'profile-images/' . $filename;
        }

        $user->update([
            'fullname'  => $request->fullname,
            // 'email' => $request->email,
        ]);

        // if ($request->filled('new_password')) {
        //     if (!Hash::check($request->current_password, $user->password)) {
        //         return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        //     }
        //     $user->update(['password' => Hash::make($request->new_password)]);
        // }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'         => $request->phone,
                'gender'        => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address'       => $request->address,
                'avatar'        => $avatarPath,
            ]
        );

        return back()->with('success', 'Profile updated successfully.');
    }
    public function changePasswordView(){
        $user = Auth::user();
        return match ($user->role) {
            'staff' => view('dashboards.staff.profile.change-password', compact('user')),
            'admin' => view('dashboards.admin.profile.change-password', compact('user')),
            'proprietor' => view('dashboards.proprietor.profile.change-password', compact('user')),
            'super-admin' => view('dashboards.super-admin.profile.change-password', compact('user')),
            default => abort(403, 'Unauthorized'),
        };

    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }


    public function showSchool()
    {
        $school = Auth::user()->school;

        return view('dashboards.proprietor.school-profile.show', compact('school'));
    }

    public function updateSchool(Request $request)
    {
        dd($request->all());
        $school = Auth::user()->school;

        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'motto'   => ['nullable', 'string', 'max:255'],
            'logo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $logoPath = $school->logo;

        if ($request->hasFile('logo')) {
            if ($logoPath) {
                $fullPath = public_path($logoPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $file     = $request->file('logo');
            $filename = 'school_logo_' . $school->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('school-logos'), $filename);
            $logoPath = 'school-logos/' . $filename;
        }

        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            if ($logoPath) {
                $fullPath = public_path($logoPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            $logoPath = null;
        }

        $school->update([
            'name'    => $request->name,
            'slug'    => Str::slug($request->name),
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'motto'   => $request->motto,
            'logo'    => $logoPath,
        ]);

        return back()->with('success', 'School profile updated successfully.');
    }




    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
