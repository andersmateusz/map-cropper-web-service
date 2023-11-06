<?php

declare(strict_types=1);

namespace Andersma\MapCropperWebService\Tests;

use Andersma\MapCropperWebService\Configuration;
use Andersma\MapCropperWebService\Container;
use PHPUnit\Framework\TestCase;

class SoapTest extends TestCase
{
    private \SoapClient $client;

    protected function setUp(): void
    {
        /** @var Configuration $config */
        $config = Container::get(Configuration::class);
        $this->client = new \SoapClient(\rtrim($config->getServerUri(), '/') . '/map_cropper.wsdl', ['cache_wsdl' => \WSDL_CACHE_NONE]);
    }

    public function testByPixelCoordinatesResponds(): void
    {
        $encodedImage = $this->client->byPixelCoordinates(0, 0, 100, 100);
        self::assertIsString($encodedImage);
        self::assertNotEmpty($encodedImage);
    }

    public function testFunctionsReadProperly(): void
    {
        /** @var array<int, string> $functions */
        $functions = $this->client->__getFunctions();

        self::assertTrue(\in_array('base64Binary byGpsCoordinates(double $lat1, double $lon1, double $lat2, double $lon2)', $functions), '"byGpsCoordinates" operation not found or has wrong signature');
        self::assertTrue(\in_array('base64Binary byPixelCoordinates(int $x1, int $y1, int $x2, int $y2)', $functions), '"byPixelCoordinates" operation not found or has wrong signature');
        self::assertTrue(\in_array('list(int $width, int $height) imageInfo()', $functions), '"imageInfo" operation not found or has wrong signature');
    }

    public function testByGpsCoordinatesResponds(): void
    {
        $encodedImage = $this->client->byGpsCoordinates(50.0, 14.0, 49.0, 15.0);
        self::assertIsString($encodedImage);
        self::assertNotEmpty($encodedImage);
    }

    public function testImageInfoResponds(): void
    {
        $info = $this->client->imageInfo();
        self::assertIsArray($info);
        self::assertCount(2, $info);
        self::assertArrayHasKey('width', $info);
        self::assertArrayHasKey('height', $info);
        list('width' => $width, 'height' => $height) = $info;
        self::assertIsInt($width);
        self::assertIsInt($height);
    }
}