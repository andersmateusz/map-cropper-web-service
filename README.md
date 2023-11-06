# About
This application is web server that uses Simple Object Access Protocol (SOAP) to crop provided map image.
You can also access the map image in your browser to test its functionality.
As server, it uses modern PHP app server built on top of the Caddy web server, which is the FrankenPHP. You can read more about it [here](https://frankenphp.dev/).

# Prerequisites
- Docker Compose
# Quick start
Run `docker compose up -d --build` from root folder of the application.
Then run `./bin/composer.sh install` to install required dependencies.
Go to http://localhost:65089 in your browser.
You can crop provided map image by providing query params.
There are 4 query params:
- x1 - x coordinate of top left corner of the desired image
- y1 - y coordinate of top left corner of the desired image
- x2 - x coordinate of bottom right corner of the desired image
- y2 - y coordinate of bottom right corner of the desired image

To use it with SOAP you need to create your own SOAP client. You can find sample client inside `test/SoapTest.php`. Inside `public/map_cropper.wsdl` you can find description of the service. 
# Configuration
You can change all parameters inside `config/config.php` file.
## Changing server URI
You can change application URI by providing `$serverUri` argument. Default value is http://localhost:65089.
If you want to change the URI remember to change also `SERVER_NAME` env variable inside `docker.env` file and port binding accordingly in `docker-compose.yml` file.
Server location inside `public/map_cropper.wsdl` must be changed also.
## Changing map image
You can change the path to the map image by providing `$mapImageName` argument. Default value is `var/szczecin.png`, which is map of Polish city Szczecin.
The image must be in png format. You must also change GPS coordinates of the map image accordingly.
## Environment type
When there is dev environment type, then server will show errors and will not cache responses.
When there is prod environment type, then server will not show errors and will cache responses.
# Testing
Run `./bin/run_test.sh` to run tests.

# Credits
Provided static map image `var/szczecin.png` is from [OpenStreetMap](https://www.openstreetmap.org/).
OpenStreetMap data is available under the Open Database License (ODbL).
You can read more about copyrights [here](https://www.openstreetmap.org/copyright/en).