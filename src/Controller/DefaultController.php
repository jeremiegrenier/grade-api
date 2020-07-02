<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Controller;

use Psr\Log\LoggerInterface;
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
     * @Route("/ping", methods={"GET"})
     * @Route("/", methods={"GET"}, name="homepage")
     */
    public function testApi(LoggerInterface $logger): JsonResponse
    {
        $logger->info('Test API is working');

        return new JsonResponse(['ping' => 'pong'], jsonResponse::HTTP_OK);
    }
}
