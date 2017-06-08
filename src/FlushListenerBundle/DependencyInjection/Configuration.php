<?php

declare(strict_types = 1);

namespace Temirkhan\FlushListenerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Module configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Parses configuration file
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        return $treeBuilder;
    }
}
