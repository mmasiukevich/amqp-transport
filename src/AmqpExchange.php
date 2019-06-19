<?php

/**
 * AMQP transport common implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Transport\Amqp;

use ServiceBus\Transport\Amqp\Exceptions\InvalidExchangeName;
use ServiceBus\Transport\Common\Topic;

/**
 * Exchange details.
 *
 * @property-read string $name
 * @property-read string $type
 * @property-read bool   $passive
 * @property-read bool   $durable
 * @property-read array  $arguments
 * @property-read int    $flags
 */
final class AmqpExchange implements Topic
{
    private const TYPE_FANOUT = 'fanout';

    private const TYPE_DIRECT = 'direct';

    private const TYPE_TOPIC = 'topic';

    private const AMQP_DURABLE = 2;

    private const AMQP_PASSIVE = 4;

    /** Plugin rabbitmq_delayed_message_exchange */
    private const TYPE_DELAYED = 'x-delayed-message';

    private const MAX_NAME_SYMBOLS = 255;

    /**
     * The exchange name consists of a non-empty sequence of these characters: letters, digits, hyphen, underscore,
     * period, or colon.
     *
     * @var string
     */
    public $name;

    /**
     * Exchange type.
     *
     * - fanout
     * - direct
     * - topic
     * - x-delayed-message
     *
     * @var string
     */
    public $type;

    /**
     *  If set, the server will reply with Declare-Ok if the exchange already exists with the same name, and raise an
     *  error if not. The client can use this to check whether an exchange exists without modifying the server state.
     *  When set, all other method fields except name and no-wait are ignored. A declare with both passive and no-wait
     *  has no effect. Arguments are compared for semantic equivalence.
     *
     * If set, and the exchange does not already exist, the server MUST raise a channel exception with reply code 404
     * (not found). If not set and the exchange exists, the server MUST check that the existing exchange has the same
     * values for type, durable, and arguments fields. The server MUST respond with Declare-Ok if the requested
     * exchange matches these fields, and MUST raise a channel exception if not.
     *
     * @var bool
     */
    public $passive = false;

    /**
     * If set when creating a new exchange, the exchange will be marked as durable. Durable exchanges remain active
     * when a server restarts. Non-durable exchanges (transient exchanges) are purged if/when a server restarts.
     *
     * @var bool
     */
    public $durable = false;

    /**
     * @see       https://www.rabbitmq.com/amqp-0-9-1-reference.html#domain.table
     *
     * @psalm-var array<array-key, string|int|float>
     *
     * @var array
     */
    public $arguments = [];

    /**
     * Exchange flags.
     *
     * @var int
     */
    public $flags = 0;

    /**
     * @param string $name
     * @param bool   $durable
     *
     * @throws \ServiceBus\Transport\Amqp\Exceptions\InvalidExchangeName
     *
     * @return self
     */
    public static function fanout(string $name, bool $durable = false): self
    {
        return new self($name, self::TYPE_FANOUT, $durable);
    }

    /**
     * @param string $name
     * @param bool   $durable
     *
     * @throws \ServiceBus\Transport\Amqp\Exceptions\InvalidExchangeName
     *
     * @return self
     */
    public static function direct(string $name, bool $durable = false): self
    {
        return new self($name, self::TYPE_DIRECT, $durable);
    }

    /**
     * @param string $name
     * @param bool   $durable
     *
     * @throws \ServiceBus\Transport\Amqp\Exceptions\InvalidExchangeName
     *
     * @return self
     */
    public static function topic(string $name, bool $durable = false): self
    {
        return new self($name, self::TYPE_TOPIC, $durable);
    }

    /**
     * @param string $name
     *
     * @throws \ServiceBus\Transport\Amqp\Exceptions\InvalidExchangeName
     *
     * @return self
     */
    public static function delayed(string $name): self
    {
        return new self($name, self::TYPE_DELAYED, true, ['x-delayed-type' => self::TYPE_DIRECT]);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function makePassive(): self
    {
        if (false === $this->passive)
        {
            $this->passive = true;
            $this->flags   += self::AMQP_PASSIVE;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function makeDurable(): self
    {
        if (false === $this->durable)
        {
            $this->durable = true;
            $this->flags   += self::AMQP_DURABLE;
        }

        return $this;
    }

    /**
     * @param array $arguments
     *
     * @return $this
     */
    public function wthArguments(array $arguments): self
    {
        /** @psalm-suppress MixedTypeCoercion */
        $this->arguments = \array_merge($this->arguments, $arguments);

        return $this;
    }

    /**
     * @deprecated Will be removed in the next version (use toString() method)
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @deprecated Call the property directly
     *
     * @return bool
     */
    public function isPassive(): bool
    {
        return $this->passive;
    }

    /**
     * @deprecated Call the property directly
     *
     * @return bool
     */
    public function isDurable(): bool
    {
        return $this->durable;
    }

    /**
     * @deprecated Call the property directly
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @deprecated Call the property directly
     *
     * @return int
     */
    public function flags(): int
    {
        return $this->flags;
    }

    /**
     * @deprecated Call the property directly
     *
     * @return array
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * @psalm-param array<array-key, string|int|float> $arguments
     *
     * @param string $name
     * @param string $type
     * @param bool   $durable
     * @param array  $arguments
     *
     * @throws \ServiceBus\Transport\Amqp\Exceptions\InvalidExchangeName
     */
    private function __construct(string $name, string $type, bool $durable, array $arguments = [])
    {
        if ('' === $name)
        {
            throw InvalidExchangeName::nameCantBeEmpty();
        }

        if (self::MAX_NAME_SYMBOLS < \mb_strlen($name))
        {
            throw InvalidExchangeName::nameIsToLong($name);
        }

        $this->arguments = $arguments;
        $this->name      = $name;
        $this->type      = $type;

        if (true === $durable)
        {
            $this->makeDurable();
        }
    }
}
