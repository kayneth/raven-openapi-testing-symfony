<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/v1/artists', methods: ['POST'])]
class PostArtistAction
{
    public function __invoke(Request $request): JsonResponse
    {
        $body = $request->toArray();
        return new JsonResponse([
            'username' => $body['username'],
            'artist_name' => $body['artist_name'],
        ]);
    }
}