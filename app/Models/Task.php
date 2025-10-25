<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Task extends Model
{
    protected $fillable = ['project_id', 'name', 'status', 'weight'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
