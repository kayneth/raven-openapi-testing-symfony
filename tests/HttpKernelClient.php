<?php

declare(strict_types=1);

namespace App\Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestInterface,ResponseInterface};
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class HttpKernelClient implements ClientInterface
{
    private readonly HttpFoundationFactory $httpFoundationFactory;
    private readonly PsrHttpFactory $psrHttpFactory;

    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
        $psr17Factory = new Psr17Factory();
        $this->httpFoundationFactory = new HttpFoundationFactory();
        $this->psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        /* transform RequestInterface to ServerRequestInterface */
        $serverRequest = new ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            $request->getHeaders(),
            $request->getBody(),
        );
        $response = $this->kernel->handle(
            $this->httpFoundationFactory->createRequest($serverRequest)
        );

        return $this->psrHttpFactory->createResponse($response);
    }
}