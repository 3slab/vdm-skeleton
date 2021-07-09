<?php

namespace App\MessageHandler;

use App\Message\OpenWeatherMapComputeMessage;
use App\Message\OpenWeatherMapStoreMessage;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class OpenWeatherMapComputeMessageHandler
 * @package App\MessageHandler
 */
class OpenWeatherMapComputeMessageHandler implements MessageSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    protected $bus;

    /**
     * OpenWeatherMapComputeMessageHandler constructor.
     * @param MessageBusInterface $bus
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param OpenWeatherMapComputeMessage $message
     */
    public function __invoke(OpenWeatherMapComputeMessage $message)
    {
        $data = $message->getPayload();

        $date = \DateTime::createFromFormat('U', $data['dt'], new \DateTimeZone('UTC'));
        $formattedData = [
            'id' => sprintf('%s-%f-%f', $date->format(\DateTime::ATOM), $data['long'], $data['lat']),
            'date' => $date,
            'long' => (float) $data['long'],
            'lat' => (float) $data['lat'],
            'temperature' => (float) $data['temp'],
            'temperature_felt' => (float) $data['feels_like'],
            'pressure' => $data['pressure'],
            'humidity' => $data['humidity'],
        ];

        if (isset($data['weather']) && isset($data['weather'][0]) && isset($data['weather'][0]['main'])) {
            $formattedData['weather'] = $data['weather'][0]['main'];
        }

        $outputMessage = OpenWeatherMapStoreMessage::createFrom($message, $formattedData);
        $this->bus->dispatch($outputMessage);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield OpenWeatherMapComputeMessage::class => [
            'from_transport' => 'openweathermap-compute'
        ];
    }
}
