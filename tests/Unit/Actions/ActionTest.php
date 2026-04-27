<?php

declare(strict_types=1);

use Corepine\Support\Actions\Action as BaseAction;
use Corepine\Support\Colors\Color;
use Corepine\Support\Facades\CorepineColor;

final class SupportActionProbe extends BaseAction
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function exposedResolveLabel(string $fallback, array $context = []): string
    {
        return $this->resolveLabel($fallback, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function exposedResolveVisible(array $context = []): bool
    {
        return $this->resolveVisible($context);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function exposedResolveAttributes(array $context = []): array
    {
        return $this->resolveAttributes($context);
    }

    /**
     * @return array<int|string, string>|null
     */
    public function exposedResolveColor(mixed $color): ?array
    {
        return $this->resolveColor($color);
    }

    /**
     * @param  array<int|string, string>|null  $palette
     */
    public function exposedResolveColorName(mixed $rawColor, ?array $palette): ?string
    {
        return $this->resolveColorName($rawColor, $palette);
    }

    /**
     * @param  array<int|string, string>  $palette
     */
    public function exposedActionSolidTextColor(array $palette): string
    {
        return $this->actionSolidTextColor($palette);
    }
}

it('resolves name, label, visibility, and attributes from shared action state', function (): void {
    $action = new SupportActionProbe('   ');

    expect($action->getName())->toBe('action');

    $action
        ->label(fn (BaseAction $action): string => strtoupper($action->getName()))
        ->visible(fn (string $role): bool => $role === 'admin')
        ->attributes(fn (string $role, BaseAction $action): array => [
            'data-role' => $role,
            'data-name' => $action->getName(),
        ]);

    expect($action->exposedResolveLabel('Fallback'))->toBe('ACTION')
        ->and($action->exposedResolveVisible(['role' => 'admin']))->toBeTrue()
        ->and($action->exposedResolveVisible(['role' => 'guest']))->toBeFalse()
        ->and($action->exposedResolveAttributes(['role' => 'admin']))->toMatchArray([
            'data-role' => 'admin',
            'data-name' => 'action',
        ]);
});

it('falls back to default label and empty attributes when closures return invalid values', function (): void {
    $action = (new SupportActionProbe('save'))
        ->label(fn (): string => '  ')
        ->attributes(fn (): string => 'invalid');

    expect($action->exposedResolveLabel('Save'))->toBe('Save')
        ->and($action->exposedResolveAttributes())->toBe([]);
});

it('normalizes color aliases and resolves palette names', function (): void {
    CorepineColor::flush();
    CorepineColor::set('primary', Color::Amber);
    CorepineColor::set('brand', Color::Fuchsia);

    $action = new SupportActionProbe('save');

    $primaryPalette = $action->exposedResolveColor('primary');
    $dangerPalette = $action->exposedResolveColor('danger');
    $brandPalette = $action->exposedResolveColor('brand');

    expect($primaryPalette)->toBe(Color::Amber)
        ->and($dangerPalette)->toBe(Color::Red)
        ->and($brandPalette)->toBe(Color::Fuchsia)
        ->and($action->exposedResolveColorName('primary', $primaryPalette))->toBe('amber')
        ->and($action->exposedResolveColorName('danger', $dangerPalette))->toBe('red')
        ->and($action->exposedResolveColorName('brand', $brandPalette))->toBe('fuchsia');
});

it('appends single attributes and computes palette text contrast', function (): void {
    $action = (new SupportActionProbe('save'))
        ->attributes(['data-role' => 'admin'])
        ->attribute('data-testid', 'save-action');

    expect($action->exposedResolveAttributes())->toMatchArray([
        'data-role' => 'admin',
        'data-testid' => 'save-action',
    ]);

    expect($action->exposedActionSolidTextColor(Color::Amber))->toBe('#18181b')
        ->and($action->exposedActionSolidTextColor(Color::Zinc))->toBe('#ffffff');
});
