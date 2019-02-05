<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DoctrineController
 * @package App\Controller
 *
 * @Route("/doctrine")
 */
class DoctrineController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('doctrine/index.html.twig', [
            'controller_name' => 'DoctrineController',
        ]);
    }

    /**
     * le requirements oblige le paramètre {id} de l'url à être un nombre
     * @Route("/user/{id}", requirements={"id": "\d+"})
     */
    public function getOneUser($id)
    {
        // gestionnaire d'entités de Doctrine
        $em = $this->getDoctrine()->getManager();

        /*
         * User::Class = 'App\Entity\User'
         * Retourne un objet User dont les attributs sont settés à partir
         * de la bdd
         */
        $user = $em->find(User::class, $id);

        dump($user);

        /*
         * s'il n'y a pas de user en bdd avec l'id passé à la méthode find(),
         * elle retourne null
         */
        if (is_null($user)) {
            // 404
            throw new NotFoundHttpException();
        }

        return $this->render(
            'doctrine/get_one_user.html.twig',
            [
                'user' => $user
            ]
        );
    }
}
