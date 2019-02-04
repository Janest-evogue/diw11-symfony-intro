<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TwigController
 * @package App\Controller
 *
 * préfixe de route pour toutes les routes définies dans la classe
 * @Route("/twig")
 */
class TwigController extends AbstractController
{
    /**
     * avec le préfixe de route de la classe,
     * l'url de cette route est /twig ou /twig/
     * @Route("/")
     */
    public function index()
    {
        $date = new \DateTime();

        echo $date->format('d/m/y');

        return $this->render(
            'twig/index.html.twig',
            [
                'auj' => new \DateTime()
            ]
        );
    }
}
