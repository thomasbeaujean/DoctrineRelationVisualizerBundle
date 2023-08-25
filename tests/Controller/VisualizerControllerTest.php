<?php

namespace tbn\DoctrineRelationVisualizerBundle\Tests\Service;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Nyholm\BundleTest\TestKernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Response;
use tbn\DoctrineRelationVisualizerBundle\Controller\VisualizerController;
use tbn\DoctrineRelationVisualizerBundle\DoctrineRelationVisualizerBundle;
use tbn\DoctrineRelationVisualizerBundle\Services\EntityService;

class VisualizerControllerTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestBundle(DoctrineRelationVisualizerBundle::class);
        $kernel->handleOptions($options);
        $kernel->addTestConfig(__DIR__.'/../config.yml');

        return $kernel;
    }

    public function testGenerate(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->assertTrue($container->has(EntityService::class));

        /** @var VisualizerController */
        $service = $container->get(VisualizerController::class);
        $response = $service->getDataAction('default');

        $this->assertInstanceOf (Response::class, $response);
    }
}
