<?php

declare(strict_types=1);

namespace Andersma\MapCropperWebService;

final class MapCropperWebService
{
    private int $width;
    private int $height;
    private \GdImage $image;
    private Configuration $config;

    public function __construct()
    {
        $this->config = Container::get(Configuration::class) ?? throw new \RuntimeException('Configuration is not set');

        if (!($img = \imagecreatefrompng($this->config->getMapImageName())) instanceof \GdImage) {
            throw  new \RuntimeException(\sprintf('Failed to create image from png "%s". Following error occurred: "%s"', $this->config->getMapImageName(), \error_get_last()['message'] ?? 'N/A'));
        }

        if (false === $size = \getimagesize($this->config->getMapImageName())) {
            throw new \RuntimeException(\sprintf('Failed to get "%s" image information. Following error occurred: "%s"', $this->config->getMapImageName(), \error_get_last()['message'] ?? 'N/A'));
        }

        $this->image = $img;
        list(0 => $this->width, 1 => $this->height) = $size;
    }

    public function imageInfo(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'lat1' => $this->config->getTopLatitude(),
            'lon1' => $this->config->getLeftLongitude(),
            'lat2' => $this->config->getBottomLatitude(),
            'lon2' => $this->config->getRightLongitude(),
        ];
    }

    /**
     * Get cropped image by given GPS coordinates.
     *
     * @param float $lat1 Latitude of upper-left corner of the desired image
     * @param float $lon1 Longitude of upper-left corner of the desired image
     * @param float $lat2 Latitude of lower-right corner of the desired image
     * @param float $lon2 Longitude of the lower-right corner of the image
     *
     * @return string Base64 encoded .png file content
     */
    public function byGpsCoordinates(float $lat1, float $lon1, float $lat2, float $lon2): string
    {
        $x1 = ($lon1 - $this->config->getLeftLongitude()) * ($this->width / $this->config->getLongWidth());
        $y1 = ($this->config->getTopLatitude() - $lat1) * ($this->height / $this->config->getLatHeight());
        $x2 = ($lon2 - $this->config->getLeftLongitude()) * ($this->width / $this->config->getLongWidth());
        $y2 = ($this->config->getTopLatitude() - $lat2) * ($this->height / $this->config->getLatHeight());

        return $this->byPixelCoordinates((int) $x1, (int) $y1, (int) $x2, (int) $y2);
    }

    /**
     * Get cropped image by given pixel coordinates.
     *
     * @param int $x1 X coordinate of upper-left corner of the desired image
     * @param int $y1 Y coordinate of upper-left corner of the desired image
     * @param int $x2 X coordinate of lower-right corner of the desired image
     * @param int $y2 Y coordinate of lower-right corner of the desired image
     *
     * @return string Base64 encoded .png file content
     */
    public function byPixelCoordinates(int $x1, int $y1, int $x2, int $y2, ClientType $clientType  = ClientType::SOAP): string
    {
        $x1 = ($x1 < $this->width && $x1 >= 0) ? $x1 : 0;
        $y1 = ($y1 < $this->height && $y1 >= 0) ? $y1 : 0;
        $width = ($x2 > $this->width || $x2 <= $x1) ? $this->width : $x2 - $x1;
        $height = ($y2 > $this->height || $y2 <= $y1) ? $this->height : $y2 - $y1;

        if (ClientType::SOAP === $clientType) {
            return \file_get_contents($this->crop($x1, $y1, $width, $height));

        }

        return \file_get_contents($this->crop($x1, $y1, $width, $height));
    }

    public function __destruct()
    {
        \imagedestroy($this->image);
    }

    /**
     * Crops the image by given params and returns fully qualified path name of the cropped image.
     *
     * @param int $x X coordinate of upper-left corner of the desired image
     * @param int $y Y coordinate of upper-left corner of the desired image
     * @param int $width Width of the desired image
     * @param int $height Height of the desired image
     *
     * @return string Fully qualified path to the cropped image
     */
    private function crop(int $x, int $y, int $width, int $height): string
    {
        $tmpName = \sys_get_temp_dir().'/'.\uniqid();
        $cropped = \imagecrop($this->image, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);

        if (false === $cropped) {
            throw new \RuntimeException(\sprintf('Failed to crop image "%s". Following error occurred: "%s".', $this->config->getMapImageName(), \error_get_last()['message'] ?? 'N/A'));
        }

        \imagepng($cropped, $tmpName);

        return $tmpName;
    }
}