<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KudosCount extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'member_id', 'kudos'];
}
