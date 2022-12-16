<?php

return [
    'kspace' => [
        'valueDistanceMultiplier' => 0.3,
    ],
    'primary' => [
        // primary color is chosen by sum of:
        'saturationMultiplier' => 5, // * normalized color saturation <this sat / max sat of all pixels>
        'valueMultiplier' => 5, // * normalized color value <this val / max val of all pixels>
        'countMultiplier' => 1,  // * normalized pixels count <this count / max count of all clusters>
    ],
    'secondary' => [
        'saturationMultiplier' => 4,
        'valueMultiplier' => 2,
        'countMultiplier' => 3,

        'priDistanceMultiplier' => 6, // color difference <distance in k-space from primary>
        'priScoreDifferenceMultiplier' => 0.33, // subtract color's primary score * x from secondary
    ],

    // scores are lowered by multiplier when normalized threshold is below these /to elimitate dark or grayish colors/:
    'saturationLowThreshold' => 0.3,
    'saturationLowMultiplier' => 0.75,
    'valueLowThreshold' => 0.1,
    'valueLowMultiplier' => 0.55,
];
