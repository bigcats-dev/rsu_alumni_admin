<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;
    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'AL_ALUMNI_DETAIL';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'alumni_code';
}
