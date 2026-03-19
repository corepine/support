<?php

declare(strict_types=1);

use Corepine\Support\Colors\ColorManager;
use Corepine\Support\Facades\CorepineColor;

if (! function_exists('corepineColor')) {
    /**
     * Get the Corepine color manager instance.
     */
    function corepineColor(): ColorManager
    {
        /** @var ColorManager $manager */
        $manager = CorepineColor::getFacadeRoot();

        return $manager;
    }
}
