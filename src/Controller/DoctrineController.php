<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        /*
         * en version longue :
        $repository = $em->getRepository(User::class);
        $user = $repository->find($id);
        */

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

    /**
     * @Route("/list-users")
     */
    public function listUsers()
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(User::class);

        // retourne tous les utilisateurs en bdd
        // sous forme d'un tableau d'ibjets User
        $users = $repository->findAll();

        dump($users);

        return $this->render(
            'doctrine/list_users.html.twig',
            [
                'users' => $users
            ]
        );
    }

    /**
     * @Route("/search-email/{email}")
     */
    public function searchEmail($email)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);

        // findOneBy() quand on est sûr qu'il n'y aura pas plus d'un résultat
        // Retourne un objet User ou null s'il n'y a pas de résultat
        $user = $repository->findOneBy([
            'email' => $email
        ]);

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

    /**
     * @Route("/search-firstname/{firstname}")
     */
    public function searchFirstname($firstname)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);

        // retourne un tableau d'objets User filtrés sur le prénom
        // s'il n'y a pas de résultats, retourne un tableau vide
        $users = $repository->findBy([
            'firstname' => $firstname
        ]);

        /*
         * équivalent d'un findAll() avec un ORDER BY lastname, firstname :
         *
        $users = $repository->findBy(
            [],
            [
                'lastname' => 'ASC',
                'firstname' => 'ASC'
            ]
        );
        */

        return $this->render(
            'doctrine/list_users.html.twig',
            [
                'users' => $users
            ]
        );
    }

    /**
     * @Route("/create-user")
     */
    public function createUser(Request $request)
    {
        // si le formulaire a été soumis
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            dump($data); // éq. $_POST

            // on instancie un nouvel objet User
            $user = new User();
            // et on sette ses attributs avec les données du formulaire
            $user
                ->setLastname($data['lastname'])
                ->setFirstname($data['firstname'])
                ->setEmail($data['email'])
                // le setter de birthdate attend un objet DateTime
                ->setBirthdate(new \DateTime($data['birthdate']))
            ;

            dump($user);

            $em = $this->getDoctrine()->getManager();

            // dit qu'il faudra enregistrer le User en bdd
            // au prochain appel de la méthode flush()
            $em->persist($user);
            // enregistrement effectif
            $em->flush();
        }

        return $this->render(
            'doctrine/create_user.html.twig'
        );
    }

    /**
     * @Route("/update-user/{id}")
     */
    public function updateUser(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->find($id);

        return $this->render(
            'doctrine/update_user.html.twig',
            [
                'user' => $user
            ]
        );
    }
}
