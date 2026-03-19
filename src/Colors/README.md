# Corepine Colors

Corepine now separates the palette catalog from the runtime facade:

1. `Corepine\Support\Colors\Color` contains the built-in palettes and palette helpers.
2. `Corepine\Support\Facades\CorepineColor` registers aliases and resolves runtime colors.

## Import

```php
use Corepine\Support\Colors\Color;
use Corepine\Support\Facades\CorepineColor;
```

## Built-In Palettes

Each built-in palette is available as a constant:

```php
Color::Blue;
Color::Red;
Color::Amber;
Color::Zinc;
Color::Dark;
```

Each palette is an array of shades:

```php
$blue = Color::Blue;

$blue[500]; // oklch(...)
$blue[700]; // oklch(...)
```

## Register App Aliases

Register the colors you want your package or app to use:

```php
CorepineColor::register([
    'primary' => Color::Blue,
    'danger' => Color::Red,
    'success' => Color::Green,
    'warning' => Color::Amber,
    'dark' => Color::Dark,
    'info' => Color::Blue,
    'gray' => Color::Zinc,
]);
```

You can also register using a built-in palette name:

```php
CorepineColor::register([
    'primary' => 'blue',
    'danger' => 'red',
    'gray' => 'zinc',
]);
```

You can also register with a closure:

```php
CorepineColor::register(fn (): array => [
    'primary' => Color::Blue,
    'brand' => Color::Fuchsia,
]);
```

## Runtime Colors

Get a specific shade:

```php
CorepineColor::get('primary');      // shade 500
CorepineColor::get('primary', 600); // shade 600
```

Convenience shortcuts:

```php
CorepineColor::primary();
CorepineColor::danger();
CorepineColor::success();
CorepineColor::warning();
CorepineColor::info();
CorepineColor::gray();
CorepineColor::dark();
```

Get the full resolved palette:

```php
CorepineColor::palette('primary');
```

Check if a color exists:

```php
CorepineColor::has('primary');
CorepineColor::has('blue');
```

## Dynamic Access

Any built-in color or registered alias can be accessed fluently:

```php
corepineColor()->purple(500); // built-in palette
corepineColor()->rose(600);   // built-in palette
corepineColor()->primary(500); // registered alias
corepineColor()->brand(500);   // registered alias
```

That means:

- You do not need to register built-in colors like `purple` or `rose` just to use them.
- You do register semantic aliases like `primary`, `danger`, or `brand`.
- Registered aliases override built-in palette names if they share the same key.

## Built-In Catalog

```php
Color::catalog();
Color::names();
Color::hasPalette('blue');
Color::palette('blue');
Color::get('blue', 500);
```

This is useful when you want the palette data directly, without going through the runtime manager.

## Runtime State

`CorepineColor::all()` returns only the aliases you registered at runtime:

```php
CorepineColor::register([
    'primary' => Color::Blue,
    'danger' => Color::Red,
]);

CorepineColor::all();
// [
//     'primary' => [...],
//     'danger' => [...],
// ]
```

`CorepineColor::getColors()` returns the full resolved set:

```php
CorepineColor::getColors();
```

Reset everything:

```php
CorepineColor::flush();
```

Remove one alias:

```php
CorepineColor::forget('primary');
```

## Helper

If you prefer a helper instead of the facade:

```php
corepineColor()->set('primary', Color::Blue);
corepineColor()->get('primary', 500);
corepineColor()->primary(500);
corepineColor()->purple(500);
```

Fluent usage also works:

```php
corepineColor()
    ->register([
        'primary' => Color::Blue,
        'danger' => Color::Red,
    ])
    ->primary(500); // Blue 500

corepineColor()->set('primary', Color::Rose);

corepineColor()->primary(500); // Rose 500
```

## Typical Package Boot Example

```php
use Corepine\Support\Colors\Color;
use Corepine\Support\Facades\CorepineColor;

protected function bootColors(): void
{
    CorepineColor::register([
        'primary' => Color::Blue,
        'danger' => Color::Red,
        'success' => Color::Green,
        'warning' => Color::Amber,
        'dark' => Color::Dark,
        'info' => Color::Blue,
        'gray' => Color::Zinc,
    ]);
}
```

## Available Built-In Palette Names

`slate`, `gray`, `zinc`, `neutral`, `stone`, `dark`, `red`, `orange`, `amber`, `yellow`, `lime`, `green`, `emerald`, `teal`, `cyan`, `sky`, `blue`, `indigo`, `violet`, `purple`, `fuchsia`, `pink`, `rose`
