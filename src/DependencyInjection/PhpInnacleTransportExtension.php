<?php

/**
 * PHP Service Bus (publish-subscribe pattern) amqp transport implementation
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Transport\Amqp\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 *
 */
final class PhpInnacleTransportExtension extends Extension
{
    /**
     * @var string
     */
    private $connectionDSN;

    /**
     * @var string
     */
    private $defaultDestinationExchange;

    /**
     * @var string|null
     */
    private $defaultDestinationRoutingKey;

    /**
     * @var int|null
     */
    private $qosSize;

    /**
     * @var int|null
     */
    private $qosCount;

    /**
     * @var bool|null
     */
    private $qosGlobal;

    /**
     * @param string      $connectionDSN
     * @param string      $defaultDestinationExchange
     * @param string|null $defaultDestinationRoutingKey
     * @param int|null    $qosSize
     * @param int|null    $qosCount
     * @param bool|null   $qosGlobal
     */
    public function __construct(
        string $connectionDSN,
        string $defaultDestinationExchange,
        ?string $defaultDestinationRoutingKey = null,
        ?int $qosSize = null,
        ?int $qosCount = null,
        ?bool $qosGlobal = null
    )
    {
        $this->connectionDSN                = $connectionDSN;
        $this->defaultDestinationExchange   = $defaultDestinationExchange;
        $this->defaultDestinationRoutingKey = $defaultDestinationRoutingKey;
        $this->qosSize                      = $qosSize;
        $this->qosCount                     = $qosCount;
        $this->qosGlobal                    = $qosGlobal;
    }

    /**
     * @inheritDoc
     *
     * @throws \Throwable
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator());
        $loader->load(__DIR__ . '/amqp.yaml');
        $loader->load(__DIR__ . '/php-innacle.yaml');

        $this->injectParameters($container);
    }

    /**
     * Push parameters to container
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function injectParameters(ContainerBuilder $container): void
    {
        $parameters = [
            'service_bus.transport.amqp.dsn'                       => $this->connectionDSN,
            'service_bus.transport.amqp.qos_size'                  => $this->qosSize,
            'service_bus.transport.amqp.qos_count'                 => $this->qosCount,
            'service_bus.transport.amqp.qos_global'                => $this->qosGlobal,
            'service_bus.transport.amqp.default_destination_topic' => $this->defaultDestinationExchange,
            'service_bus.transport.amqp.default_destination_key'   => $this->defaultDestinationRoutingKey,
        ];

        foreach($parameters as $key => $value)
        {
            $container->setParameter($key, $value);
        }
    }
}
