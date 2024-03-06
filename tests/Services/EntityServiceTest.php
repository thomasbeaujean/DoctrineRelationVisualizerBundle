<?php

namespace Tbn\DoctrineRelationVisualizerBundle\Tests\Service;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Nyholm\BundleTest\TestKernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Tbn\DoctrineRelationVisualizerBundle\DoctrineRelationVisualizerBundle;
use Tbn\DoctrineRelationVisualizerBundle\Services\EntityService;

class EntityServiceTest extends KernelTestCase
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

        /** @var EntityService */
        $service = $container->get(EntityService::class);
        $data = $service->getEntities([], 'default');

        /** @var SerializerInterface */
        $serializer = $container->get(SerializerInterface::class);

        // trick for having a pretty json
        $array = json_decode($serializer->serialize($data, 'json'), true);
        $entryJSONFile = json_encode($array, JSON_PRETTY_PRINT);

        // Line keep for dev purpose
        // file_put_contents(__DIR__.'/expected.json', $entryJSONFile);
        $this->assertGeneratedJson($entryJSONFile);
    }

    private function assertGeneratedJson(string $json): void
    {
        $expectedContent = \file_get_contents(__DIR__.'/expected.json');
        $this->assertSame($expectedContent, $json);
    }
}
