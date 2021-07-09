<?php

namespace App\Tests\MessageHandler;

use App\Message\OpenWeatherMapComputeMessage;
use App\Message\OpenWeatherMapPersistMessage;
use App\Message\OpenWeatherMapStoreMessage;
use App\MessageHandler\OpenWeatherMapCollectHttpMessageHandler;
use App\MessageHandler\OpenWeatherMapComputeMessageHandler;
use App\MessageHandler\OpenWeatherMapStoreMessageHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Vdm\Bundle\LibraryHttpTransportBundle\Message\HttpMessage;

class OpenWeatherMapStoreMessageHandlerTest extends TestCase
{
    public function testInvoke()
    {
        $middleware = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $middleware
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function ($message) {
                    $this->assertInstanceOf(OpenWeatherMapPersistMessage::class, $message);
                    $this->assertEquals('2021-07-01T12:00:00+00:00-2.348800-48.853400', $message->getPayload()['id']);
                    $this->assertEquals('2021-07-01T12:00:00+00:00', $message->getPayload()['date']);
                    $this->assertEquals(1.0, $message->getPayload()['lat']);
                    $this->assertEquals(2.0, $message->getPayload()['long']);
                    $this->assertEquals(456.0, $message->getPayload()['temperature']);
                    $this->assertEquals(87.4, $message->getPayload()['temperature_felt']);
                    $this->assertEquals(101, $message->getPayload()['pressure']);
                    $this->assertEquals(86, $message->getPayload()['humidity']);
                    return true;
                })
            )
            ->willReturn(new Envelope(new \stdClass()));

        $handler = new OpenWeatherMapStoreMessageHandler($middleware);
        $handler(new OpenWeatherMapStoreMessage([
            'id' => "2021-07-01T12:00:00+00:00-2.348800-48.853400",
            'date' => "2021-07-01T12:00:00+00:00",
            'lat' => 1.0,
            'long' => 2.0,
            'temperature' => 456,
            'temperature_felt' => 87.4,
            'pressure' => 101,
            'humidity' => 86,
            'weather' => "Clouds"
        ]));
    }
}
