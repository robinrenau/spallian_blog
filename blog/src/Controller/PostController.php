<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PostFormType;

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
    public function addPost(Request $request): Response
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

            return $this->redirectToRoute('post');
        }

        return $this->render("post/new.html.twig", [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/post/{id}", name="post")
     */
    public function post(int $id): Response
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        return $this->render("post/show.html.twig", [
            "post" => $post,
        ]);
    }
}
