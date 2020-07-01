<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Controller;

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
     */
    public function testApi(): JsonResponse
    {
        return new JsonResponse(['ping' => 'pong'], jsonResponse::HTTP_OK);
    }
}
