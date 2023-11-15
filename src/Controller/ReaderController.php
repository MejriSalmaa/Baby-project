<?php

namespace App\Controller;
use App\Entity\Reader;
use App\Form\ReaderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReaderRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class ReaderController extends AbstractController
{
    #[Route('/reader', name: 'app_reader')]
    public function index(): Response
    {
        return $this->render('reader/index.html.twig', [
            'controller_name' => 'ReaderController',
        ]);
    }


    #[Route('/reader/list', name: 'app_reader_list')]
    public function afficher (ReaderRepository $ReaderRep)   : Response
    {
        $reader = $ReaderRep->findAll(); // SELECT * FROM author
        return $this->render('reader/list.html.twig', [
            'reader' => $reader,
        ]);
    }

    #[Route('/reader/add', name: 'app_reader_add')]
    public function add ( Request $request )
    {
        $reader = new Reader(); 
        $form = $this->createForm(ReaderType::class, $reader); 
        $form -> add('submit', SubmitType::class, ['label' => 'Ajouter']); 
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) 
        {
          
            $Reader = $form->getData(); 
            $entityManager = $this->getDoctrine()->getManager(); 
            $entityManager->persist($Reader);
            $entityManager->flush();
            return $this->redirectToRoute('app_reader_list');
        }
        return $this->render('reader/add.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    #[Route('/reader/edit/{id}', name: 'app_reader_edit')]
    function edit (ReaderRepository $readerRep , Request $request)
    {
        $reader = $readerRep->find($request->get('id'));
        $form = $this->createForm(ReaderType::class, $reader);
        $form -> add('submit', SubmitType::class, ['label' => 'Modifier']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $reader = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_reader_list');
        }
        return $this->render('reader/edit.html.twig', [
            'f' => $form->createView(),
        ]);
   }


   #[Route('/reader/delete/{id}', name: 'app_reader_delete')]
   function delete (ReaderRepository $readerRep , Request $request)
   {
       $reader = $readerRep->find($request->get('id'));
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->remove($reader);
       $entityManager->flush();
       return $this->redirectToRoute('app_reader_list');
   }
}



