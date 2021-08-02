<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     */
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    /**
     * @Route("/add-post", name="add_post")
     */
    public function addPost(Request $request): Response
    {
        $form = $this->createForm(PostFormType::class);

        return $this->render("post/post-form.html.twig", [
            "form_title" => "Ajouter un article",
            "form_post" => $form->createView(),
        ]);
    }
}
