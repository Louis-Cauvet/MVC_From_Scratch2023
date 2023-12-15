<?php

namespace App\Routing;

class AbstractRoute
{
    public function __construct(
        protected string $uri,
        protected string $name,
        protected string $httpMethod = 'GET'    // la valeur par défaut est GET
    ) {
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUri()
    {
        return $this->uri;
    }
}
