# VDM Skeleton

This is a skeleton project for a VDM dataflow and API based on Openweathermap API.

You need git, docker and docker-compose installed on your computer.

## Installation

Clone the project :

```shell script
git clone git@github.com:3slab/vdm-skeleton.git myproject
cd myproject
```

Create an OpenWeatherMap API with permissions on the API 
[https://api.openweathermap.org/data/2.5/onecall](https://openweathermap.org/api/one-call-api) and setup an 
`.env.secrets` file with this key :

```shell script
echo "OPENWEATHERMAP_API_KEY=my_openweathermap_api_key >> .env.secrets"
```

Then starts the containers :

```shell script
make up
```

Wait for the composer install to end in the API container (launched in the container entrypoint) and setup the db :

```shell script
make setup-db
```

## Docker

This project has the following containers :

* `vdm_skeleton_data_app` : container to run dataflow command (collect, compute, store)
* `vdm_skeleton_api_app` : container to run the API nginx + php-fpm webserver
* `vdm_skeleton_rabbitmq` : container to run the broker used by the dataflow
* `vdm_skeleton_postgres` : container to run the database
* `vdm_skeleton_adminer` : container to run a database simple gui

*Note : there is no volume setup for retention of collected data in these containers*

*Note : the data_app and api_app containers shares the same sources*

## VDM dataflow and API

We have a VDM dataflow with 3 nodes :

1. Collect

    Source Transport : HTTP `https://api.openweathermap.org/data/2.5/onecall`
    Source Message : `Vdm\Bundle\LibraryHttpTransportBundle\Message\HttpMessage`
    Handler : [App\MessageHandler\OpenWeatherMapCollectHttpMessageHandler](src/MessageHandler/OpenWeatherMapCollectHttpMessageHandler.php)
    Destination Transport : AMQP `amqp://guest:guest@vdm_skeleton_rabbitmq:5672/%2f` exchange `openweathermap_hourly`
    Destination Message : [App\Message\OpenWeatherMapComputeMessage](src/Message/OpenWeatherMapComputeMessage.php)

    Description : it calls the OpenWeatherMap API, send the response to the handler which loop over each hourly data,
    append the latitude and longitude then send it to the destination transport.

2. Compute

    Source Transport : AMQP `amqp://guest:guest@vdm_skeleton_rabbitmq:5672/%2f` queue `openweathermap_hourly`
    Source Message : [App\Message\OpenWeatherMapComputeMessage](src/Message/OpenWeatherMapComputeMessage.php)
    Handler : [App\MessageHandler\OpenWeatherMapComputeMessageHandler](src/MessageHandler/OpenWeatherMapComputeMessageHandler.php)
    Destination Transport : AMQP `amqp://guest:guest@vdm_skeleton_rabbitmq:5672/%2f` exchange `openweathermap_hourly_formated`
    Destination Message : [App\Message\OpenWeatherMapStoreMessage](src/Message/OpenWeatherMapStoreMessage.php)

    Description : For each message, extract the useful data and format an array matching the destination entity and
    send it to the destination transport


3. Store

    Source Transport : AMQP `amqp://guest:guest@vdm_skeleton_rabbitmq:5672/%2f` queue `openweathermap_hourly_formated`
    Source Message : [App\Message\OpenWeatherMapStoreMessage](src/Message/OpenWeatherMapStoreMessage.php)
    Handler : [App\MessageHandler\OpenWeatherMapStoreMessageHandler](src/MessageHandler/OpenWeatherMapStoreMessageHandler.php)
    Destination Transport : Doctrine ORM [App\Entity\Meteo](src/Entity/Meteo.php) Entity
    Destination Message : [App\Message\OpenWeatherMapPersistMessage](src/Message/OpenWeatherMapPersistMessage.php)

    Description : For each message, check if the entity exists in the db, if not it creates a new one, if yes, it 
    updates the existing one

The API is available on [http://localhost:5000](http://localhost:5000) and lists the `App\Entity\Meteo` entity.

## Usage

The [Makefile](./Makefile) provides a few shortcut to run helping command :

* `make shell-data` : connect a shell to the data container
* `make shell-api` : connect a shell to the api container
* `make dev-collect` : Run the collect command with the `openweathermapcollectdev` environment
* `make dev-compute` : Run the compute command with the `openweathermapcomputedev` environment
* `make dev-store` : Run the store command with the `openweathermapstoredev` environment
* `make local-collect` : Run the collect command with the `openweathermapcollectlocal` environment
* `make local-compute` : Run the compute command with the `openweathermapcomputelocal` environment
* `make local-store` : Run the store command with the `openweathermapstorelocal` environment

So in order to populate the db with data to test the API, you need to run these commands :

```shell script
make dev-collect
make dev-compute
make dev-store
```

The `local-*` commands allow you tu run each node of the dataflow with a special transport which simulate 
a message from the broker using definition in the [local](./local) folder.
