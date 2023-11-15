<?php

namespace App\Controller;
use App\Repository\AuthorRepository;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class AuthorsEspritController extends AbstractController
{
    #[Route('/authors/esprit', name: 'app_authors_esprit')]
    public function index(): Response
    {
        return $this->render('authors_esprit/index.html.twig', [
            'controller_name' => 'AuthorsEspritController',
        ]);
    }



}

