<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExoController
 * @package App\Controller
 *
 * @Route("/exo")
 */
class ExoController extends AbstractController
{
    /**
     * Faire un formulaire en POST avec :
     * - email (text)
     * - message (textarea)
     *
     * Si le formulaire est envoyé, vérifier que les 2 champs sont remplis
     * Si non, afficher un message d'erreur
     * Si oui, enregistrer les valeurs en session et rediriger
     * vers une nouvelle page qui affiche l'email et le message
     * et vider la session
     * Dans cette page, si la session est vide rediriger vers le formulaire
     *
     * @Route("/")
     */
    public function index(Request $request)
    {
        $erreur = '';

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            if (!empty($email) && !empty($message)) {
                $session = $request->getSession();

                $session->set('email', $email);
                $session->set('message', $message);

                return $this->redirectToRoute('app_exo_afficher');
            } else {
                $erreur = 'Tous les champs doivent être remplis';
            }
        }

        return $this->render(
            'exo/index.html.twig',
            [
                'erreur' => $erreur
            ]
        );
    }

    /**
     * @Route("/afficher")
     */
    public function afficher(Request $request)
    {
        $session = $request->getSession();

        if (empty($session->all())) {
            return $this->redirectToRoute('app_exo_index');
        }

        $email = $session->get('email');
        $message = $session->get('message');

        $session->clear();

        return $this->render(
            'exo/afficher.html.twig',
            [
                'email' => $email,
                'message' => $message
            ]
        );
    }
}
