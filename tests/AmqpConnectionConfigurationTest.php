<?php

/**
 * AMQP transport common implementation
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Transport\Amqp\Tests;

use PHPUnit\Framework\TestCase;
use ServiceBus\Transport\Amqp\AmqpConnectionConfiguration;
use ServiceBus\Transport\Common\Exceptions\InvalidConnectionParameters;

/**
 *
 */
final class AmqpConnectionConfigurationTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function createLocalhost(): void
    {
        $options = AmqpConnectionConfiguration::createLocalhost();

        static::assertEquals(
            'amqp://guest:guest@localhost:5672?vhost=/&timeout=1&heartbeat=60.00',
            (string) $options
        );

        static::assertEquals('localhost', $options->host());
        static::assertEquals(5672, $options->port());
        static::assertEquals('/', $options->virtualHost());
        static::assertEquals('guest', $options->password());
        static::assertEquals('guest', $options->user());
        static::assertEquals(1.0, $options->timeout());
        static::assertEquals(60.0, $options->heartbeatInterval());
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function parseDSN(): void
    {
        static::assertEquals(
            AmqpConnectionConfiguration::createLocalhost(),
            new AmqpConnectionConfiguration(
                'amqp://guest:guest@localhost:5672?vhost=/&timeout=1&heartbeat=60.00'
            )
        );
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function failedQuery(): void
    {
        $this->expectException(InvalidConnectionParameters::class);
        $this->expectExceptionMessage('Can\'t parse specified connection DSN (///example.org:80)');

        new AmqpConnectionConfiguration('///example.org:80');
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function emptyDSN(): void
    {
        $this->expectException(InvalidConnectionParameters::class);
        $this->expectExceptionMessage('Connection DSN can\'t be empty');

        new AmqpConnectionConfiguration('');
    }
}
