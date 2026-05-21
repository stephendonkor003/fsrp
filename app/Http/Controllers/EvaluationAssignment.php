<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluationAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_id',
        'evaluation_id',
        'user_id',
        'role',
    ];

    /* ===============================
     | RELATIONSHIPS
   =============================== */

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}