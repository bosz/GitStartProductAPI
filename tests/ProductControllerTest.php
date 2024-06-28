<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends ApiTestCase
{
    private $client;
    private $jwtToken;
    use RefreshDatabaseTrait;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Create a test user and authenticate to get JWT token
        $this->client->request('POST', '/api/register', [
            'body' => [
                'email' => 'zheadmin@gitstart.com',
                'password' => 'password@123'
            ], 'headers' => [
                'Content-Type' => 'application/json'
            ],
        ]);

        // return;

        $this->client->request('POST', '/api/login_check', [
            'body' => [
                'email' => 'zheadmin@gitstart.com',
                'password' => 'password@123'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
        ]);

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->jwtToken = $data['token'];
    }



    // protected function tearDown(): void
    // {
    //     parent::tearDown();
    //     $this->client = null;
    //     $this->jwtToken = null;
    // }

    // Helper function to perform authenticated requests
    private function request(string $method, string $uri, array $data = [])
    {
        $this->client->request(
            $method,
            $uri,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $this->jwtToken)
            ],
            json_encode($data)
        );
    }

    public function testListProducts()
    {
        $this->request('GET', '/api/products');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    // public function testCreateProduct()
    // {
    //     $this->request('POST', '/api/products', [
    //         'name' => 'New Product',
    //         'description' => 'This is a test product',
    //         'price' => 10.99,
    //         'quantity' => 100
    //     ]);
    //     $response = $this->client->getResponse();
    //     $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    //     $this->assertJson($response->getContent());
    //     $data = json_decode($response->getContent(), true);
    //     $this->assertArrayHasKey('id', $data);
    // }

    // public function testGetProduct()
    // {
    //     // First, create a product
    //     $this->request('POST', '/api/products', [
    //         'name' => 'New Product',
    //         'description' => 'This is a test product',
    //         'price' => 12.99,
    //         'quantity' => 130
    //     ]);
    //     $response = $this->client->getResponse();
    //     $data = json_decode($response->getContent(), true);
    //     $productId = $data['id'];

    //     // Then, get the product details
    //     $this->request('GET', '/api/products/' . $productId);
    //     $response = $this->client->getResponse();
    //     $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    //     $this->assertJson($response->getContent());
    //     $data = json_decode($response->getContent(), true);
    //     $this->assertEquals($productId, $data['id']);
    // }

    // public function testUpdateProduct()
    // {
    //     // First, create a product
    //     $this->request('POST', '/api/products', [
    //         'name' => 'New Product',
    //         'description' => 'This is a test product',
    //         'price' => 18.33,
    //         'quantity' => 150
    //     ]);
    //     $response = $this->client->getResponse();
    //     $data = json_decode($response->getContent(), true);
    //     $productId = $data['id'];

    //     // Then, update the product
    //     $this->request('PUT', '/api/products/' . $productId, [
    //         'name' => 'Updated Product',
    //         'description' => 'This is an updated test product',
    //         'price' => 20.99,
    //         'quantity' => 200
    //     ]);
    //     $response = $this->client->getResponse();
    //     $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    //     $this->assertJson($response->getContent());
    //     $data = json_decode($response->getContent(), true);
    //     $this->assertEquals('Updated Product', $data['title']);
    // }

    // public function testDeleteProduct()
    // {
    //     // First, create a product
    //     $this->request('POST', '/api/products', [
    //         'name' => 'New Product',
    //         'description' => 'This is a test product',
    //         'price' => 12.22,
    //         'quantity' => 1,
    //     ]);
    //     $response = $this->client->getResponse();
    //     $data = json_decode($response->getContent(), true);
    //     $productId = $data['id'];

    //     // Then, delete the product
    //     $this->request('DELETE', '/api/products/' . $productId);
    //     $response = $this->client->getResponse();
    //     $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    // }
}
