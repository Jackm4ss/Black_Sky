@php
    $record = $getRecord();
    $name = trim((string) ($record->name ?? 'Black Sky Member')) ?: 'Black Sky Member';
    $email = trim((string) ($record->email ?? ''));
    $avatar = trim((string) ($record->avatar ?? ''));
    $avatarUrl = filled($avatar)
        ? (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://', '/']) ? $avatar : asset($avatar))
        : null;
    $initials = collect(preg_split('/\s+/', $name) ?: [])
        ->filter()
        ->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<span class="bsa-table-member">
    <span class="bsa-table-member__avatar" aria-hidden="true">
        @if ($avatarUrl)
            <img src="{{ $avatarUrl }}" alt="" loading="lazy">
        @else
            <span>{{ $initials ?: 'BS' }}</span>
        @endif
    </span>
    <span class="bsa-table-member__copy">
        <strong>{{ $name }}</strong>
        @if ($email !== '')
            <span>{{ $email }}</span>
        @endif
    </span>
</span>
