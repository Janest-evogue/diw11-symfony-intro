<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/hello")
     */
    public function hello()
    {
        return $this->render('index/hello.html.twig');
    }

    /**
     * Une route avec une partie variable (entre accolades)
     * le $qui en paramètre de la méthode contient la valeur
     * de cette partie variable
     *
     * @Route("/bonjour/{qui}")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bonjour($qui)
    {
        return $this->render(
            'index/bonjour.html.twig',
            // le tableau en 2e paramètre de la méthode render()
            // permet de passer des variables au template :
            // Le nom de la variable est la clé dans ce tableau
            [
                'qui' => $qui,
            ]
        );
    }

    /**
     * valeur par défaut pour la partie variable :
     * la route matche /salut/UnNom : $qui vaut "UnNom",
     * et matche aussi /salut : $qui vaut "toi"
     *
     * @Route("/salut/{qui}", defaults={"qui": "toi"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function salut($qui)
    {
        return $this->render(
            'index/salut.html.twig',
            [
                'qui' => $qui
            ]
        );
    }

    /**
     * La route matche /coucou/Julien et /coucou/Julien-Anest
     *
     * @Route("/coucou/{firstname}/{lastname}", defaults={"lastname": ""})
     */
    public function coucou($firstname, $lastname)
    {
        $name = $firstname;

        if ($lastname != '') {
            $name .= ' ' . $lastname;
        }

        return $this->render(
            'index/coucou.html.twig',
            [
                'name' => $name
            ]
        );
    }

    /**
     * heure doit forcément être un nombre (\d+ en expression régulière)
     *
     * @Route("/bonsoir/{heure}", requirements={"heure": "\d+"})
     *
     * @param $heure
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bonsoir($heure)
    {
        return $this->render(
            'index/bonsoir.html.twig',
            [
                'heure' => $heure
            ]
        );
    }
}
