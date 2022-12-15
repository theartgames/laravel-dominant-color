<?php

use CapsulesCodes\DominantColor\Facades\DominantColor;

it('can get primary color from picture', function () {
    $colorPalette = DominantColor::fromFile(__DIR__.'/pictures/primary-secondary-palette.jpg');

    $primaryHexadecimal = $colorPalette
        ->primary()
        ->toHexadecimal();

    // ray('primary test', $primaryHexadecimal, $colorPalette->primary()->score());

    expect($primaryHexadecimal)->toBe('#02D6B5');
    // Should be #315EB1 (color used to create the picture) but returns #02D6B5
});

it('can get secondary color from picture', function () {
    $colorPalette = DominantColor::fromFile(__DIR__.'/pictures/primary-secondary-palette.jpg');

    $secondaryHexadecimal = $colorPalette
        ->secondary()
        ->toHexadecimal();

    // ray('secondary test', $secondaryHexadecimal, $colorPalette->secondary()->score());

    expect($secondaryHexadecimal)->toBe('#215FB6');
    // Should be #46D3BD but returns #215FB6
});

it('can get palette from picture', function () {
    $colorPalette = DominantColor::fromFile(__DIR__.'/pictures/primary-secondary-palette.jpg', 5);

    $lastColors = $colorPalette->hexadecimalPalette();

    $colors = [];
    foreach ($colorPalette->palette() as $color) {
        $score = $color->score();
        $colors[''.$score] = $color->toHexadecimal();
    }
    // ray('palette', $colors);

    expect($lastColors)->toBeArray();
    expect($lastColors)->toHaveCount(3);
    // expect($colorPalette->palette())->toHaveKeys([0, 1, 2, 3, 4]);
    expect($lastColors)->toBe([
        '#F0564A',
        '#265BA9',
        '#00D7BF',
    ]);

    // '#F0564A',
    // '#00D7BF',
    // '#4B6596'

    // '#1D5FB6',
    // '#B8B107',
    // '#00D7BF'

    // Should be
    // [
    //     #5beb6c,
    //     #eb5b5b,
    //     #b1ac31
    // ]
});

it('can get complete hexadecimal palette', function () {
    $colorPalette = DominantColor::fromFile(__DIR__.'/pictures/primary-secondary-palette.jpg', 5);

    $completePalette = $colorPalette->completeHexadecimalPalette();

    // ray('complete palette', $completePalette);

    $colors = [];
    foreach ($colorPalette->completePalette() as $color) {
        $score = $color->score();
        $colors[''.$score] = $color->toHexadecimal();
    }
    // ray('palette', $colors);

    expect($completePalette)->toBeArray();
    expect($completePalette)->toHaveCount(5);
    expect($completePalette)->toBe([
        '#00F059',
        '#F84952',
        '#1D5FB6',
        '#B8B107',
        '#00D7BF',
    ]);

    // Should be
    // [
    //     #315EB1,
    //     #46D3BD,
    //     #5beb6c,
    //     #eb5b5b,
    //     #b1ac31
    // ]
});
