<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_email',
        'sender_id',
        'team_id',
        'token'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'email', 'recipient_email');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'id', 'sender_id');
    }
}
