<?php 

use Symfony\Bundle;

class OCI8RepositoryBundleBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}