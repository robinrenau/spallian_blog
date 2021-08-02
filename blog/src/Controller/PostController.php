<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PostFormType;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="posts")
     */
    public function index(): Response
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();

        return $this->render('post/index.html.twig', [
             "posts" => $posts,
        ]);
    }

    /**
     * @Route("/add-post", name="add_post")
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setUser($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts');
        }

        return $this->render("post/new.html.twig", [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/post/{id}", name="post")
     */
    public function show(int $id): Response
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        return $this->render("post/show.html.twig", [
            "post" => $post,
        ]);
    }

    /**
     * @Route("/modify-post/{id}", name="modify_post")
     */
    public function edit(Request $request, Post $post): Response
    {
        if ($this->getUser() != $post->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier cet article !");
        }
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('posts', ['id' => $post->getUser()->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete-post/{id}", name="delete_post")
    
     */
    public function delete(int $id, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('delete', $request->query->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }else{
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute("posts");
        }
    }
}
