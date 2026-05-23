<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Jobs\BroadcastMemberNotification;
use App\Support\MemberNotificationFeed;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.list-records-shell';

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    /**
     * @return array<string, mixed>
     */
    public function adminListMeta(): array
    {
        return [
            'heroEyebrow' => 'Member Management',
            'heroTitle' => 'User Management',
            'heroDescription' => 'Manage member identity, registration source, country, and account activity.',
            'totalLabel' => 'Total Members',
            'totalValue' => UserResource::getEloquentQuery()->count(),
            'cardEyebrow' => 'Member Directory',
            'cardTitle' => 'Registered Member Catalog',
            'action' => [
                'type' => 'wire',
                'label' => 'Broadcast Notification',
                'wireClick' => "mountAction('broadcastNotification')",
                'icon' => 'heroicon-o-megaphone',
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('broadcastNotification')
                ->label('Broadcast Notification')
                ->icon('heroicon-o-megaphone')
                ->modalHeading('Broadcast member notification')
                ->modalSubmitActionLabel('Send broadcast')
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(120),
                    Forms\Components\Textarea::make('body')
                        ->label('Message')
                        ->required()
                        ->rows(4)
                        ->maxLength(500),
                ])
                ->action(function (array $data): void {
                    BroadcastMemberNotification::dispatch(
                        title: (string) $data['title'],
                        body: (string) $data['body'],
                        adminId: Auth::id(),
                    );

                    $recipientCount = app(MemberNotificationFeed::class)->countBroadcastRecipients();

                    Notification::make()
                        ->title('Broadcast queued')
                        ->body($recipientCount.' active member'.($recipientCount === 1 ? '' : 's').' will be notified.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
