<?php

namespace App\Enums;

enum Module: string
{
    case EMPLOYEE = 'EMPLOYEE';
    case SETTINGS = 'SETTINGS';

    public function label(): string
    {
        return match($this) {
            self::EMPLOYEE => 'Employee',
            self::SETTINGS => 'Settings',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn (self $module) => [
            'value' => $module->value,
            'label' => $module->label(),
        ], self::cases());
    }
}
