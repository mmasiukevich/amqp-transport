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
use ServiceBus\Transport\Amqp\AmqpExchange;
use ServiceBus\Transport\Amqp\AmqpQueue;

/**
 *
 */
final class AmqpQueueTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function defaultCreate(): void
    {
        $queue = AmqpQueue::default(__METHOD__);

        static::assertEquals(__METHOD__, (string) $queue);

        static::assertEquals(0, $queue->flags());

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
        $queue = AmqpQueue::delayed('test', AmqpExchange::direct('qwerty'));

        static::assertEquals('test', (string) $queue);

        /** @see AmqpQueue::AMQP_DURABLE */
        static::assertEquals(2, $queue->flags());
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
        $queue = AmqpQueue::default(__METHOD__, true);

        /** @see AmqpQueue::AMQP_DURABLE */
        static::assertEquals(2, $queue->flags());


        /** @see AmqpQueue::AMQP_PASSIVE */
        $queue->makePassive();
        static::assertEquals(6, $queue->flags());


        /** @see AmqpQueue::AMQP_AUTO_DELETE */
        $queue->enableAutoDelete();
        static::assertEquals(22, $queue->flags());

        /** @see AmqpQueue::AMQP_EXCLUSIVE */
        $queue->makeExclusive();
        static::assertEquals(30, $queue->flags());


        $queue->wthArguments(['key' => 'value']);
        static::assertEquals(['key' => 'value'], $queue->arguments());
    }
}
