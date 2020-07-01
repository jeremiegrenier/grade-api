<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace Tests\Functional\AppBundle;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AbstractFunctionalTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 */
abstract class AbstractFunctionalTest extends WebTestCase
{
    public const PATCH = 'PATCH';
    public const POST = 'POST';
    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const OPTIONS = 'OPTIONS';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';

    /**
     * @var KernelBrowser
     */
    protected $client = null;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->client = static::createClient(['environment' => 'test']);
    }

    /**
     * Send Json request.
     *
     * @param ?string $content
     */
    public function createJsonRequest(string $method, string $url, ?string $content = null): KernelBrowser
    {
        $this->client->request($method, $url, [], [], [], $content);

        return $this->client;
    }
}
