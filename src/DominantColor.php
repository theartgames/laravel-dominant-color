<?php

namespace CapsulesCodes\DominantColor;

use CapsulesCodes\DominantColor\Utils\ColorConversion;
use KMeans\Cluster;
use KMeans\Space;

class DominantColor
{
    /* define how many pixels horizontally and vertically are taken into account
        100x100 is more than enough to get colors from album covers
    */
    public function __construct(
        protected int $areaPixelWidth = 100,
        protected int $areaPixelHeight = 100
    ) {
    }

    public function fromGD($gdImage, int $colorCount = 2): ColorPalette
    {
        $colorCount = max($colorCount, 2); // at least 2 colors - primary and secondary

        $space = $this->imageToKSpace($gdImage);

        $clusters = $space->solve(
            nbClusters: $colorCount,
            initMethod: Cluster::INIT_KMEANS_PLUS_PLUS
        );

        /* score calculation for primary and secondary dominant color */
        return new ColorPalette($this->createScoreArray($clusters));
    }

    public function fromFile(string $fileName, int $colorCount = 2): ColorPalette
    {
        $gdImg = imagecreatefromstring(file_get_contents($fileName));

        if (! $gdImg) {
            throw new \Exception("Could not load image from file $fileName");
        }

        $colorInfo = $this->fromGD($gdImg, $colorCount);
        imagedestroy($gdImg);

        return $colorInfo;
    }

    private function imageToKSpace($gdImage): Space
    {
        $imageWidth = imagesx($gdImage);
        $imageHeight = imagesy($gdImage);

        $xSkip = max($imageWidth / $this->areaPixelWidth, 1);
        $ySkip = max($imageHeight / $this->areaPixelHeight, 1);

        $space = new Space(3);

        // walk through the pixels
        for ($y = 0; $y < $imageHeight; $y += $ySkip) {
            for ($x = 0; $x < $imageWidth; $x += $xSkip) {
                $xRGB = imagecolorat($gdImage, floor($x), floor($y));
                $aRGB = ColorConversion::hex2rgb($xRGB);
                $aHSV = ColorConversion::rgb2hsv($aRGB[0], $aRGB[1], $aRGB[2]);

                // convert HSV to coordinates in cone
                $pr = $aHSV[1] * $aHSV[2]; // radius

                $px = sin($aHSV[0] * 2 * M_PI) * $pr;
                $py = cos($aHSV[0] * 2 * M_PI) * $pr;
                $pz = $aHSV[2] * config('dominant-color.kspace.valueDistanceMultiplier');

                $space->addPoint([$px, $py, $pz], [$aHSV, $xRGB]);
            }
        }

        return $space;
    }

    private function createScoreArray(array $clusters): array
    {
        $clusterScore = [];
        $maxCount = 0;
        $maxS = 0;
        $maxV = 0;

        foreach ($clusters as $i => $cluster) {
            if (! count($cluster)) {
                continue;
            }
            $closest = $cluster->getClosest($cluster);

            $colors = $closest->toArray()['data'];
            $aHSV = $colors[0];
            $xRGB = $colors[1];
            $clusterCount = count($cluster);

            $clusterScore[] = [
                'clusterObj' => $cluster,
                'color' => $xRGB,
                'h' => $aHSV[0],
                's' => $aHSV[1],
                'v' => $aHSV[2],
                'count' => $clusterCount,
            ];

            $maxCount = max($maxCount, $clusterCount);
            $maxS = max($maxS, $aHSV[1]);
            $maxV = max($maxV, $aHSV[2]);
        }

        if (! $maxS) {
            $maxS = 1;
        }
        if (! $maxV) {
            $maxV = 1;
        }

        return [
            'clusters' => $clusterScore,
            'maxCount' => $maxCount,
            'maxS' => $maxS,
            'maxV' => $maxV,
        ];
    }
}
