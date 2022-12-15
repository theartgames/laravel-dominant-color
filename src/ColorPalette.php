<?php

namespace CapsulesCodes\DominantColor;

use CapsulesCodes\DominantColor\Utils\ColorConversion;

class ColorPalette
{
    protected int $primaryIndex;

    protected int $secondaryIndex;

    protected array $palette;

    public function __construct(protected array $scores)
    {
        $this->findPrimaryColor($this->scores);
        $this->findSecondaryColor($this->scores);
        $this->findPalette($this->scores);
    }

    /**
     * Return primary color
     *
     * @return Color
     */
    public function primary(): Color
    {
        return new Color($this->scores['clusters'][$this->primaryIndex]);
    }

    /**
     * Return secondary color
     *
     * @return Color
     */
    public function secondary(): Color
    {
        // return $this->secondary;
        return new Color($this->scores['clusters'][$this->secondaryIndex]);
    }

    /**
     * Return palette without primary and secondary colors
     *
     * @return array<Color>
     */
    public function palette(): array
    {
        return $this->palette;
    }

    public function hexadecimalPalette(): array
    {
        return array_map(fn ($color) => $color->toHexadecimal(), $this->palette());
    }

    /**
     * Return complete palette with primary and secondary colors
     *
     * @return array<Color>
     */
    public function completePalette(): array
    {
        return [
            $this->primary(),
            $this->secondary(),
            ...$this->palette,
        ];
    }

    public function completeHexadecimalPalette(): array
    {
        return array_map(fn ($color) => $color->toHexadecimal(), $this->completePalette());
    }

    public function completeHexadecimalPaletteWithProbability(): array
    {
        $hexadecimalCompletePalette = array_map(fn ($color) => $color->toHexadecimal(), $this->completePalette());;
        $probabilityCompletePalette = array_map(fn ($color) => $color->score(), $this->completePalette());
        return array_combine($probabilityCompletePalette, $hexadecimalCompletePalette);
    }

    private function findPrimaryColor() : void
    {
        foreach ($this->scores['clusters'] as &$cluster) {
            [$sf, $vf, $cf] = $this->normalizeColor($cluster);
            $scorePrimary = $sf * config('dominant-color.primary.saturationMultiplier');
            $scorePrimary += $vf * config('dominant-color.primary.valueMultiplier');
            $scorePrimary += $cf * config('dominant-color.primary.countMultiplier');
            $cluster['p_score'] = $scorePrimary;

            if ($cluster['s'] < $this->scores['maxS'] * config('dominant-color.saturationLowThreshold')) {
                $cluster['p_score'] *= config('dominant-color.saturationLowMultiplier');
            }
            if ($cluster['v'] < $this->scores['maxV'] * config('dominant-color.valueLowThreshold')) {
                $cluster['p_score'] *= config('dominant-color.valueLowMultiplier');
            }
        }

        $maxPScore = 0;
        $primaryIdx = 0;

        array_walk($this->scores['clusters'], function ($cluster, $index) use (&$maxPScore, &$primaryIdx) {
            if ($cluster['p_score'] > $maxPScore) {
                $maxPScore = $cluster['p_score'];
                $primaryIdx = $index;
            }
        });
        $this->scores['primary'] = ['maxScore' => $maxPScore, 'idx' => $primaryIdx];

        $this->primaryIndex = $primaryIdx;
    }

    private function findSecondaryColor(): void
    {
        $maxSScore = 0;
        $secondaryIdx = 0;

        $primary = $this->scores['clusters'][$this->scores['primary']['idx']];

        array_walk($this->scores['clusters'], function (&$cluster, $index) use (&$maxSScore, &$secondaryIdx, $primary) {
            if ($index == $this->scores['primary']['idx']) { // primary != secondary
                $cluster['s_score'] = 0;

                return;
            }
            [$sf, $vf, $cf] = $this->normalizeColor($cluster);

            $distPrimary = $cluster['clusterObj']->getDistanceWith($primary['clusterObj']);

            $cluster['s_score'] = $sf * config('dominant-color.secondary.saturationMultiplier');
            $cluster['s_score'] += $vf * config('dominant-color.secondary.valueMultiplier');
            $cluster['s_score'] *= ($cf * config('dominant-color.secondary.countMultiplier') + $distPrimary * config('dominant-color.secondary.priDistanceMultiplier'));
            $cluster['s_score'] -= $cluster['p_score'] * config('dominant-color.secondary.priScoreDifferenceMultiplier');

            if ($sf < config('dominant-color.saturationLowThreshold')) {
                $cluster['s_score'] *= config('dominant-color.saturationLowMultiplier');
            }
            if ($vf < config('dominant-color.valueLowThreshold')) {
                $cluster['s_score'] *= config('dominant-color.valueLowMultiplier');
            }

            if ($cluster['s_score'] > $maxSScore) {
                $maxSScore = $cluster['s_score'];
                $secondaryIdx = $index;
            }
        });

        $this->scores['secondary'] = ['maxScore' => $maxSScore, 'idx' => $secondaryIdx];
        $this->secondaryIndex = $secondaryIdx;
    }

    private function findPalette(): void
    {
        $palette = [];
        foreach ($this->scores['clusters'] as &$cluster) {
            if ($cluster['color'] != $this->primary()->color() && $cluster['color'] != $this->secondary()->color()) {
                $palette[] = new Color($cluster, secondaryMaxScore: $this->scores['secondary']['maxScore']);
            }
        }
        usort($palette, function ($a, $b) {
            return $b->score() <=> $a->score();
        });

        $this->palette = $palette;
    }

    private function normalizeColor(array $cluster): array
    {
        $sf = $cluster['s'] / $this->scores['maxS'];
        $vf = $cluster['v'] / $this->scores['maxV'];
        $cf = $cluster['count'] / $this->scores['maxCount'];
        $sf *= $this->nlcurve($vf); //decrease saturation for dark colors

        return [$sf, $vf, $cf];
    }

    private function nlcurve($x)
    {
        // 0>0 0.1>0.05 0.5>0.75 0.8>0.95 1>1
        // 5.85317x^4−13.254x^3+8.6379x^2−0.237103x
        return ColorConversion::clamp(5.85317 * $x ** 4 - 13.254 * $x ** 3 + 8.6379 * $x ** 2 - 0.237103 * $x, 0, 1);
    }
}
