<?php
declare(strict_types = 1);

namespace Temirkhan\FlushListenerBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Temirkhan\FlushListenerBundle\EventListener\OnResponseFlushListener;

/**
 * Extension loader tests
 */
class TemirkhanFlushListenerExtensionTest extends TestCase
{
    /**
     * Tests if event listener is injected into container
     */
    public function testLoad()
    {
        $container = $this->createContainer();
        $extension = new TemirkhanFlushListenerExtension();

        $container
            ->expects($this->once())
            ->method('setDefinition')
            ->with(
                $this->equalTo('temirkhan.flush_listener.event_listener.flush_on_response'),
                $this->callback(function (Definition $definition): bool {
                    $this->assertEquals(OnResponseFlushListener::class, $definition->getClass());
                    $this->assertEquals(new Reference('doctrine.orm.entity_manager'), $definition->getArgument(0));
                    $this->assertTrue($definition->hasTag('kernel.event_listener'));

                    $tags         = $definition->getTag('kernel.event_listener');
                    $expectedTags = [
                        [
                            'event'  => 'kernel.response',
                            'method' => 'onKernelResponse',
                        ],
                        [
                            'event'  => 'transaction.commit',
                            'method' => 'onTransactionCommit',
                        ],
                        [
                            'event'  => 'transaction.rollback',
                            'method' => 'onTransactionRollback',
                        ],
                    ];

                    $this->assertArraySubset($expectedTags, $tags);

                    return true;
                })
            );

        $extension->load([], $container);
    }

    /**
     * Creates container
     *
     * @return MockObject|ContainerBuilder
     */
    private function createContainer(): ContainerBuilder
    {
        return $this->createMock(ContainerBuilder::class);
    }
}
