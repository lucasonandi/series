<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

#[Route ('serie', name: 'serie_')]
class SerieController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(SerieRepository $serieRepository): Response
    {

        //TODO recuperer la liste des series en BDD
        //on recupere toutes les series en passant par le repository
       // $series = $serieRepository->findAll();

        //$series = $serieRepository->findBy(["status" => "ended"], ["popularity"=>'DESC'], 10);
        //$series = $serieRepository->findByStatus("ended");
        //$series = $serieRepository->findBy([],["vote"=>"DESC"], 50);
        $series = $serieRepository->findBestSeries();


        dump($series);
        return $this->render('serie/list.html.twig', [
            // on envoie les donnees a la vue
            'series'=>$series
        ]);
    }
    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);
        dump($id);

        if (!$serie){
            //lance une error 404 si la serie n'existe pas
            throw $this->createNotFoundException("Oops ! serie not found !");
        }

        //TODO recupereration des infos de la serie en BDD
        return $this->render('serie/show.html.twig', ['serie' =>$serie]);
    }
    #[Route('/add', name: 'add')]
    public function add(SerieRepository $serieRepository, EntityManagerInterface $entityManager): Response
    {

        $serie = new Serie();

        $serie
            ->setName("Le magicien")
            ->setBackdrop("backdrop.png")
            ->setDateCreated(new \DateTime())
            ->setGenres("Comedy")
            ->setFirstAirDate(new \DateTime('2022-02-02'))
            ->setLastAirDate(new \DateTime("-6 month"))
            ->setPopularity(850.52)
            ->setPoster("poster.png")
            ->setTmdbId(123456)
            ->setVote(8.5)
            ->setStatus("Ended");

        $entityManager->persist($serie);
        $entityManager->flush();
//        dump($serie);
//
//        $serieRepository->save($serie, true);
//
//        dump($serie);
//        $serie->setName("The last of us");
//        $serieRepository->save($serie, true);
//
//        dump($serie);

        $serieRepository->remove($serie, true);
        dump($serie);


        //TODO recuperer la liste des series en BDD
        return $this->render('serie/add.html.twig');
    }

}
