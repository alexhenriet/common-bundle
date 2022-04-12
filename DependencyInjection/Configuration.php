<?php

namespace Alexhenriet\Bundle\CommonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('common');
        $rootNode = $builder->getRootNode();
        $rootNode->children()
            ->scalarNode('ldap_host')
            ->isRequired()
            ->end()
            ->scalarNode('ldap_port')
            ->defaultValue(389)
            ->end()
            ->scalarNode('ldap_opt_protocol_version')
            ->defaultValue(3)
            ->end()
            ->scalarNode('ldap_opt_referrals')
            ->defaultValue(0)
            ->end()
            ->scalarNode('login_prefix')
            ->defaultValue('')
            ->end()
            ->scalarNode('login_route')
            ->defaultValue('app_login')
            ->end()
            ->arrayNode('bypass_user_identifiers')
            ->isRequired()
            ->scalarPrototype()
            ->end()
            ->end()
            ->arrayNode('bypass_environments')
            ->isRequired()
            ->scalarPrototype()
            ->end()
            ->end();
        return $builder;
    }
}