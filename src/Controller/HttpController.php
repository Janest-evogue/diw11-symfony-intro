<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HttpController
 * @package App\Controller
 * @Route("/http")
 */
class HttpController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {


        return $this->render('http/index.html.twig', [
            'controller_name' => 'HttpController',
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/request")
     */
    public function request(Request $request)
    {
        dump($_GET);

        /*
         * $request->query est l'attribut de l'objet Request qui fait
         * référence au tableau $_GET, sa méthode all() retourne la totalité
         * de $_GET
         */
        dump($request->query->all());

        /*
         * dump($_GET['nom'])
         * null si 'nom' n'est pas dans la query string
         */
        dump($request->query->get('nom'));

        dump($request->query->get('nom', 'anonyme'));

        $nom = (!empty($_GET['nom'])) ? $_GET['nom'] : 'anonyme';
        $nom = $request->query->get('nom', 'anonyme');

        // GET ou POST
        dump($request->getMethod());

        // éq. if (!empty($_POST))
        if ($request->isMethod('POST')) {
            echo 'on a reçu des données de formulaire';

            /*
             * $request->request est l'attribut de l'objet Request qui fait
             * référence au tableau $_POST, sa méthode all() retourne la totalité
             * de $_POST
             */
            dump($request->request->all());

            // $request->request fonctionne de la même manière que $request->query
            dump($_POST['nom']);
            dump($request->request->get('nom'));
        }

        return $this->render('http/request.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/session")
     */
    public function session(Request $request)
    {
        // pour accéder à la session
        $session = $request->getSession();

        // pour ajouter des éléments à la session
        $session->set('nom', 'Anest');
        $session->set('prenom', 'Julien');

        dump($_SESSION);

        // accède à l'élément 'nom' de la session
        dump($session->get('nom'));

        // tous les éléments de la session
        dump($session->all());

        // supprime un élément de la session
        $session->remove('nom');

        dump($session->all());

        // vide la session
        $session->clear();

        dump($session->all());

        return $this->render('http/session.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/response")
     */
    public function response(Request $request)
    {
        // une réponse qui contient du texte brut
        $response = new Response('Ma réponse');

        // if ($_GET['type'] == 'twig')
        // http://localhost:8000/http/response?type=twig
        if ($request->query->get('type') == 'twig') {
            // $this->render() retourne un objet Response
            // dont le contenu est le HTML construit par le template
            $response = $this->render('http/response.html.twig');
        // http://localhost:8000/http/response?type=json
        } elseif ($request->get('type') == 'json') {
            $exemple = [
                'nom' => 'Anest',
                'prenom' => 'Julien'
            ];

            /* en json :
             {
                "nom": "Anest",
                "prenom" : "Julien"
             }
             */

            $response = new JsonResponse($exemple);
            // éq. $response = new Response(json_encode($exemple));
        // http://localhost:8000/http/response?found=no
        } elseif ($request->query->get('found') == 'no') {
            // on jette cette exception pour retourner une 404
            throw new NotFoundHttpException();
        // http://localhost:8000/http/response?redirect=index
        } elseif ($request->query->get('redirect') == 'index') {
            // redirige vers la page dont la route a pour nom app_index_index
            $response = $this->redirectToRoute('app_index_index');
        } elseif ($request->query->get('redirect') == 'bonjour') {
            // redirection vers une route qui contient une partie variable
            $response = $this->redirectToRoute(
                'app_index_bonjour',
                [
                    'qui' => 'le monde'
                ]
            );
        }

        // une méthode doit toujours retourner un objet instance de Response
        return $response;
    }

    /**
     * @Route("/flash")
     */
    public function flash()
    {
        // ajoute un message flash de type 'success'
        $this->addFlash('success', 'Message de succès');

        // redirige vers /http/flashed
        return $this->redirectToRoute('app_http_flashed');
    }

    /**
     * @Route("/flashed")
     */
    public function flashed()
    {
        return $this->render('http/flashed.html.twig');
    }
}
