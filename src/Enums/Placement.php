<?php

declare(strict_types=1);

namespace Corepine\Support\Enums;

enum Placement: string
{
    case Center = 'center';

    case Top = 'top';

    case Bottom = 'bottom';

    case Left = 'left';

    case Right = 'right';

    public static function fromValue(mixed $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (! is_string($value)) {
            return null;
        }

        return match (strtolower(trim($value))) {
            'center', 'middle' => self::Center,
            'top' => self::Top,
            'bottom' => self::Bottom,
            'left' => self::Left,
            'right' => self::Right,
            default => null,
        };
    }

    public static function normalize(mixed $value, ?self $fallback = null): self
    {
        return self::fromValue($value) ?? $fallback ?? self::Center;
    }

    public function originClass(): string
    {
        return "origin-{$this->value}";
    }

    public function isEdge(): bool
    {
        return $this !== self::Center;
    }

    public function isHorizontal(): bool
    {
        return in_array($this, [self::Left, self::Right], true);
    }

    public function isVertical(): bool
    {
        return in_array($this, [self::Top, self::Bottom], true);
    }
}