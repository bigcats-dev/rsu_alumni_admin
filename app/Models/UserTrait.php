<?php

namespace App\Models;

trait UserTrait
{
    public function user_create()
    {
        return $this->belongsTo(User::class, 'user_create_id', 'id');
    }

    public function user_update()
    {
        return $this->belongsTo(User::class, 'user_update_id', 'id');
    }

    public function user_approve()
    {
        return $this->belongsTo(User::class, 'user_approve_id', 'id');
    }
}
