<?php

namespace App\Models;

use DateTime;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public const AllQueryFields = ['id', 'name', 'email'];

    public const DefaultSorts = ['id', 'name', 'email'];

    public const AllowedSorts = ['id', 'name', 'email'];

    public const AllowedIncludes = [];

    public const AllowedFilters = ['id', 'name', 'email'];

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function createOtpToken(string $name, array $abilities = ['*'])
    {
        $otp = rand(100000, 999999);
        $name = Str::slug($name, '-');

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = $otp),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }

    public function verifyOtp($otp)
    {
        $token = $this->tokens()->where('token', hash('sha256', $otp))->where('tokenable_id', $this->id)->first();

        if ($token != null) {
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($token->created_at);
            $has_expired = intval($datetime1->diff($datetime2)->format('%i') ?? 0)
                > intval(env('APP_OTP_EXPIRATION_TIME', "10"));

            $token->delete();

            if ($has_expired) return null;
        }

        return $token;
    }

    public function getId()
    {
        return $this->id;
    }


    function verifyMail($otp)
    {
        $verify = $this->verifyOtp($otp);
        return ($verify != null) ? $this->markEmailAsVerified() : false;
    }

    public function websites()
    {
        $this->hasMany(Website::class, 'owner_id', 'id');
    }
    
    public function subscriptions()
    {
        $this->hasMany(Subscriber::class, 'user_id', 'id');
    }
    
    public function posts()
    {
        $this->hasManyThrough(Post::class, Website::class, 'owner_id', 'website_id', 'id', 'id');
    }
}
