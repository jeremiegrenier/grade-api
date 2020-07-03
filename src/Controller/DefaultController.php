<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 */
class DefaultController extends AbstractController
{
    /**
     * Check if API is alive.
     *
     * @Route("/ping", methods={"GET"})
     * @Route("/", methods={"GET"}, name="homepage")
     *
     * @SWG\Response(
     *     response=200,
     *     description="pong",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="ping", type="string", default="pong")
     *     )
     * )
     *
     * @SWG\Tag(name="tests")
     */
    public function testApi(LoggerInterface $logger): JsonResponse
    {
        $logger->info('Test API is working');

        return new JsonResponse(['ping' => 'pong'], jsonResponse::HTTP_OK);
    }
}
