<?php

namespace com\cminds\seokeywords\plugin\helpers;

use com\cminds\seokeywords\App;

class TrendHelper {

    // https://halfelf.org/2017/linear-regressions-php/
    public static function getLinearRegression($data) {

        $x = array_keys($data);
        $y = array_values($data);

        $n = count($x);     // number of items in the array
        $x_sum = array_sum($x); // sum of all X values
        $y_sum = array_sum($y); // sum of all Y values

        $xx_sum = 0;
        $xy_sum = 0;

        for ($i = 0; $i < $n; $i++) {
            $xy_sum += ( $x[$i] * $y[$i] );
            $xx_sum += ( $x[$i] * $x[$i] );
        }

        $slope = 0;
        $intercept = 0;

        if (( $n * $xx_sum ) != ( $x_sum * $x_sum )) {
            $slope = ( ( $n * $xy_sum ) - ( $x_sum * $y_sum ) ) / ( ( $n * $xx_sum ) - ( $x_sum * $x_sum ) );
            $intercept = ( $y_sum - ( $slope * $x_sum ) ) / $n;
        }

        return array(
            'slope' => $slope,
            'intercept' => $intercept,
        );
    }

}
