<?php

/**
 * PHP Service Bus amqp common implementation
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Transport\Amqp\Tests;

use PHPUnit\Framework\TestCase;
use ServiceBus\Transport\Amqp\AmqpQoSConfiguration;

/**
 *
 */
final class AmqpQoSConfigurationTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function successCreate(): void
    {
        $qos = new AmqpQoSConfiguration(1, 6, true);

        static::assertEquals(1, $qos->size);
        static::assertEquals(6, $qos->count);
        static::assertTrue($qos->global);
    }
}
