<?php

declare(strict_types=1);

namespace Corepine\Support\Facades;

use Closure;
use Corepine\Support\Colors\ColorManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Corepine\Support\Colors\ColorManager set(string $name, array|string|Closure $palette)
 * @method static \Corepine\Support\Colors\ColorManager forget(string $name)
 * @method static \Corepine\Support\Colors\ColorManager flush()
 * @method static array<string, array<int|string, string>> all()
 * @method static array<string, array<int|string, string>> registered()
 * @method static array<string, array<int|string, string>> getColors()
 * @method static array<int|string, string>|null palette(string $name)
 * @method static string|null get(string $name, int $shade = 500)
 * @method static bool has(string $name)
 * @method static string|null primary(int $shade = 500)
 * @method static string|null danger(int $shade = 500)
 * @method static string|null success(int $shade = 500)
 * @method static string|null warning(int $shade = 500)
 * @method static string|null info(int $shade = 500)
 * @method static string|null gray(int $shade = 500)
 * @method static string|null dark(int $shade = 500)
 *
 * Dynamic color access is also supported through the manager:
 * `CorepineColor::purple(500)` or `CorepineColor::brand(500)`.
 *
 * @see \Corepine\Support\Colors\ColorManager
 */
class CorepineColor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ColorManager::class;
    }

    /**
     * @param  array<string, array<int|string, string>|string|Closure>|Closure  $colors
     */
    public static function register(array | Closure $colors): void
    {
        static::resolved(function (ColorManager $colorManager) use ($colors): void {
            $colorManager->register($colors);
        });
    }
}
