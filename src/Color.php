<?php

namespace CapsulesCodes\DominantColor;

use CapsulesCodes\DominantColor\Utils\ColorConversion;

class Color
{
    public function __construct(protected array $kmeansOutput)
    {
    }

    public function score(): float
    {
        return $this->kmeansOutput['s_score'];
    }

    public function toRGB()
    {
        $rgb = ColorConversion::hsv2rgb($this->kmeansOutput['h'], $this->kmeansOutput['s'], $this->kmeansOutput['v']);
        return [
            'r' => $rgb[0],
            'g' => $rgb[1],
            'b' => $rgb[2]
        ];
    }

    public function toHexadecimal(bool $withHash = true)
    {
        $hexadecimal = ColorConversion::hsv2hex($this->kmeansOutput['h'], $this->kmeansOutput['s'], $this->kmeansOutput['v']);

        return $withHash ? '#' . $hexadecimal : $hexadecimal;
    }

    public function toHSV(): array
    {
        return [
            'h' => $this->kmeansOutput['h'],
            's' => $this->kmeansOutput['s'],
            'v' => $this->kmeansOutput['v']
        ];
    }
}
