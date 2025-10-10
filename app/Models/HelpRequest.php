<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    protected $table = 'requests';
    
    protected $fillable = [
        'user_id',
        'name',
        'phone', 
        'email',
        'location',
        'latitude',
        'longitude',
        'request_type',
        'description',
        'people_count',
        'special_needs',
        'urgency',
        'status',
        'assigned_by',
        'admin_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignment()
    {
        return $this->hasOne(Assignment::class, 'request_id');
    }

    public function assignedShelter()
    {
        return $this->belongsTo(Shelter::class, 'assigned_shelter_id');
    }
}
