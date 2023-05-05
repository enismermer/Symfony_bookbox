<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Borrow;
use App\Entity\Box;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\BoxRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    #[Route('/api/book/{id_book}', name: 'app_api_id_book', methods: "GET")]
    public function index(BookRepository $bookRepository, $id_book): Response
    {
        $book = $this->getDoctrine()
        ->getRepository(Book::class)
        ->findOneBy(["id" => $id_book]);
       return $this->json($book, 200, [], ['groups' => 'book:read']);
    }

    #[Route('/api', name: 'app_api_store', methods: "POST")]
    public function store(Request $request, SerializerInterface $serializer, EntityManagerInterface $em) 
    {
        $jsonRecu = $request->getContent();

        $book = $serializer->deserialize($jsonRecu, Book::class, 'json');

        $em->persist($book);
        $em->flush();

        return $this->json($book, 201, []);
    }

    #[Route('/api/box/{id}', name: 'app_api_box', methods: "GET")]
    public function apibox(BoxRepository $boxRepository, $id): Response
    {
        $id = $this->getDoctrine()
            ->getRepository(Box::class)
            ->findOneBy(["id" => $id]);
            if (!$id) {
                return $this->json(["error" => " Utilisateur inexistant"], 201);
                }

       return $this->json($id, 200, [], ['groups' => 'book:read']);

       
    }

    #[Route('/api/v1/user/login', name: 'app_api', methods: "POST")]
    public function addBook(Request $request, SerializerInterface $serializer): Response {
        $uuid = $request->get("uuid");
        try {
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(["uuid" => $uuid]);
            if (!$users) {
                return $this->json(["error" => " Utilisateur inexistant"], 201);
            }

            $json = $serializer->serialize($users, 'json', ["groups" => "book:read"]);
            $response = new Response($json, 200, ["Content-Type" => "application/json"]);
            return $response;
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'staut' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/api/v1/books/{id_book}/borrowBook/{id_user}', name: 'post_books_borrow', methods: ["GET"])]
    public function borrowBook($id_book, $id_user, SerializerInterface $serializer, Request $request, BookRepository $bookRepository, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $book   = $bookRepository->find($id_book);
        $user   = $userRepository->find($id_user);
        
        $borrow = new Borrow();

        if ($book->isIsAvailable()) {
            
            $borrow->AddIdBook($book);
            $borrow->setIdUser($user);
            $borrow->setDateBorrow(new DateTime());
            $book->setIsAvailable(false);

            $em->persist($borrow);
            $em->flush();
            return $this->json(["Message" => "Book borrowed!"], 200);
        }

        
        $borrow->setDateReturn(new DateTime());
        $book->setIsAvailable(true);

        $em->persist($borrow);
        $em->flush();
        return $this->json(["Message" => "Book est rendu"], 201);
    }
}
