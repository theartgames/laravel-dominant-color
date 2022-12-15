<?php

namespace CapsulesCodes\DominantColor;

use CapsulesCodes\DominantColor\Utils\ColorConversion;

class Color
{
    public function __construct(
        protected array $kmeansOutput,
        protected float $secondaryMaxScore = 0.0
    ) {
    }

    /**
     * Return Score to be able to compare colors to create the palette
     *
     * @return float
     */
    public function score(): float
    {
        if ($this->secondaryMaxScore == 0.0) {
            throw new \Exception('Secondary max score is not set');
        }

        return $this->kmeansOutput['s_score'] / $this->secondaryMaxScore;
    }

    /**
     * Gives the count of pixels of that color in the image
     *
     * @return int
     */
    public function count(): int
    {
        return $this->kmeansOutput['count'];
    }

    /**
     * Color in RGB concatenated to be able to compare it with other colors
     *
     * @return void
     */
    public function color(): int
    {
        return $this->kmeansOutput['color'];
    }

    /**
     * Primary score to find the primary color
     *
     * @return float
     */
    public function primaryScore(): float
    {
        return $this->kmeansOutput['p_score'];
    }

    /**
     * Secondary score to find the secondary color
     *
     * @return float
     */
    public function secondaryScore() : float
    {
        return $this->kmeansOutput['s_score'];
    }

    /**
     * Convert colors to RGB
     *
     * @return array
     */
    public function toRGB(): array
    {
        $rgb = ColorConversion::hsv2rgb($this->kmeansOutput['h'], $this->kmeansOutput['s'], $this->kmeansOutput['v']);

        return [
            'r' => $rgb[0],
            'g' => $rgb[1],
            'b' => $rgb[2],
        ];
    }

    /**
     * Convert colors to HSV
     *
     * @return array
     */
    public function toHSV(): array
    {
        return [
            'h' => $this->kmeansOutput['h'],
            's' => $this->kmeansOutput['s'],
            'v' => $this->kmeansOutput['v'],
        ];
    }

    /**
     * Convert colors to Hexadecimal
     *
     * @param  bool  $withHash
     * @return string
     */
    public function toHexadecimal(bool $withHash = true): string
    {
        $hexadecimal = ColorConversion::hsv2hex($this->kmeansOutput['h'], $this->kmeansOutput['s'], $this->kmeansOutput['v']);

        return $withHash ? '#'.$hexadecimal : $hexadecimal;
    }
}
