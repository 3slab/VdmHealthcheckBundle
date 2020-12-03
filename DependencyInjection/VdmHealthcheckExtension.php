<?php

namespace Vdm\Bundle\HealthcheckBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\FileLocator;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerFactoryRegistry;

/**
 * VdmHealthcheckExtension
 */
class VdmHealthcheckExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setParameter('vdm_healthcheck.secret', $mergedConfig['secret']);
        $container->setParameter('vdm_healthcheck.liveness_path', $mergedConfig['liveness_path']);
        $container->setParameter('vdm_healthcheck.readiness_path', $mergedConfig['readiness_path']);

        $container->setParameter(
            'vdm_healthcheck.liveness_checkers_config',
            $mergedConfig['liveness_checkers']
        );
        $container->setParameter(
            'vdm_healthcheck.readiness_checkers_config',
            $mergedConfig['readiness_checkers']
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        if (
            $this->isCheckerTypeEnabled('dbal', $mergedConfig['readiness_checkers'])
            || $this->isCheckerTypeEnabled('dbal', $mergedConfig['liveness_checkers'])
        ) {
            $loader->load('./checker/dbal.yml');
        }

        if (
            $this->isCheckerTypeEnabled('http', $mergedConfig['readiness_checkers'])
            || $this->isCheckerTypeEnabled('http', $mergedConfig['liveness_checkers'])
        ) {
            $loader->load('./checker/http.yml');
        }

        if (
            $this->isCheckerTypeEnabled('odm', $mergedConfig['readiness_checkers'])
            || $this->isCheckerTypeEnabled('odm', $mergedConfig['liveness_checkers'])
        ) {
            $loader->load('./checker/odm.yml');
        }
    }

    /**
     * Verify if a specific type of checker is enabled
     *
     * @param string $type
     * @param array $config
     * @return bool
     */
    protected function isCheckerTypeEnabled(string $type, array $config): bool
    {
        $filteredConfig = array_filter($config, function (array $item) use ($type) {
            return $item['type'] === $type;
        });

        return count($filteredConfig) > 0;
    }
}
