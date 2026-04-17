<?php

namespace App\Tests\Support;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

trait DatabaseResetTrait
{
    protected function resetDatabase(): EntityManagerInterface
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        return $entityManager;
    }
}

