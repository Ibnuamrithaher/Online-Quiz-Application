<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'action', 'description', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to record an activity quickly.
     */
    public static function record(string $action, string $description, ?int $userId = null)
    {
        self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
