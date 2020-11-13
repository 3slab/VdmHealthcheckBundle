<?php

/**
 * @package    3slab/VdmHealthcheckBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmHealthcheckBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\HealthcheckBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vdm\Bundle\HealthcheckBundle\DependencyInjection\Compiler\CheckerFactoryPass;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory;

class VdmHealthcheckBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CheckerFactoryPass());

        $container->registerForAutoconfiguration(AbstractCheckerFactory::class)
            ->addTag('vdm_healthcheck.checker_factory')
        ;
    }
}
