<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('serie', name: 'serie_')]
class SerieController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(): Response
    {

        //TODO recuperer la liste des series en BDD
        return $this->render('serie/list.html.twig');
    }
    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        dump($id);

        //TODO recupereration des infos de la serie en BDD
        return $this->render('serie/show.html.twig');
    }
    #[Route('/add', name: 'add')]
    public function add(): Response
    {

        //TODO recuperer la liste des series en BDD
        return $this->render('serie/add.html.twig');
    }

}
