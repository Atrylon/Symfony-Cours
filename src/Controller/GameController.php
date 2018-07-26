<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GameController extends Controller
{
    /**
     * @Route("/game", name="game")
     */
    public function index( Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('notice', 'Ajout effectué!');
            return $this->redirectToRoute('home');
        }

        $users = $userRepository->findAll();

        return $this->render('game/index.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_id", requirements={"id"="\d+"})
     */
    public function user(Request $request, UserRepository $userRepository, int $id){

        $user = $userRepository->find($id);


        return $this->render('game/userFiche.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/{byFirstname}", name="user_firstname")
     * @ParamConverter("user", options={"mapping"={"byFirstname"="firstname"}})
     */
    public function firstname(Request $request, UserRepository $userRepository, User $user){

        return $this->render('game/userFirstName.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/game/remove/{id}", name="user_remove")
     */
    public function remove(User $user, EntityManagerInterface $entityManager){

        $articles = $user->getArticles();
        foreach($articles as $article){
            $article->setUser(null);
        }

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin');
    }


}
