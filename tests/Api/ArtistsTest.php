<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\HttpKernelClient;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Factory;
use CHStudio\Raven\Executor;
use CHStudio\Raven\Http\Factory\RequestFactory;
use CHStudio\Raven\Validator\Expectation\ExpectationFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OpenApiTest extends WebTestCase
{
    /**
     * @dataProvider giveRequests
     */
    public function test_openapi(array $requestData, RequestInterface $request): void
    {
        $factory = Factory::fromYamlFile('docs/openapi.yaml');

        $client = new HttpKernelClient(self::createKernel());
        $executor = new Executor(
            $client,
            $factory->getRequestValidator(),
            $factory->getResponseValidator()
        );

        $expectationFactory = new ExpectationFactory();
        $expectations = $expectationFactory->fromArray($requestData);

        try {
            $executor->execute($request, $expectations);
            $message = null;
        } catch (\Throwable $e) {
            $message = $e->getMessage();
        }

        self::assertNull($message, $message ?? '');
    }

    public function giveRequests(): iterable
    {
        $psrFactory = new Psr17Factory();
        $requestFactory = new RequestFactory($psrFactory, $psrFactory);

        $requestData = [
            'uri' => '/v1/artists',
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer token'
            ],
            'body' =>  json_encode([
                'artist_name' => 'coco',
                'artist_genre' => 'rock',
                'albums_recorded' => 3,
                'username' => 'Kayneth',
            ])
        ];

        yield [
            'requestsData' => $requestData,
            'request' => $requestFactory->fromArray($requestData),
        ];
    }
}