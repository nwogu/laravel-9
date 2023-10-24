<?php

namespace App\Models;

use App\Jobs\SendEmail;
use App\Jobs\CacheEmail;
use App\Jobs\IndexEmail;
use App\Jobs\MotivateUser;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
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
        'last_email_sent_at' => 'datetime',
    ];

    /**
     * Motivate the user
     *
     * @return void
     */
    public function motivate()
    {
        MotivateUser::dispatchSync($this);
    }

    /**
     * Create a greeting that we can display to the user.
     *
     * @param  bool  $smallTalk
     * @param  string  $salutation
     * @return string
     */
    public function getGreeting(bool $smallTalk = true, string $salutation): string
    {
        $greeting = "$salutation, {$this->name}!";

        if ($smallTalk) {
            $greeting .= " Lovely weather we are having!";
        }

        return $greeting;
    }
}
