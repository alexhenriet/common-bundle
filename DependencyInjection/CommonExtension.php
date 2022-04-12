<?php

namespace Alexhenriet\Bundle\CommonBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class CommonExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container) : void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('common.ldap_host', $config['ldap_host']);
        $container->setParameter('common.ldap_port', $config['ldap_port']);
        $container->setParameter('common.ldap_opt_protocol_version', $config['ldap_opt_protocol_version']);
        $container->setParameter('common.ldap_opt_referrals', $config['ldap_opt_referrals']);
        $container->setParameter('common.login_prefix', $config['login_prefix']);
        $container->setParameter('common.login_route', $config['login_route']);
        $container->setParameter('common.bypass_user_identifiers', $config['bypass_user_identifiers']);
        $container->setParameter('common.bypass_environments', $config['bypass_environments']);
    }
}