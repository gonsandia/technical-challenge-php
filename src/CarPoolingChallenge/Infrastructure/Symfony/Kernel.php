<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return dirname(__DIR__, 4);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import($this->getProjectDir()  . '/config/{packages}/*.yaml');
        $container->import($this->getProjectDir()  . '/config/{packages}/'.$this->environment.'/*.yaml');

        $container->import($this->getProjectDir() . '/config/services.yaml');
        $container->import($this->getProjectDir() . '/config/{services}_' . $this->environment . '.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir()  . '/config/{routes}/*.yaml');
        $routes->import($this->getProjectDir()  . '/config/{routes}/'.$this->environment.'/*.yaml');

        $routes->import($this->getProjectDir() . '/config/routes.yaml');
    }
}
