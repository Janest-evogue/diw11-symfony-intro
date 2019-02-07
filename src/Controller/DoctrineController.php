<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        dump($user);

        if ($request->isMethod('POST')) {
            $user->setEmail($request->request->get('email'));

            $em->persist($user);
            $em->flush();
        }

        return $this->render(
            'doctrine/update_user.html.twig',
            [
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/delete-user/{id}")
     */
    public function deleteUser($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->find($id);

        // si l'id existe en bdd
        if (!is_null($user)) {
            // suppression de l'utilisateur en bdd
            $em->remove($user);
            $em->flush();

            return new Response('Utilisateur supprimé');
        } else {
            return new Response('Utilisateur inexistant');
        }
    }

    /**
     * ParamConverter :
     * le paramètre dans l'url s'appelle id
     * comme la clé primaire de la table user.
     * En typant User le paramètre passé à la méthode, on récupère dans $user
     * un objet User qui est défini à partir d'un SELECT sur la table user
     * sur cet id
     * Si l'id n'existe pas en bdd, le paramConverter retourne une 404
     *
     * @Route("/another-user/{id}")
     */
    public function getAnotherUser(User $user)
    {
        return $this->render(
            'doctrine/get_one_user.html.twig',
            [
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/publications/author/{id}")
     */
    public function publicationsByAuthor(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Publication::class);

        $publications = $repository->findBy([
            'author' => $user
        ]);

        return $this->render(
            'doctrine/publications.html.twig',
            [
                'publications' => $publications
            ]
        );
    }

    /**
     * @Route("/user/{id}/publications")
     */
    public function userPublications(User $user)
    {
        /*
         * En appelant le getter de l'attribut $publications d'un
         * objet User, Doctrine va automatiquement faire une requête en bdd
         * pour y mettre les publications liées au User grâce
         * à l'annotation OneToMany sur l'attribut (lazy loading)
         */

        return $this->render(
            'doctrine/publications.html.twig',
            [
                'publications' => $user->getPublications()
            ]
        );
    }

    /**
     * @Route("/users/team/{id}")
     */
    public function userByTeam(Team $team)
    {
        // le lazy loading fonctionne aussi en relation ManyToMany

        return $this->render(
            'doctrine/list_users.html.twig',
            [
                'users' => $team->getUsers()
            ]
        );
    }

    /**
     * @Route("/team/{id}/add-user")
     */
    public function addUserToTeam(Request $request, Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        // permet un findAll() avec un tri sur le nom :
        $users = $repository->findBy([], ['lastname' => 'ASC']);

        if ($request->isMethod('POST')) {
            // $_POST['user']
            $userId = $request->request->get('user');

            $user = $repository->find($userId);

            $team->getUsers()->add($user);

            $em->persist($team);
            $em->flush();
        }

        return $this->render(
            'doctrine/add_user_to_team.html.twig',
            [
                'users' => $users,
                'team' => $team
            ]
        );
    }
}
