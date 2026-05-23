<?php

namespace App\Jobs;

use App\Support\MemberNotificationFeed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastMemberNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly ?int $adminId = null,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(MemberNotificationFeed $feed): void
    {
        $feed->broadcastToMembers(
            title: $this->title,
            body: $this->body,
            adminId: $this->adminId,
        );
    }
}
