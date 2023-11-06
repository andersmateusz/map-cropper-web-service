<?php

declare(strict_types=1);

namespace Andersma\MapCropperWebService;

class Configuration
{
    /**
     * @param string $mapImageName Fully qualified path name to a .png image
     * @param float $bottomLatitude Latitude of lower-right corner of the provided map image
     * @param float $topLatitude Latitude of upper-left corner of the provided map image
     * @param float $rightLongitude Longitude of lower-right corner of the provided map image
     * @param float $leftLongitude Longitude of the upper-right corner of the provided map image
     */
    public function __construct(
        private readonly string $mapImageName,
        private readonly float  $bottomLatitude,
        private readonly float  $topLatitude,
        private readonly float  $rightLongitude,
        private readonly float  $leftLongitude,
        private readonly string $serverUri,
        private readonly EnvironmentType $environmentType = EnvironmentType::PROD,
    ) {
    }

    public function getMapImageName(): string
    {
        return $this->mapImageName;
    }

    public function getBottomLatitude(): float
    {
        return $this->bottomLatitude;
    }

    public function getTopLatitude(): float
    {
        return $this->topLatitude;
    }

    public function getRightLongitude(): float
    {
        return $this->rightLongitude;
    }

    public function getLeftLongitude(): float
    {
        return $this->leftLongitude;
    }

    public function getLongWidth(): float
    {
        return $this->rightLongitude - $this->leftLongitude;
    }

    public function getLatHeight(): float
    {
        return $this->bottomLatitude - $this->topLatitude;
    }

    public function getEnvironmentType(): EnvironmentType
    {
        return $this->environmentType;
    }

    public function getServerUri(): string
    {
        return $this->serverUri;
    }
}