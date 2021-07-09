<?php

namespace App\MessageHandler;

use App\Message\OpenWeatherMapComputeMessage;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Vdm\Bundle\LibraryHttpTransportBundle\Message\HttpMessage;

/**
 * Class OpenWeatherMapCollectHttpMessageHandler
 * @package App\MessageHandler
 */
class OpenWeatherMapCollectHttpMessageHandler implements MessageSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    protected $bus;

    /**
     * OpenWeatherMapCollectHttpMessageHandler constructor.
     * @param MessageBusInterface $bus
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param HttpMessage $message
     */
    public function __invoke(HttpMessage $message)
    {
        $data = $message->getPayload();

        foreach ($data['hourly'] as $hourlyData) {
            $hourlyData['lat'] = $data['lat'];
            $hourlyData['long'] = $data['lon'];

            $toComputeMessage = OpenWeatherMapComputeMessage::createFrom($message, $hourlyData);
            $this->bus->dispatch($toComputeMessage);
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield HttpMessage::class => [
            'from_transport' => 'openweathermap-collect'
        ];
    }
}
