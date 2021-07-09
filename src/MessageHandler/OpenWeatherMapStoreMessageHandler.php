<?php

namespace App\MessageHandler;

use App\Message\OpenWeatherMapPersistMessage;
use App\Message\OpenWeatherMapStoreMessage;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class OpenWeatherMapStoreMessageHandler
 * @package App\MessageHandler
 */
class OpenWeatherMapStoreMessageHandler implements MessageSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    protected $bus;

    /**
     * OpenWeatherMapStoreMessageHandler constructor.
     * @param MessageBusInterface $bus
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param OpenWeatherMapStoreMessage $message
     */
    public function __invoke(OpenWeatherMapStoreMessage $message)
    {
        $outputMessage = OpenWeatherMapPersistMessage::createFrom($message);
        $this->bus->dispatch($outputMessage);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield OpenWeatherMapStoreMessage::class => [
            'from_transport' => 'openweathermap-store'
        ];
    }
}
