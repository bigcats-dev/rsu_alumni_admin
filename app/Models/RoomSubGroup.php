<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomSubGroup extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     *
     */
    protected $table = 'ROOM_SUB_GROUP';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'room_sub_group_uid';
}
