<?php

namespace App\Http\Services;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    private Mailer $mailer;

    public function __construct()
    {
        $this->mailer = new Mailer();
    }

    public function login(string $email, string $password)
    {
        return Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function logout()
    {
        return Auth::logout();
    }

    public function refresh()
    {
        return Auth::logout();
    }

    public function generateEmailVerificationOtp(User|Authenticatable|null $user)
    {
        $otp = $user->createOtpToken($user->name . ' email confirmation otp')->plainTextToken;

        return $this->mailer->queue_mail($user->email, new WelcomeMail($user->name, $otp));
    }

    public function generateResetPasswordOtp(string $email)
    {
        $user = User::firstWhere('email', $email);

        if ($user == null) return null;

        $otp = $user->createOtpToken($user->name . ' password reset otp')->plainTextToken;

        return $this->mailer->queue_mail($user->email, new WelcomeMail($user->name, $otp));
    }

    public function generateJWToken(User $user)
    {
        return Auth::login($user);
    }
}
