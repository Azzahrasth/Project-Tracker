<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'status', 'completion_progress'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function calculateProgress()
    {
        // Reload relasi tasks
        $this->load('tasks'); 
        
        $totalTasks = $this->tasks->count(); 
        
        if ($totalTasks === 0) {
            $this->completion_progress = 0;
            $this->status = 'Draft';
            $this->save();
            return;
        }

        // Hitung total bobot dan bobot tugas yang selesai
        $totalWeight = $this->tasks->sum('weight');
        $doneWeight = $this->tasks->where('status', 'Done')->sum('weight');
        
        // Hitung persentase progress
        if ($totalWeight > 0) {
            $progress = ($doneWeight / $totalWeight) * 100;
            $this->completion_progress = round($progress, 2);
        } else {
            $this->completion_progress = 0;
        }

        // Menentukan Status
        if ($totalWeight > 0 && $doneWeight === $totalWeight) {
            $this->status = 'Done';
        } 
        elseif ($this->tasks->whereIn('status', ['In Progress', 'Done'])->count() > 0) {
            $this->status = 'In Progress';
        }
        else {
            $this->status = 'Draft';
        }
        
        $this->save();
    }
}