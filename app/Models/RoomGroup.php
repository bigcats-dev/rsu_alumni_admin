<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomGroup extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     *
     */
    protected $table = 'ROOM_GROUP';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'room_group_uid';
}
