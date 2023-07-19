<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use App\Form\TodoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TodoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TodoRepository $todoRepository;

    public function __construct(EntityManagerInterface $entityManager, TodoRepository $todoRepository)
    {
        $this->entityManager = $entityManager;
        $this->todoRepository = $todoRepository;
    }

    #[Route('/', name: 'todo_index', methods: ['GET'])]
    public function index(TodoRepository $todoRepository): Response
    {
        $user = $this->getUser();
        $todos = $todoRepository->findBy(['userAccount' => $user]);
    
        return $this->render('todo/index.html.twig', ['todos' => $todos]);
    }
    

    #[Route('/new', name: 'todo_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the currently logged in user
            $user = $this->getUser();
            // Set the user on the Todo object
            if ($user) {
                $todo->setUserAccount($user);
            }
            $this->entityManager->persist($todo);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('todo_index');
        }
    
        return $this->render('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}', name: 'todo_show', methods: ['GET'])]
    public function show(Todo $todo): Response
    {
        
        $deleteForm = $this->createFormBuilder()->getForm();

    
        return $this->render('todo/show.html.twig', [
            'todo' => $todo,
            'form' => $deleteForm->createView(),
        ]);
    }
    

    #[Route('/{id}/edit', name: 'todo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Todo $todo): Response
    {
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('todo_index');
        }

        return $this->render('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'todo_delete', methods: ['POST'])]
    public function delete(Request $request, Todo $todo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $todo->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($todo);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('todo_index');
    }
}
