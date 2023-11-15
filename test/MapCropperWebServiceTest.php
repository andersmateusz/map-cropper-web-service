<?php

declare(strict_types=1);

namespace Andersma\MapCropperWebService\Tests;

use Andersma\MapCropperWebService\MapCropperWebService;
use PHPUnit\Framework\TestCase;

final class MapCropperWebServiceTest extends TestCase
{
    private MapCropperWebService $service;
    protected function setUp(): void
    {
        $this->service = new MapCropperWebService();
    }

    /**
     * @dataProvider pixelProvider
     */
    public function testByPixelCoordinates(
        int $x1,
        int $y1,
        int $x2,
        int $y2,
        int $width,
        int $height,
    ): void {
        $image = \imagecreatefromstring($this->service->byPixelCoordinates($x1, $y1, $x2, $y2));

        self::assertEquals($width, \imagesx($image));
        self::assertEquals($height, \imagesy($image));
    }

    public function testByGpsCoordinates(): void
    {
        $image = \imagecreatefromstring($this->service->byGpsCoordinates(53.4451, 14.52665, 53.4200, 14.56868));
        self::assertEquals(1000, \imagesx($image));
        self::assertEquals(1000, \imagesy($image));
    }


    /** @return iterable<string, array{int, int, int, int, int, int}> */
    public static function pixelProvider(): iterable
    {
        yield 'No cropping' => [0, 0, 1000, 1000, 1000, 1000];
        yield 'X1 higher than width' => [1001, 0, 1000, 1000, 1000, 1000];
        yield 'X1 lower than zero' => [-5, 0, 100, 100, 100, 100];
        yield 'Y1 higher than height' => [0, 1001, 100, 100, 100, 100];
        yield 'Y1 lower than zero' => [0, -5, 100, 100, 100, 100];
        yield 'X2 higher than width' => [0, 0, 1001, 1000, 1000, 1000];
        yield 'X2 lower than zero' => [0,  0, -1000, 100, 1000, 100];
        yield 'Y2 higher than height' => [0, 0, 100, 1001, 100, 1000];
        yield 'Y2 lower than zero' => [0, 0, 100, -5, 100, 1000];
        yield 'Image cropped in whole' => [0 , 0, 0, 0, 1000, 1000];

    }
}
