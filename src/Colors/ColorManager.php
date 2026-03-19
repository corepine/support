<?php

declare(strict_types=1);

namespace Corepine\Support\Colors;

use BadMethodCallException;
use Closure;

class ColorManager
{
    /**
     * @var array<string, array<int|string, string>>
     */
    protected array $colors = [];

    /**
     * @param  array<string, array<int|string, string>|string|Closure>|Closure  $map
     */
    public function register(array | Closure $map): static
    {
        $evaluatedMap = $this->evaluate($map);

        if (! is_array($evaluatedMap)) {
            return $this;
        }

        foreach ($evaluatedMap as $name => $palette) {
            if (! is_string($name)) {
                continue;
            }

            $this->set($name, $palette);
        }

        return $this;
    }

    /**
     * @param  array<int|string, string>|string|Closure  $palette
     */
    public function set(string $name, array|string|Closure $palette): static
    {
        $normalizedName = $this->normalizeName($name);

        if ($normalizedName === '') {
            return $this;
        }

        $resolvedPalette = $this->resolvePalette($palette);

        if ($resolvedPalette === null) {
            return $this;
        }

        $this->colors[$normalizedName] = $resolvedPalette;

        return $this;
    }

    public function forget(string $name): static
    {
        $normalizedName = $this->normalizeName($name);

        if ($normalizedName !== '') {
            unset($this->colors[$normalizedName]);
        }

        return $this;
    }

    public function flush(): static
    {
        $this->colors = [];

        return $this;
    }

    /**
     * Return only colors that were explicitly registered at runtime.
     *
     * @return array<string, array<int|string, string>>
     */
    public function all(): array
    {
        return $this->colors;
    }

    /**
     * @return array<string, array<int|string, string>>
     */
    public function registered(): array
    {
        return $this->all();
    }

    /**
     * Resolve all colors that are currently available.
     *
     * Registered aliases override built-in palette names when they share a key.
     *
     * @return array<string, array<int|string, string>>
     */
    public function getColors(): array
    {
        return array_merge(Color::catalog(), $this->colors);
    }

    /**
     * Resolve a palette from registered colors first, then built-in palettes.
     *
     * @return array<int|string, string>|null
     */
    public function palette(string $name): ?array
    {
        $normalizedName = $this->normalizeName($name);

        if ($normalizedName === '') {
            return null;
        }

        return $this->getColors()[$normalizedName] ?? null;
    }

    public function get(string $name, int $shade = 500): ?string
    {
        $palette = $this->palette($name);

        if ($palette === null) {
            return null;
        }

        return $palette[$shade] ?? $palette[500] ?? null;
    }

    public function has(string $name): bool
    {
        return $this->palette($name) !== null;
    }

    public function primary(int $shade = 500): ?string
    {
        return $this->get('primary', $shade);
    }

    public function danger(int $shade = 500): ?string
    {
        return $this->get('danger', $shade);
    }

    public function success(int $shade = 500): ?string
    {
        return $this->get('success', $shade);
    }

    public function warning(int $shade = 500): ?string
    {
        return $this->get('warning', $shade);
    }

    public function info(int $shade = 500): ?string
    {
        return $this->get('info', $shade);
    }

    public function gray(int $shade = 500): ?string
    {
        return $this->get('gray', $shade);
    }

    public function dark(int $shade = 500): ?string
    {
        return $this->get('dark', $shade);
    }

    /**
     * @param  array<int|string, string>|string|Closure  $palette
     * @return array<int|string, string>|null
     */
    protected function resolvePalette(array|string|Closure $palette): ?array
    {
        $palette = $this->evaluate($palette);

        if (is_string($palette)) {
            $catalogKey = $this->normalizeName($palette);

            return Color::catalog()[$catalogKey] ?? null;
        }

        $normalizedPalette = [];

        foreach ($palette as $shade => $value) {
            if (! is_string($value)) {
                continue;
            }

            $normalizedValue = trim($value);

            if ($normalizedValue === '') {
                continue;
            }

            $normalizedShade = is_int($shade) || ! ctype_digit((string) $shade)
                ? $shade
                : (int) $shade;

            $normalizedPalette[$normalizedShade] = $normalizedValue;
        }

        if ($normalizedPalette === []) {
            return null;
        }

        ksort($normalizedPalette);

        return $normalizedPalette;
    }

    protected function normalizeName(string $name): string
    {
        return strtolower(trim($name));
    }

    protected function evaluate(mixed $value): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        return app()->call($value);
    }

    public function __call(string $method, array $parameters): ?string
    {
        $name = $this->normalizeName($method);

        if (! $this->has($name)) {
            throw new BadMethodCallException(sprintf(
                'Color [%s] is not registered and does not exist in the built-in catalog.',
                $method,
            ));
        }

        $shade = $parameters[0] ?? 500;

        if (is_string($shade) && ctype_digit($shade)) {
            $shade = (int) $shade;
        }

        if (! is_int($shade)) {
            throw new BadMethodCallException(sprintf(
                'Color method [%s] expects an integer shade.',
                $method,
            ));
        }

        return $this->get($name, $shade);
    }
}
