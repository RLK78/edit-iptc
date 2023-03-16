<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/images/{filename}", name="app_image")
     */
    public function index(string $filename): BinaryFileResponse
    {
        $path = $this->getParameter('kernel.project_dir') . '/data/images/' . $filename;

        if (!file_exists($path)) {
            throw new NotFoundHttpException('Image not found');
        }

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', mime_content_type($path));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);

        return $response;
    }
}