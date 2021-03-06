<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{
    /**
     * @Route("/article", name="article")
     */
    public function index(Request $request, ArticleRepository $articleRepository)
    {

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('notice', 'Article ajouté!');
            return $this->redirectToRoute('home');
        }

        $articles= $articleRepository->findAll();

        return $this->render('article/index.html.twig', [
            'formArticle' => $form->createView(),
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/remove/{id}", name="article_remove")
     */
    public function remove(Article $article, EntityManagerInterface $entityManager){
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('home');
    }
}
