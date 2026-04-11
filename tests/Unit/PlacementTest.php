<?php

declare(strict_types=1);

use Corepine\Support\Enums\Placement;

it('defines the shared placement enum values', function (): void {
    expect(Placement::Center->value)->toBe('center')
        ->and(Placement::Top->value)->toBe('top')
        ->and(Placement::Bottom->value)->toBe('bottom')
        ->and(Placement::Left->value)->toBe('left')
        ->and(Placement::Right->value)->toBe('right');
});

it('normalizes placement values and synonyms', function (): void {
    expect(Placement::fromValue('middle'))->toBe(Placement::Center)
        ->and(Placement::fromValue(' left '))->toBe(Placement::Left)
        ->and(Placement::fromValue(Placement::Right))->toBe(Placement::Right)
        ->and(Placement::fromValue('invalid'))->toBeNull()
        ->and(Placement::normalize('invalid'))->toBe(Placement::Center)
        ->and(Placement::normalize('invalid', Placement::Bottom))->toBe(Placement::Bottom)
        ->and(Placement::Left->originClass())->toBe('origin-left')
        ->and(Placement::Top->isVertical())->toBeTrue()
        ->and(Placement::Left->isHorizontal())->toBeTrue()
        ->and(Placement::Center->isEdge())->toBeFalse();
});