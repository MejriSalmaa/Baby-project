<?php

namespace App\Controller;


use App\Entity\Book;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;
use App\Entity\Author;
use App\Form\SearchBookType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\BookRepository;
use PHPUnit\Framework\Constraint\Count;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }


 #[Route('/fetchBook', name: 'app_AfficheBook')]
    public function fetchbooks(BookRepository $repository){

        //récupérer les livres publiés
        $publishedBooks = $this->getDoctrine()->getRepository(Book::class)->findBy(['published' => true]);
        //compter le nombre de livres pubbliés et non publiés
        $numPublishedBooks = count($publishedBooks);
        $numUnPublishedBooks = count($this->getDoctrine()->getRepository(Book::class)->findBy(['published' => false]));

        if ($numPublishedBooks > 0) {
            return $this->render('book/Affiche.html.twig', ['publishedBooks' => $publishedBooks, 'numPublishedBooks' => $numPublishedBooks, 'numUnPublishedBooks' => $numUnPublishedBooks]);

        } else {
            //afficher un message si aucun livre n'a été trouvé$
            return $this->render('book/no_books_found.html.twig');
        }

    }


  /*  #[Route('/addBook', name: 'add_book')]
public function  AddBooks (Request  $request)
{
    $book=new Book();
    $form =$this->CreateForm(BookType::class,$book);
  $form->add('save',SubmitType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid())
    {
        $em=$this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();
        return $this->redirectToRoute('fetch_book');
    }
    return $this->render('book/Add.html.twig',['f'=>$form->createView()]);

}*/

 #[Route('/AddBook', name: 'app_AddBook')]
    public function Add(Request $request)
    {
        $book = new Book();
        $form = $this->CreateForm(BookType::class, $book);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //initialisation de l'attribut "published" a true
            //  $book->setPublished(true);
// get the accociated author from the book entity
            $author = $book->getAuthor();
            //incrementation de l'attribut "nb_books" de l'entire Author

            if ($author instanceof Author) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('app_AfficheBook');
        }
        return $this->render('book/Add.html.twig', ['f' => $form->createView()]);

}

  #[Route('/editbook/{ref}', name: 'app_editBook')]
    public function edit(BookRepository $repository, $ref, Request $request)
    {
        $author = $repository->find($ref);
        $form = $this->createForm(BookType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush(); // Correction : Utilisez la méthode flush() sur l'EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("app_AfficheBook");
        }

        return $this->render('book/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }

 #[Route('/deletebook/{ref}', name: 'app_deleteBook')]
    public function delete($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);


        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();


        return $this->redirectToRoute('app_AfficheBook');
    }
    #[Route('/ShowBook/{ref}', name: 'app_detailBook')]

    public function showBook($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);
        if (!$book) {
            return $this->redirectToRoute('app_AfficheBook');
        }

        return $this->render('book/show.html.twig', ['b' => $book]);

}


 //Query Builder: Question 2
 #[Route('/book/list/search', name: 'app_book_search', methods: ['GET', 'POST'])]
 public function searchBookByRef(Request $request, BookRepository $bookRepository): Response
 {
     $book = new Book();
     $form = $this->createForm(SearchBookType::class, $book);
     $form->handleRequest($request);
     if ($form->isSubmitted()) {
         return $this->render('book/listSearch.html.twig', [
             'books' => $bookRepository->showAllBooksByRef($book->getRef()),
             'f' => $form->createView()
         ]);
     }
     return $this->render('book/listSearch.html.twig', [
         'books' => $bookRepository->findAll(),
         'f' => $form->createView()
     ]);
 }

//Query Builder: Question 3
#[Route('/book/list/author', name: 'app_book_list_author', methods: ['GET'])]
public function showOrderedBooksByAuthor(BookRepository $bookRepository): Response
{
    return $this->render('book/listBookAuthor.html.twig', [
        'books' => $bookRepository->booksListByAuthors(),
    ]);
}

 //Query Builder: Question 4
 #[Route('/book/list/QB', name: 'app_book_list_author_date', methods: ['GET'])]
 public function showBooksByDateAndNbBooks(BookRepository $bookRepository): Response
 {
     return $this->render('book/listBookDateNbBooks.html.twig', [
         'books' => $bookRepository->showBooksByDateAndNbBooks(10, '2023-01-01'),
     ]);
 }

     //Query Builder: Question 5
     #[Route('/book/list/author/update/{category}', name: 'app_book_list_author_update', methods: ['GET'])]
     public function updateBooksCategoryByAuthor($category, BookRepository $bookRepository): Response
     {
         $bookRepository->updateBooksCategoryByAuthor($category);
         return $this->render('book/listBookAuthor.html.twig', [
             'books' => $bookRepository->findAll(),
         ]);
     }

  //DQL: Question 1
  #[Route('/book/NbrCategory', name: 'book_Count')]
  function NbrCategory(BookRepository $repo)
  {
      $nbr = $repo->NbBookCategory();
      return $this->render('book/showNbrCategory.html.twig', [
          'nbr' => $nbr,
      ]);
  }

  //DQL: Question 2
  #[Route('/book/showBookTitle', name: 'book_showBookByTitle')]
  function showTitleBook(BookRepository $repo)
  {
      $books = $repo->findBookByPublicationDate();
      return $this->render('book/showBooks.html.twig', [
          'books' => $books,
      ]);
  }

}