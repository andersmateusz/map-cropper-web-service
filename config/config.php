<?php

use Andersma\MapCropperWebService\Configuration;
use Andersma\MapCropperWebService\Container;
use Andersma\MapCropperWebService\EnvironmentType;

require_once __DIR__ . '/../vendor/autoload.php';

Container::set(
    new Configuration(
    __DIR__ . '/../var/szczecin.png',
    53.4200,
    53.4451,
    14.56868,
    14.52665,
    'http://localhost:65089',
    EnvironmentType::DEV,
    )
);