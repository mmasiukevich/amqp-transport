<?php

/**
 * PHP Service Bus (publish-subscribe pattern) amqp transport implementation
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace ServiceBus\Transport\Amqp\Tests;

use PHPUnit\Framework\TestCase;
use ServiceBus\Transport\Amqp\AmqpExchange;

/**
 *
 */
final class AmqpTopicTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function fanoutCreate(): void
    {
        $exchange = AmqpExchange::fanout('fanoutName');

        static::assertEquals('fanout', $exchange->type());
        static::assertEquals('fanoutName', (string) $exchange);
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function directCreate(): void
    {
        $exchange = AmqpExchange::direct('directName');

        static::assertEquals('direct', $exchange->type());
        static::assertEquals('directName', (string) $exchange);
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function topicCreate(): void
    {
        $exchange = AmqpExchange::topic('topicName');

        static::assertEquals('topic', $exchange->type());
        static::assertEquals('topicName', (string) $exchange);
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function delayedCreate(): void
    {
        $exchange = AmqpExchange::delayed('delayedName');

        static::assertEquals('x-delayed-message', $exchange->type());
        static::assertEquals('delayedName', (string) $exchange);

        static::assertEquals(['x-delayed-type' => 'direct'], $exchange->arguments());
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function flags(): void
    {
        $exchange = AmqpExchange::direct('directName', true);

        /** @see AmqpExchange::AMQP_DURABLE */
        static::assertEquals(2, $exchange->flags());

        /** @see AmqpExchange::AMQP_PASSIVE */
        $exchange->makePassive();
        static::assertEquals(6, $exchange->flags());


        $exchange->wthArguments(['key' => 'value']);
        static::assertEquals(['key' => 'value'], $exchange->arguments());
    }
}
