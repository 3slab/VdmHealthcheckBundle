<?php

namespace Vdm\Bundle\HealthcheckBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Vdm\Bundle\HealthcheckBundle\Controller\HealthController;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;

/**
 * Class CheckerFactoryPass
 *
 * Container compiler pass to load checkers factory into the registry service
 *
 * @package Vdm\Bundle\HealthcheckBundle\DependencyInjection\Compiler
 */
class CheckerFactoryPass implements CompilerPassInterface
{
    protected const CHECKER_FACTORY_TAG = 'vdm_healthcheck.checker_factory';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(HealthController::class)) {
            return;
        }

        $taggedServices = $container
            ->findTaggedServiceIds(self::CHECKER_FACTORY_TAG);

        foreach ($taggedServices as $id => $tags) {
            $checkerFactoryClass = $container->getDefinition($id)->getClass();
            $container->setAlias(
                'vdm_healthcheck.factory.' . $checkerFactoryClass::getType(),
                $id
            );
        }

        $this->createCheckManager($container, 'liveness_manager', 'liveness_checkers_config');
        $this->createCheckManager($container, 'readiness_manager', 'readiness_checkers_config');
    }

    /**
     * @param ContainerBuilder $container
     * @param string $managerName
     * @param string $configName
     * @return Definition
     */
    protected function createCheckManager(ContainerBuilder $container, string $managerName, string $configName)
    {
        $managerDefiniton = new Definition(CheckManager::class);
        $managerDefiniton->setPublic(true);

        $checkerConfig = $container->getParameter("vdm_healthcheck.$configName");

        foreach ($checkerConfig as $name => $config) {
            $checkerDefinition = $this->createCheckerDefinition($container, $name, $config);

            $container->setDefinition("vdm_healthcheck.$managerName.checker.$name", $checkerDefinition);

            $managerDefiniton->addMethodCall(
                'addCheck',
                [
                    $name,
                    new Reference("vdm_healthcheck.$managerName.checker.$name")
                ]
            );
        }

        $container->setDefinition("vdm_healthcheck.manager.$managerName", $managerDefiniton);

        return $managerDefiniton;
    }

    /**
     * @param ContainerBuilder $container
     * @param string $name
     * @param array $config
     * @return Definition
     */
    protected function createCheckerDefinition(ContainerBuilder $container, string $name, array $config)
    {
        $factory = $container->getDefinition($container->getAlias('vdm_healthcheck.factory.' . $config['type']));

        $checkerConfigDefinition = $this->createCheckerConfigDefinition($container, $name, $config);

        $checkerDefinition = new Definition(AbstractChecker::class);
        $checkerDefinition->setPublic(false);
        $checkerDefinition->setFactory([$factory, 'build']);
        $checkerDefinition->setArguments([$checkerConfigDefinition]);

        return $checkerDefinition;
    }


    /**
     * @param ContainerBuilder $container
     * @param string $name
     * @param array $config
     * @return Definition
     */
    protected function createCheckerConfigDefinition(ContainerBuilder $container, string $name, array $config)
    {
        foreach ($config['arguments'] as $key => $arg) {
            if (!is_string($arg) || strpos($arg, '@') !== 0) {
                continue;
            }

            $serviceId = substr($arg, 1);
            if ($container->hasDefinition($serviceId)) {
                $config['arguments'][$key] = new Reference($serviceId);
            } elseif ($container->hasAlias($serviceId)) {
                $config['arguments'][$key] = new Reference($serviceId);
            } else {
                throw new ServiceNotFoundException(
                    "Service $serviceId not found when building checker config $name"
                );
            }
        }

        $checkerConfigDefinition = new Definition(CheckerConfig::class);
        $checkerConfigDefinition->setPublic(false);
        $checkerConfigDefinition->setArguments([$name, $config['type'], $config['arguments']]);

        return $checkerConfigDefinition;
    }
}
