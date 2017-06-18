<?php
declare(strict_types = 1);

namespace Temirkhan\FlushListenerBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration tests
 */
class ConfigurationTest extends TestCase
{
    /**
     * Tests tree builder
     */
    public function testTreeBuilder()
    {
        $configuration = new Configuration();

        $this->assertEquals(new TreeBuilder(), $configuration->getConfigTreeBuilder());
    }
}
