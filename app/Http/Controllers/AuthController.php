<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// DONE: addition user profile fields
// DONE: update profile
// DONE: change password
// DONE: change email
// Done: forgot password (except,middleware)
// TODO: social auth

class AuthController extends Controller
{
    public AuthService $authService;
    public UserRepository $userRepository;

    public function __construct()
    {
        // parent::__construct();
        $this->authService = new AuthService;
        $this->userRepository = new UserRepository;
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyMail', 'forgotPassword', 'resetPassword']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $token = $this->authService->login($request['email'], $request['password']);

        if (!$token) return $this->response('Unauthorized', 401);

        return $this->response('Login successfull', 200, $this->user(), ['token' => $token]);
    }

    public function register(Request $request)
    {
        $request->validate($this->userRepository::CreateValidation);

        $user = $this->userRepository->create($request->all());

        $token = $this->authService->generateJWToken($user);

        return $this->response('User created successfully', 200, $user, ['token' => $token]);
    }

    public function logout()
    {
        $this->authService->logout();

        return $this->response('Successfully logged out', 200);
    }

    public function profile()
    {
        return $this->response('Profile retrived', 200, $this->user());
    }


    public function refresh()
    {
        return $this->response(
            'User token refreshed successfully',
            200,
            $this->user(),
            ['token' => $this->authService->refresh()]
        );
    }

    public function verifyMail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:45|exists:users,email',
            'otp' => 'required|numeric|min:100000|max:999999',
        ]);

        $user = User::firstWhere('email', $request->email);

        $otp = $user->verifyMail($request->otp);

        if ($otp == true) return $this->response('Email verified successfully', 200);

        return $this->response('Email address was not verified', 400, $otp);
    }

    public function sendVerificationMail()
    {
        $user = $this->user();

        $data = $this->authService->generateEmailVerificationOtp($user);

        return $this->response('Confirm email has been sent', 200, $data);
    }

    public function updateUserProfile(Request $request)
    {
        $request->validate($this->userRepository::CreateValidation);

        $fields = $request->only('name', 'email', 'password', 'current_password');

        $user = $this->userRepository->__update($this->user(), $fields);

        return $this->response('User profile updated successfully', 200, $user);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:45',
        ]);

        $this->authService->generateResetPasswordOtp($request->email);

        return $this->response('Token has being sent to your mail', 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:45',
            'otp' => 'required|numeric|min:100000|max:999999',
            'password' => 'string|min:6|max:14|confirmed',
        ]);

        $user = $this->userRepository->find('email', $request->email); 

        $otp = $user->verifyOtp($request->otp);

        if (!$otp) return $this->response('Could not reset password', 400);

        $user = $this->userRepository->__update($user, $request->only('password'));

        return $this->response('Password reset successfully', 200);
    }
}
