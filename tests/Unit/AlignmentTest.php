<?php

declare(strict_types=1);

use Corepine\Support\Enums\Alignment;

it('defines the shared alignment enum values', function (): void {
    expect(Alignment::Start->value)->toBe('start')
        ->and(Alignment::Center->value)->toBe('center')
        ->and(Alignment::Right->value)->toBe('end');
});
