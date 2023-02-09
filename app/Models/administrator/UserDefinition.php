<?php

namespace App\Models\administrator;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use RichardStyles\EloquentEncryption\Casts\Encrypted;


class UserDefinition extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids;

    protected $table = 'FE_USERS';

    protected $fillable = [
        'user_password', 'user_id', 'application', 'USER_LAST_NAME', 'USER_FIRST_NAME', 'GROUP_ID'
    ];

    protected $casts = [
        //'user_password' => 'encrypted',
        'user_password' => Encrypted::class,
    ];
}
