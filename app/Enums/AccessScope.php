<?php

namespace App\Enums;

enum AccessScope: string
{
    case ALL = 'ALL';
    case DEPARTMENT = 'DEPARTMENT';
    case LOCATION = 'LOCATION';
    case TEAM = 'TEAM';

    public function label(): string
    {
        return match($this) {
            self::ALL => 'All',
            self::DEPARTMENT => 'Department',
            self::LOCATION => 'Location',
            self::TEAM => 'Team',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn (self $scope) => [
            'value' => $scope->value,
            'label' => $scope->label(),
        ], self::cases());
    }
}
