<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a new School + Proprietor onboarding request.
     *
     * Creates:
     *  1. A new School record
     *  2. A User with role = proprietor, linked to that school
     *  3. An empty UserProfile for the proprietor
     */
    public function register(Request $request)
    {
        $request->validate([
            // School details
            'school_name'    => ['required', 'string', 'max:255'],
            'school_email'   => ['nullable', 'email', 'max:255'],
            // Proprietor account details
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        // Wrap in a transaction — if anything fails, nothing gets saved halfway
        $user = DB::transaction(function () use ($request) {

            // 1. Create the School
            $school = School::create([
                'name'      => $request->school_name,
                'slug'      => $this->generateUniqueSlug($request->school_name),
                'address'   => $request->school_address,
                'is_active' => true,
            ]);
            $user = User::create([
                'school_id' => $school->id,
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'proprietor',
                'is_active' => true,
            ]);

            // 3. Create an empty profile for the proprietor
            UserProfile::create([
                'user_id' => $user->id,
            ]);

            return $user;
        });

        // Log the proprietor in immediately
        Auth::login($user);

        return $this->redirectUser()->with('success', 'Welcome! Your school has been set up successfully.');
    }

    private function redirectUser(){
        $user = Auth::user();

        return match($user->role){
            // 'student' => redirect()->route('student-dashboard'),
            'admin' => redirect()->route('admin-dashboard'),
            'proprietor' => redirect()->route('super-admin-dashboard'),
            default => redirect()->route('staff-dashboard'),
        };

    }

    /**
     * Generate a unique slug from the school name.
     * e.g. "Greenfield Academy" => "greenfield-academy"
     * If that slug already exists, appends a number: "greenfield-academy-2"
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 2;

        while (School::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }
}