<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Normal => 'Normal',
            self::High => 'High',
            self::Urgent => 'Urgent',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $priority): array => [$priority->value => $priority->label()])
            ->all();
    }

    /**
     * @return array{first_response_hours: int, resolve_hours: int}
     */
    public function slaWindows(): array
    {
        return match ($this) {
            self::Urgent => ['first_response_hours' => 1, 'resolve_hours' => 4],
            self::High => ['first_response_hours' => 4, 'resolve_hours' => 24],
            self::Normal => ['first_response_hours' => 8, 'resolve_hours' => 72],
            self::Low => ['first_response_hours' => 24, 'resolve_hours' => 168],
        };
    }
}
