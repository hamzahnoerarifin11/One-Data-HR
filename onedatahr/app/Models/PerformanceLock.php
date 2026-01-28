<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceLock extends Model
{
    protected $fillable = [
        'tahun',
        'status',
        'locked_by',
        'locked_at',
        'unlocked_by',
        'unlocked_at',
        'locked_reason',
        'unlock_reason',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
    ];

    /**
     * Relationship: locked by user
     */
    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Relationship: unlocked by user
     */
    public function unlockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    /**
     * Check if a tahun is locked
     */
    public static function isLocked(int $tahun): bool
    {
        return static::where('tahun', $tahun)
            ->where('status', 'locked')
            ->exists();
    }

    /**
     * Get current lock status for tahun
     */
    public static function getLockStatus(int $tahun): ?self
    {
        return static::where('tahun', $tahun)
            ->latest()
            ->first();
    }

    /**
     * Lock a tahun
     */
    public static function lock(int $tahun, int $userId, string $reason = null): self
    {
        // Unlock any previous lock for this tahun
        static::where('tahun', $tahun)->update([
            'status' => 'unlocked',
            'unlocked_by' => $userId,
            'unlocked_at' => now(),
        ]);

        // Create new lock
        return static::create([
            'tahun' => $tahun,
            'status' => 'locked',
            'locked_by' => $userId,
            'locked_at' => now(),
            'locked_reason' => $reason,
        ]);
    }

    /**
     * Unlock a tahun (superadmin only)
     */
    public static function unlock(int $tahun, int $userId, string $reason = null): bool
    {
        return static::where('tahun', $tahun)
            ->where('status', 'locked')
            ->update([
                'status' => 'unlocked',
                'unlocked_by' => $userId,
                'unlocked_at' => now(),
                'unlock_reason' => $reason,
            ]) > 0;
    }

    /**
     * Get lock history for tahun
     */
    public static function getHistory(int $tahun): array
    {
        $lock = static::where('tahun', $tahun)->latest()->first();

        return [
            'tahun' => $tahun,
            'is_locked' => $lock?->status === 'locked',
            'locked_by_name' => $lock?->lockedBy?->name ?? '-',
            'locked_at' => $lock?->locked_at?->format('d-m-Y H:i:s') ?? '-',
            'locked_reason' => $lock?->locked_reason ?? '-',
            'unlocked_by_name' => $lock?->unlockedBy?->name ?? '-',
            'unlocked_at' => $lock?->unlocked_at?->format('d-m-Y H:i:s') ?? '-',
            'unlock_reason' => $lock?->unlock_reason ?? '-',
        ];
    }
}
