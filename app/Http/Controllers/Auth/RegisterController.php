<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_email' => ['nullable', 'email', 'max:255'],
            // Proprietor account details
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = DB::transaction(function () use ($request) {

            $school = School::create([
                'name' => $request->school_name,
                'slug' => $this->generateUniqueSlug($request->school_name),
                'email' => $request->school_email,
                'address' => $request->school_address,
                'is_active' => true,
            ]);
            $user = User::create([
                'school_id' => $school->id,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'proprietor',
                'is_active' => true,
            ]);
            $user->assignRole('SchoolOwner');

            UserProfile::create([
                'user_id' => $user->id,
            ]);

            return $user;
        });
        Auth::login($user);

        return $this->redirectUser()->with('success', 'Welcome! Your school has been set up successfully.');
    }

    protected function redirectUser()
    {
        $user = Auth::user();

        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('super-admin-dashboard');
        }

        if ($user->hasRole('SchoolOwner')) {
            return redirect()->route('proprietor.dashboard');
        }

        if ($user->hasRole('SchoolAdmin')) {
            return redirect()->route('admin-dashboard');
        }

        return redirect()->route('loginview');
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
            $slug = $original.'-'.$count;
            $count++;
        }

        return $slug;
    }
}
