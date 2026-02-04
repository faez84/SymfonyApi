<?php

namespace App\Tests\Controller;

use App\Tests\Api\Api;
use Symfony\Component\HttpFoundation\Response;

class ProfileControllerTest extends Api
{
    public function testGetUserProfileForbidden(): void
    {
        $this->client->request('GET', '/api/profile', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testGetUserProfileUnauthorizedWhenNoToken(): void
    {
        $this->client->request('GET', '/api/profile', [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer '. 'invalidtoken',
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testGetUserProfileSuccess(): void
    {
        $token = $this->getToken();

        $this->client->request('GET', '/api/profile', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent() ?? '', true);
        $this->assertIsArray($data);

        $this->assertArrayHasKey('email', $data);
        $this->assertArrayNotHasKey('password', $data);
    }
}
