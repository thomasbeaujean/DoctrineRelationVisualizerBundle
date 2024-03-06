<?php

namespace Tbn\DoctrineRelationVisualizerBundle\Services;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;

class Persister
{
    public function __construct(
        private string $ymlFilePath,
    ) {
        $this->ymlFilePath = $ymlFilePath;
    }

    public function save(array $entities, string $connectionName): void
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($entities, 10);

        $fs = new Filesystem();

        $filepath = $this->ymlFilePath.'/visualizer-'.$connectionName.'.yml';
        $fs->touch($filepath);

        file_put_contents($filepath, $yaml);
    }

    public function load(string $connectionName): array
    {
        $filepath = $this->ymlFilePath.'/visualizer-'.$connectionName.'.yml';

        $fs = new Filesystem();
        $fs->touch($filepath);

        $content = file_get_contents($filepath);

        return Yaml::parse($content) ?? [];
    }
}
