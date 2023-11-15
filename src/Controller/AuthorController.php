<?php

namespace App\Controller;
use App\Repository\AuthorRepository;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AuthorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;


class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
     #[Route('/showAuthor/{name}', name: 'showAuthor')]
public function  showAuthor ($name):Response{
return $this->render('author/show.html.twig',[
    'n'=>$name,
]);
}
 #[Route('/listAuthor', name: 'listAuthor')]
public function listAuthor():Response{
$authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>
            'victor.hugo@gmail.com ', 'nb_books' => 0),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
            ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>
            'taha.hussein@gmail.com', 'nb_books' => 300),
            );

            return  $this->render('author/list.html.twig',[
                'authors'=>$authors,
            ]);
}
 #[Route('/detailAuthor/{id}', name: 'detailAuthor')]
public function authorDetails($id)
{
    // Récupérez les informations de l'auteur en utilisant l'ID
    // Par exemple, en utilisant Doctrine pour accéder à la base de données
   //$author = $this->getDoctrine()->getRepository(Author::class)->find($id);

    return $this->render('author/author.html.twig', [
        'author' => $author,
    ]);
}
 #[Route('/fetch', name: 'fetch')]
    public function fetchAuthors(AuthorRepository $repository){
        $author=$repository->findAll(); //select *
        return $this->render('authors_esprit/index.html.twig',['author'=>$author]);
    }

#[Route('/addStatique', name: 'addStatique')]
    public function addStatique(EntityManagerInterface $entityManager):Response{
//préparation de l'objet
        $author1= new Author();
        $author1->setUsername("test");
        $author1->setEmail("test@gmail.com");
      //  $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($author1);
        $entityManager->flush();
       // return new Response("Auteur ajouté avec succès !");
         return $this->redirectToRoute('fetch');

//Deuxième methode 
         /*use Doctrine\Persistence\ManagerRegistry; AJOUTER AU DEBUT 

         public function AddStudent(ManagerRegistry $em ){
        $auth=new Author();
        $auth->setName('ali');
        $auth->setEmail('test');
        $auth->setNbbooks(300);
        $manager=$em->getManager();
        $manager->persist($auth);
        $manager->flush();
        return new Response('added');}
        */

    }
#[Route('/delete/{id}', name: 'delete')]

public function delete( $id, AuthorRepository $repository){
$author = $repository->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('fetch');
}

//Edit 
 #[Route('/edit/{id}', name: 'edit')]
    public function edit(AuthorRepository $repository, $id, Request $request)
    {
        $author = $repository->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush(); // Correction : Utilisez la méthode flush() sur l'EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("fetch");
        }

        return $this->render('author/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }

//Ajout à partit d'un formulaire
    #[Route('/Add', name: 'app_Add')]
public function  Add (Request  $request)
{
    $author=new Author();
    $form =$this->CreateForm(AuthorType::class,$author);
  $form->add('Ajouter',SubmitType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid())
    {
        $em=$this->getDoctrine()->getManager();
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('fetch');
    }
    return $this->render('author/Add.html.twig',['f'=>$form->createView()]);

}
  //Query Builder: Question 1
  #[Route('/author/list/OrderByEmail', name: 'app_author_list_ordered', methods: ['GET'])]
  public function listAuthorByEmail(AuthorRepository $authorRepository): Response
  {
      return $this->render('author/orderedList.html.twig', [
          'authors' => $authorRepository->showAllAuthorsOrderByEmail(),
      ]);
  }
}
