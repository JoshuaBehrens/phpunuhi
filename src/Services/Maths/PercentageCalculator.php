<?php

namespace PHPUnuhi\Services\Maths;

class PercentageCalculator
{

    /**
     * @param int $numberExisting
     * @param int $numberTotal
     * @return float
     */
    public function getRoundedPercentage(int $numberExisting, int $numberTotal): float
    {
        if ($numberExisting === 0 && $numberTotal === 0) {
            return 100;
        }

        if ($numberTotal === 0) {
            return 0;
        }

        $percent = ($numberExisting / $numberTotal) * 100;

        return round($percent, 2);
    }

}
