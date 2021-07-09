<?php

namespace App\Tests\MessageHandler;

use App\Message\OpenWeatherMapComputeMessage;
use App\MessageHandler\OpenWeatherMapCollectHttpMessageHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Vdm\Bundle\LibraryHttpTransportBundle\Message\HttpMessage;

class OpenWeatherMapCollectHttpMessageHandlerTest extends TestCase
{
    public function testInvoke()
    {
        $middleware = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $middleware
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback(function ($message) {
                    $this->assertInstanceOf(OpenWeatherMapComputeMessage::class, $message);
                    $this->assertEquals(['temperature' => 123, 'lat' => 1.0, 'long' => 2.0], $message->getPayload());
                    return true;
                })],
                [$this->callback(function ($message) {
                    $this->assertInstanceOf(OpenWeatherMapComputeMessage::class, $message);
                    $this->assertEquals(['temperature' => 4456, 'lat' => 1.0, 'long' => 2.0], $message->getPayload());
                    return true;
                })]
            )
            ->willReturn(new Envelope(new \stdClass()));

        $handler = new OpenWeatherMapCollectHttpMessageHandler($middleware);
        $handler(new HttpMessage([
            'hourly' => [
                ['temperature' => 123],
                ['temperature' => 4456]
            ],
            'lat' => 1.0,
            'lon' => 2.0
        ]));
    }
}
