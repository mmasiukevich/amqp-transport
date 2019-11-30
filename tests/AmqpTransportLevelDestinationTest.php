<?php

/**
 * AMQP transport common implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Transport\Amqp\Tests;

use PHPUnit\Framework\TestCase;
use ServiceBus\Transport\Amqp\AmqpTransportLevelDestination;
use ServiceBus\Transport\Amqp\Exceptions\IncorrectDestinationExchange;

/**
 *
 */
final class AmqpTransportLevelDestinationTest extends TestCase
{
    /**
     * @test
     *
     * @throws \Throwable
     */
    public function withEmptyExchangeName(): void
    {
        $this->expectException(IncorrectDestinationExchange::class);
        $this->expectExceptionMessage('Destination exchange name must be specified');

        new AmqpTransportLevelDestination('');
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function successCreate(): void
    {
        $destination = new AmqpTransportLevelDestination('qwerty', 'root');

        static::assertSame('qwerty', $destination->exchange);
        static::assertSame('root', $destination->routingKey);
    }
}
