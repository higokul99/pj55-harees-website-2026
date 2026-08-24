<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('sign');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'password' => 'required|digits:4',
        ]);

        $phone = $request->phone;
        $password = $request->password;

        // Find user by phone
        $user = User::where('phone', $phone)->first();

        // Check if user exists and password matches (plain text comparison as per old system)
        if ($user && $password === $user->password) {
            // Login successful
            Auth::login($user, $request->has('remember'));

            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        // Login failed
        return back()->with('error', 'Invalid phone number or password')->withInput($request->only('phone'));
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|min:3|max:50|regex:/^[A-Za-z ]+$/',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
            'pin' => 'required|digits:4',
            'confirm_pin' => 'required|same:pin',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'address1' => 'required|string',
            'address2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string',
            'landmark' => 'nullable|string',
            'terms' => 'required|accepted',
        ]);

        // Create new user
        $user = User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->pin, // Storing plain text as per old system
            'security_question' => $request->security_question,
            'security_answer' => $request->security_answer,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'landmark' => $request->landmark,
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! Please login.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password - step 1: verify phone
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->with('error', 'No account found with that phone number');
        }

        // Store in session for next steps
        session([
            'reset_phone' => $user->phone,
            'security_question' => $user->security_question,
            'correct_answer' => $user->security_answer,
        ]);

        return back()->with('step', 'security');
    }

    /**
     * Verify security answer
     */
    public function verifySecurity(Request $request)
    {
        $request->validate([
            'security_answer' => 'required|string',
        ]);

        if (strtolower($request->security_answer) !== strtolower(session('correct_answer'))) {
            return back()->with('error', 'Incorrect answer to security question');
        }

        session(['security_verified' => true]);
        return back()->with('step', 'reset');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'new_pin' => 'required|digits:4',
            'confirm_pin' => 'required|same:new_pin',
        ]);

        if (!session('security_verified')) {
            return redirect()->route('password.request')->with('error', 'Security verification required');
        }

        $user = User::where('phone', session('reset_phone'))->first();
        $user->password = $request->new_pin;
        $user->save();

        // Clear session
        session()->forget(['reset_phone', 'security_question', 'correct_answer', 'security_verified']);

        return redirect()->route('login')->with('success', 'Your PIN has been updated successfully');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Handle different update sections
        $section = $request->input('section');

        switch ($section) {
            case 'personal':
                $validated = $request->validate([
                    'fullname' => 'required|string|min:3|max:50|regex:/^[A-Za-z ]+$/',
                ]);

                $user->fullname = $validated['fullname'];
                $user->save();

                return back()->with('success', 'Personal information updated successfully.');

            case 'address':
                $validated = $request->validate([
                    'address1' => 'required|string|max:255',
                    'address2' => 'nullable|string|max:255',
                    'city' => 'required|string|max:100',
                    'state' => 'required|string|max:100',
                    'pincode' => 'required|digits:6',
                    'landmark' => 'nullable|string|max:255',
                ]);

                $user->update($validated);
                return back()->with('success', 'Address information updated successfully.');

            case 'birthday':
                $validated = $request->validate([
                    'dob' => 'nullable|date|before_or_equal:today',
                ]);

                $user->update($validated);
                return back()->with('success', 'Birthday updated successfully.');

            case 'anniversary':
                $validated = $request->validate([
                    'anniversary' => 'nullable|date|before_or_equal:today',
                ]);

                $user->update($validated);
                return back()->with('success', 'Anniversary updated successfully.');

            case 'dates':
                $validated = $request->validate([
                    'dob' => 'nullable|date|before_or_equal:today',
                    'anniversary' => 'nullable|date|before_or_equal:today',
                ]);

                $user->update($validated);
                return back()->with('success', 'Special dates updated successfully.');

            case 'password':
                $request->validate([
                    'current_password' => 'required',
                    'new_password' => 'required|digits:4',
                    'new_password_confirmation' => 'required|same:new_password',
                ]);

                // Check if current password matches (simple comparison as per requirement)
                if ($user->password !== $request->current_password) {
                    return back()->with('error', 'Current password is incorrect.');
                }

                $user->password = $request->new_password;
                $user->save();

                return back()->with('success', 'Password updated successfully.');

            default:
                return back()->with('error', 'Invalid update request.');
        }
    }
}
