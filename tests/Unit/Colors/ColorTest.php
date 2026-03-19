<?php

declare(strict_types=1);

use Corepine\Support\Colors\Color;
use Corepine\Support\Facades\CorepineColor;

test('it exposes the built in color catalog', function (): void {
    expect(Color::catalog()['blue'])->toBe(Color::Blue)
        ->and(Color::catalog()['dark'])->toBe(Color::Dark)
        ->and(Color::hasPalette('amber'))->toBeTrue()
        ->and(Color::names())->toContain('blue', 'dark', 'rose')
        ->and(Color::get('blue', 400))->toBe(Color::Blue[400]);
});

test('it registers named colors and resolves shades', function (): void {
    CorepineColor::register([
        'primary' => Color::Blue,
        'danger' => 'red',
        'gray' => 'zinc',
        'dark' => 'dark',
    ]);

    expect(CorepineColor::primary())->toBe(Color::Blue[500])
        ->and(CorepineColor::danger(400))->toBe(Color::Red[400])
        ->and(CorepineColor::gray())->toBe(Color::Zinc[500])
        ->and(CorepineColor::dark())->toBe(Color::Dark[500]);
});

test('it falls back to built in palettes when a color is not registered', function (): void {
    expect(CorepineColor::palette('blue'))->toBe(Color::Blue)
        ->and(CorepineColor::get('blue', 400))->toBe(Color::Blue[400])
        ->and(CorepineColor::has('rose'))->toBeTrue();
});

test('it only returns runtime registrations from all', function (): void {
    CorepineColor::register([
        'primary' => Color::Blue,
        'danger' => 'red',
    ]);

    expect(CorepineColor::all())->toBe([
        'primary' => Color::Blue,
        'danger' => Color::Red,
    ]);
});

test('the helper resolves the shared color manager', function (): void {
    corepineColor()->set('success', 'green');

    expect(CorepineColor::success())->toBe(Color::Green[500]);
});

test('the helper supports fluent registration and updated primary lookups', function (): void {
    expect(
        corepineColor()->register([
            'primary' => Color::Blue,
        ])->primary(500)
    )->toBe(Color::Blue[500]);

    corepineColor()->set('primary', Color::Rose);

    expect(corepineColor()->primary(500))->toBe(Color::Rose[500])
        ->and(CorepineColor::primary(500))->toBe(Color::Rose[500]);
});

test('built in colors are dynamically accessible without registration', function (): void {
    expect(corepineColor()->purple(500))->toBe(Color::Purple[500])
        ->and(corepineColor()->rose(700))->toBe(Color::Rose[700]);
});

test('registered aliases are dynamically accessible', function (): void {
    CorepineColor::register([
        'brand' => Color::Fuchsia,
    ]);

    expect(corepineColor()->brand(500))->toBe(Color::Fuchsia[500])
        ->and(CorepineColor::brand(600))->toBe(Color::Fuchsia[600]);
});

test('registered colors can override built in palette names', function (): void {
    CorepineColor::set('purple', Color::Blue);

    expect(corepineColor()->purple(500))->toBe(Color::Blue[500]);
});
