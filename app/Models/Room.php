<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     *
     */
    protected $table = 'ROOM';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'room_uid';
}
