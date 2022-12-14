<?php

namespace CapsulesCodes\DominantColor;

use CapsulesCodes\DominantColor\Color;

class ColorPalette
{
    public function __construct(
        protected Color $primary,
        protected Color $secondary,
        protected array $palette
    ) {
    }

    public function primary(): Color
    {
        return $this->primary;
    }

    public function secondary(): Color
    {
        return $this->secondary;
    }

    public function palette(): array
    {
        return $this->palette;
    }

    public function completePalette(): array
    {
        return [
            $this->primary,
            $this->secondary,
            ...$this->palette
        ];
    }

    // public function addPrimary(Color $color): self
    // {
    //     $this->primary = $color;
    //     return $this;
    // }

    // public function addSecondary(Color $color): self
    // {
    //     $this->secondary = $color;
    //     return $this;
    // }
}
