<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Support\MemberNotificationFeed;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
                    $sent = app(MemberNotificationFeed::class)->broadcastToMembers(
                        title: (string) $data['title'],
                        body: (string) $data['body'],
                        adminId: Auth::id(),
                    );

                    Notification::make()
                        ->title('Broadcast sent')
                        ->body($sent . ' active member' . ($sent === 1 ? '' : 's') . ' notified.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
