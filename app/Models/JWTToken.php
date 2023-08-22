<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JWTToken extends Model
{
    use HasFactory;

    protected $table = 'jwt_tokens';

    protected $fillable = [
        'token_title',
        'unique_id',
        'expires_at',
        'last_used_at',
        'refreshed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'restrictions' => 'array',
        'permissions'  => 'array',
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
        'refreshed_at' => 'datetime',
    ];

}
