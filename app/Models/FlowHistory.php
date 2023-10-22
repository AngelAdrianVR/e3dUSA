<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowHistory extends Model
{
    use HasFactory;

    
    protected $fillable = ['description', 'historable_id', 'historable_type'];

    // relationshps
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
