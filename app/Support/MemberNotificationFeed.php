<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberNotificationFeed
{
    private const NOTIFICATION_TYPE = 'member_dashboard';
    private const BROADCAST_CHUNK_SIZE = 500;

    public function recordActivity(
        User $user,
        string $title,
        string $body,
        string $activityType,
        ?string $actionUrl = null,
    ): void {
        DB::table('notifications')->insert([
            $this->notificationRow(
                userId: (int) $user->id,
                title: $title,
                body: $body,
                dataType: $activityType,
                source: 'activity',
                actionUrl: $actionUrl,
                now: now(),
            ),
        ]);
    }

    public function broadcastToMembers(string $title, string $body, ?int $adminId = null): int
    {
        $sent = 0;

        User::query()
            ->select('id')
            ->where('is_active', true)
            ->whereHas('roles', fn (Builder $query): Builder => $query->where('name', 'user'))
            ->whereDoesntHave('roles', fn (Builder $query): Builder => $query->where('name', 'admin'))
            ->orderBy('id')
            ->chunkById(self::BROADCAST_CHUNK_SIZE, function (Collection $users) use ($title, $body, $adminId, &$sent): void {
                $now = now();
                $rows = $users
                    ->map(fn (User $user): array => $this->notificationRow(
                        userId: (int) $user->id,
                        title: $title,
                        body: $body,
                        dataType: 'admin.broadcast',
                        source: 'admin',
                        actionUrl: null,
                        now: $now,
                        adminId: $adminId,
                    ))
                    ->all();

                if ($rows === []) {
                    return;
                }

                DB::table('notifications')->insert($rows);
                $sent += count($rows);
            });

        return $sent;
    }

    private function notificationRow(
        int $userId,
        string $title,
        string $body,
        string $dataType,
        string $source,
        ?string $actionUrl,
        Carbon $now,
        ?int $adminId = null,
    ): array {
        return [
            'id' => (string) Str::uuid(),
            'type' => self::NOTIFICATION_TYPE,
            'notifiable_type' => User::class,
            'notifiable_id' => $userId,
            'data' => json_encode([
                'title' => Str::of($title)->squish()->limit(120, '')->toString(),
                'body' => Str::of($body)->squish()->limit(500, '')->toString(),
                'type' => $dataType,
                'source' => $source,
                'action_url' => $actionUrl,
                'admin_id' => $adminId,
            ], JSON_THROW_ON_ERROR),
            'read_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
