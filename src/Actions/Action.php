<?php

namespace Corepine\Support\Actions;

use Closure;
use Corepine\Support\Colors\Color as SupportColor;
use Corepine\Support\Facades\CorepineColor;

abstract class Action
{
    protected string $name;

    protected string|Closure|null $label = null;

    protected bool|Closure $visible = true;

    /**
     * @var array<int|string, string>|string|Closure|null
     */
    protected array|string|Closure|null $color = null;

    /**
     * @var array<string, mixed>|Closure
     */
    protected array|Closure $attributes = [];

    public function __construct(string $name)
    {
        $name = trim($name);
        $this->name = $name === '' ? 'action' : $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function label(string|Closure|null $label): static
    {
        if (is_string($label)) {
            $label = trim($label);
            $label = $label === '' ? null : $label;
        }

        $this->label = $label;

        return $this;
    }

    public function visible(bool|Closure $condition = true): static
    {
        $this->visible = $condition;

        return $this;
    }

    /**
     * @param  array<int|string, string>|string|Closure|null  $color
     */
    public function color(array|string|Closure|null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function primary(): static
    {
        return $this->color('primary');
    }

    public function danger(): static
    {
        return $this->color('danger');
    }

    public function success(): static
    {
        return $this->color('success');
    }

    public function warning(): static
    {
        return $this->color('warning');
    }

    public function info(): static
    {
        return $this->color('info');
    }

    public function gray(): static
    {
        return $this->color('gray');
    }

    public function dark(): static
    {
        return $this->color('dark');
    }

    /**
     * @param  array<string, mixed>|Closure  $attributes
     */
    public function attributes(array|Closure $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param  array<string, mixed>|Closure  $attributes
     */
    public function extraAttributes(array|Closure $attributes): static
    {
        return $this->attributes($attributes);
    }

    public function attribute(string $name, mixed $value = true): static
    {
        $name = trim($name);

        if ($name === '') {
            return $this;
        }

        $attributes = $this->evaluate($this->attributes);

        if (! is_array($attributes)) {
            $attributes = [];
        }

        $attributes[$name] = $value;
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function resolveLabel(string $fallback, array $context = []): string
    {
        $resolved = $this->evaluate($this->label, $context);

        if (is_string($resolved) && trim($resolved) !== '') {
            return trim($resolved);
        }

        return $fallback;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function resolveVisible(array $context = []): bool
    {
        return (bool) $this->evaluate($this->visible, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function resolveAttributes(array $context = []): array
    {
        $resolved = $this->evaluate($this->attributes, $context);

        return is_array($resolved) ? $resolved : [];
    }

    /**
     * @return array<int|string, string>|null
     */
    protected function resolveColor(mixed $color): ?array
    {
        if (is_array($color)) {
            $normalized = [];

            foreach ($color as $shade => $value) {
                if (! is_string($value)) {
                    continue;
                }

                $value = trim($value);

                if ($value === '') {
                    continue;
                }

                $normalized[is_int($shade) || ! ctype_digit((string) $shade) ? $shade : (int) $shade] = $value;
            }

            if ($normalized === []) {
                return null;
            }

            ksort($normalized);

            return $normalized;
        }

        if (! is_string($color)) {
            return null;
        }

        $color = trim($color);

        if ($color === '') {
            return null;
        }

        $primaryPalette = CorepineColor::palette($color);

        if (is_array($primaryPalette)) {
            return $primaryPalette;
        }

        return match (strtolower($color)) {
            'primary' => CorepineColor::palette('primary') ?? SupportColor::Blue,
            'danger' => SupportColor::Red,
            'success' => SupportColor::Green,
            'warning' => SupportColor::Yellow,
            'info' => SupportColor::Sky,
            'gray' => SupportColor::Gray,
            'dark' => SupportColor::Zinc,
            default => null,
        };
    }

    /**
     * @param  array<int|string, string>|null  $palette
     */
    protected function resolveColorName(mixed $rawColor, ?array $palette): ?string
    {
        $catalog = SupportColor::catalog();

        if (is_array($palette)) {
            foreach ($catalog as $name => $builtInPalette) {
                if ($builtInPalette === $palette) {
                    return $name;
                }
            }
        }

        if (! is_string($rawColor)) {
            return null;
        }

        $name = strtolower(trim($rawColor));

        if ($name === '') {
            return null;
        }

        return match ($name) {
            'primary' => $this->matchPrimaryPaletteName(),
            'danger' => 'red',
            'success' => 'green',
            'warning' => 'yellow',
            'info' => 'sky',
            'gray' => 'gray',
            'dark' => 'zinc',
            default => $palette !== null ? $this->matchPaletteName($palette) : null,
        };
    }

    /**
     * @param  array<int|string, string>  $palette
     */
    protected function paletteShade(array $palette, int $shade): ?string
    {
        return $palette[$shade] ?? $palette[500] ?? null;
    }

    /**
     * @param  array<int|string, string>  $palette
     */
    protected function actionSolidTextColor(array $palette): string
    {
        $baseColor = $this->paletteShade($palette, 500) ?? '';

        return $this->isLightActionColor($baseColor) ? '#18181b' : '#ffffff';
    }

    protected function isLightActionColor(string $color): bool
    {
        if (preg_match('/^oklch\(\s*([0-9.]+)/i', $color, $matches) === 1) {
            return (float) ($matches[1] ?? 0) >= 0.72;
        }

        if (preg_match('/^#([0-9a-f]{6})$/i', $color, $matches) === 1) {
            $hex = $matches[1];
            $red = hexdec(substr($hex, 0, 2));
            $green = hexdec(substr($hex, 2, 2));
            $blue = hexdec(substr($hex, 4, 2));

            return ((0.299 * $red) + (0.587 * $green) + (0.114 * $blue)) / 255 >= 0.65;
        }

        return false;
    }

    protected function matchPrimaryPaletteName(): ?string
    {
        $palette = CorepineColor::palette('primary');

        if (! is_array($palette)) {
            return null;
        }

        return $this->matchPaletteName($palette);
    }

    /**
     * @param  array<int|string, string>  $palette
     */
    protected function matchPaletteName(array $palette): ?string
    {
        foreach (SupportColor::catalog() as $name => $builtInPalette) {
            if ($builtInPalette === $palette) {
                return $name;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function evaluate(mixed $value, array $context = []): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        $context['action'] = $this;

        return app()->call($value, $context);
    }
}

