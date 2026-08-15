<?php

namespace App\Modules\Auth\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';
    protected $fillable = ['email', 'token', 'created_at'];
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
