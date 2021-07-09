<?php

namespace App\Tests\Executor;

use App\Executor\OpenWeatherMapCollectHttpExecutor;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Messenger\Envelope;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;
use Vdm\Bundle\LibraryHttpTransportBundle\Message\HttpMessage;

class OpenWeatherMapCollectHttpExecutorTest extends TestCase
{
    public function testExecute()
    {
        /** @var HttpClientInterface $httpClient */
        $httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://myurl', ['key' => 'value'])
            ->willReturn(
                MockResponse::fromRequest(
                    "GET",
                    "http://myurl",
                    ['key' => 'value'],
                    new MockResponse('{"body":"value"}')
                )
            );

        $executor = new OpenWeatherMapCollectHttpExecutor($httpClient, new NullLogger());
        $envelopes = iterator_to_array($executor->execute('http://myurl', 'GET', ['key' => 'value']));

        $this->assertCount(1, $envelopes);
        $this->assertInstanceOf(Envelope::class, $envelopes[0]);
        $this->assertArrayHasKey(StopAfterHandleStamp::class, $envelopes[0]->all());

        $message = $envelopes[0]->getMessage();
        $this->assertInstanceOf(HttpMessage::class, $message);
        $this->assertEquals(['body' => 'value'], $message->getPayload());
    }
}
