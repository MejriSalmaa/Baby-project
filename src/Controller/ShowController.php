<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowController extends AbstractController
{
    #[Route('/show', name: 'app_show')]
    public function index(): Response
    {
        return $this->render('show/index.html.twig', [
            'controller_name' => 'ShowController',
        ]);
    }

    #[Route ('test',name: 'test' )]
    public function show(){
        return new Response ('bonjour');
    } 

    #[Route ('t',name: 't' )]
    public function format(){
        return new JsonResponse('bonjour salma ');
    } 
}
