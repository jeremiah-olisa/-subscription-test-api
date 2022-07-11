<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserRepository extends BaseRepository
{
    private AuthService $authService;

    public const CreateValidation = [
        'name' => 'required|string|max:70',
        'email' => 'required|string|email|max:45|unique:users',
        'phone_number' => 'required|string|min:9|max:14|unique:users|regex:/^\\+?[1-9][0-9]{7,14}$/',
        'password' => 'required|string|min:6|max:14|confirmed',
        'role' => 'required|string|exists:roles,name',
    ];

    public const UpdateValidation = [
        'name' => 'string|max:70',
        'email' => 'string|email|max:45|unique:users',
        'phone_number' => 'string|min:9|max:14|unique:users|regex:/^\\+?[1-9][0-9]{7,14}$/',
        'current_password' => 'string|min:6|max:14|required_with:password',
        'password' => 'string|min:6|max:14|confirmed|different:current_password|required_with:current_password',
    ];

    function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    // create User
    public function create(array $fields): User
    {
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'phone_number' => $fields['phone_number'],
            'password' => Hash::make($fields['password']),
        ]);

        $this->authService->generateEmailVerificationOtp($user);

        return $user;
    }

    // get all or list of Users in db
    public function list(): Paginator
    {
        return CustomQueryBuilder
            ::forModel(User::class, new User)
            ->simplePaginate($this->per_page);
    }

    // get single User
    public function get(int $id): User
    {
        $this->exists(new User(), 'id', $id, 'User was not found');

        return CustomQueryBuilder::forModel(User::class, new User)
            ->where('id', $id)
            ->with($this->include_q)
            ->select($this->select_q)
            ->first();
    }

    // get single User
    public function find(string $column, $value): User
    {
        $this->exists(new User(), $column, $value, 'User was not found');

        return CustomQueryBuilder::forModel(User::class, new User)
            ->where($column, $value)
            ->with($this->include_q)
            ->select($this->select_q)
            ->first();
    }

    // get single User
    public function update(int $id, array $fields): User
    {
        $user = $this->get($id);

        return $this->__update($user, $fields);
    }

    public function __update(User|Authenticatable|null $user, array $fields): User
    {
        $user->name = $fields['name'] ?? $user->name;
        $user->email = $fields['email'] ?? $user->email;
        $user->phone_number = $fields['phone_number'] ?? $user->phone_number;

        // TODO: restrict token
        if (empty($fields['current_password']) && isset($fields['current_password']) && !Hash::check($fields['current_password'], $user->password))
            throw new UnauthorizedHttpException('Unauthorized', 'Incorrect password');

        if (isset($fields['password']) && !empty($fields['password']))
            $user->password = Hash::make($fields['password']);


        if (isset($fields['email']) && !empty($fields['email'])) {
            $user->email_verified_at = null;
            // send confirmation mail
            $this->authService->generateEmailVerificationOtp($user);
        }

        // send confirmation sms
        if (isset($fields['phone_number']) && !empty($fields['phone_number']))
            $user->phone_number_verified_at = null;


        $user->save();

        return $user;
    }

    // delete single User
    public function remove($id): bool
    {
        return $this->get($id)->delete();
    }

    // delete multiple Users
    public function removeMany(array $ids)
    {
        $users = User::whereIn('id', $ids)->delete();
        return $users;
    }
}
