<?php

declare(strict_types=1);

use Andersma\MapCropperWebService\ClientType;
use Andersma\MapCropperWebService\Configuration;
use Andersma\MapCropperWebService\Container;
use Andersma\MapCropperWebService\EnvironmentType;
use Andersma\MapCropperWebService\MapCropperWebService;

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../config/config.php';

/** @var Configuration $config */
$config = Container::get(Configuration::class);

if (EnvironmentType::DEV === $config->getEnvironmentType()) {
    \error_reporting(\E_ALL);
    \ini_set('display_errors', 1);
}

if (EnvironmentType::DEV === $config->getEnvironmentType()) {
    \file_put_contents(__DIR__ . '/../var/request_log', \sprintf("[%s %s %s]\n%s\n", \date('Y-m-d H:i:s'), $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], \file_get_contents('php://input')), \FILE_APPEND);
}

if (!\in_array(\parse_url($_SERVER['REQUEST_URI'], \PHP_URL_PATH), ['/', '/index.php'])) {
    \http_response_code(404);
    exit;
}

if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? null)) {
    $options = array('uri' => $config->getServerUri(), 'cache_wsdl' => EnvironmentType::DEV === $config->getEnvironmentType() ? \WSDL_CACHE_NONE : \WSDL_CACHE_MEMORY);
    $server = new \SoapServer('map_cropper.wsdl', $options);
    $server->setObject(Container::getOrNew(MapCropperWebService::class));
    $server->handle();
    \http_response_code(404);
    exit;
} else if ('GET' === ($_SERVER['REQUEST_METHOD'] ?? null)) {
    $filterQueryParam = static fn (string $paramName, int $default): int => \filter_var($_GET[$paramName] ?? null, \FILTER_VALIDATE_INT, \FILTER_NULL_ON_FAILURE) ?? $default;
    $img = Container::getOrNew(MapCropperWebService::class)
        ->byPixelCoordinates(
            $filterQueryParam('x1', 0),
            $filterQueryParam('y1', 0),
            $filterQueryParam('x2', 1000),
            $filterQueryParam('y2', 1000),
            ClientType::BROWSER
        );
    header('Content-Type: image/png');
    echo $img;
}