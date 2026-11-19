<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'name', 'email', 'role', 'company_id',
        'token', 'invited_by', 'accepted_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }
}
