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
use ServiceBus\Transport\Amqp\AmqpExchange;
use ServiceBus\Transport\Amqp\Exceptions\InvalidExchangeName;

/**
 *
 */
final class AmqpTopicTest extends TestCase
{
    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function createWithEmptyName(): void
    {
        $this->expectException(InvalidExchangeName::class);
        $this->expectExceptionMessage('Exchange name must be specified');

        AmqpExchange::fanout('');
    }

    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function createWithToLongName(): void
    {
        $this->expectException(InvalidExchangeName::class);
        $this->expectExceptionMessage('Exchange name may be up to 255 bytes of UTF-8 characters (260 specified)');

        AmqpExchange::fanout(\str_repeat('x', 260));
    }

    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function fanoutCreate(): void
    {
        $exchange = AmqpExchange::fanout('fanoutName');

        static::assertSame('fanout', $exchange->type());
        static::assertSame('fanoutName', (string) $exchange);
    }

    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function directCreate(): void
    {
        $exchange = AmqpExchange::direct('directName');

        static::assertSame('direct', $exchange->type());
        static::assertSame('directName', (string) $exchange);
    }

    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function topicCreate(): void
    {
        $exchange = AmqpExchange::topic('topicName');

        static::assertSame('topic', $exchange->type());
        static::assertSame('topicName', (string) $exchange);
    }

    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function delayedCreate(): void
    {
        $exchange = AmqpExchange::delayed('delayedName');

        static::assertSame('x-delayed-message', $exchange->type());
        static::assertSame('delayedName', (string) $exchange);

        static::assertSame(['x-delayed-type' => 'direct'], $exchange->arguments());
    }

    /**
     * @test
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function flags(): void
    {
        $exchange = AmqpExchange::direct('directName', true);

        /** @see AmqpExchange::AMQP_DURABLE */
        static::assertSame(2, $exchange->flags());

        /** @see AmqpExchange::AMQP_PASSIVE */
        $exchange->makePassive();
        static::assertSame(6, $exchange->flags());

        $exchange->wthArguments(['key' => 'value']);
        static::assertSame(['key' => 'value'], $exchange->arguments());
    }
}
