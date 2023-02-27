<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

#[Route ('serie', name: 'serie_')]
class SerieController extends AbstractController
{
    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], methods: "GET")]
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {

        //TODO recuperer la liste des series en BDD
        //on recupere toutes les series en passant par le repository
        // $series = $serieRepository->findAll();

        //$series = $serieRepository->findBy(["status" => "ended"], ["popularity"=>'DESC'], 10);
        //$series = $serieRepository->findByStatus("ended");
        //$series = $serieRepository->findBy([],["vote"=>"DESC"], 50);

        //nombre de series dans ma table
        $nbSerieMax = $serieRepository->count([]);
        $maxPage = ceil($nbSerieMax / SerieRepository::SERIE_LIMIT);
        if ($page >= 1 && $page <= $maxPage) {
            $series = $serieRepository->findBestSeries($page);
        } else {
            throw $this->createNotFoundException("Ooops ! Page not found !");
        }

        dump($series);
        return $this->render('serie/list.html.twig', [
            // on envoie les donnees a la vue
            'series' => $series,
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);
        dump($id);

        if (!$serie) {
            //lance une error 404 si la serie n'existe pas
            throw $this->createNotFoundException("Oops ! serie not found !");
        }

        //TODO recupereration des infos de la serie en BDD
        return $this->render('serie/show.html.twig', ['serie' => $serie]);
    }

    #[Route('/add', name: 'add')]
    public function add(SerieRepository $serieRepository, Request $request, EntityManagerInterface $entityManager): Response
    {

        $serie = new Serie();
        //creation de'une instance de form lie a une instance de serie

        $serieForm = $this->createForm(SerieType::class, $serie);

        //metode qui extrait les elements du formulaire de la requete
        $serieForm->handleRequest($request);


        if ($serieForm->isSubmitted() && $serieForm->isValid()) {

            //upload photo
            /**
             * @var UploadedFile $file
             */
            $file = $serieForm->get('poster')->getData();

            //creation de un nouveau nom
            $newFileName = $serie->getName(). "-" . uniqid() . "." . $file->guessExtension();
            //copy du ficher
            $file->move('img/posters/series', $newFileName);

            $serie->setPoster($newFileName);

            //sauvegarde en BDD la nouvelle serie
            $serieRepository->save($serie, true);

            $this->addFlash("success", "Serie added !");

            //redirige vers la page de detail de la serie
            return $this->redirectToRoute('serie_show', ['id' => $serie->getId()]);

        }


        dump($serie);
//        $serie
//            ->setName("Le magicien")
//            ->setBackdrop("backdrop.png")
//            ->setDateCreated(new \DateTime())
//            ->setGenres("Comedy")
//            ->setFirstAirDate(new \DateTime('2022-02-02'))
//            ->setLastAirDate(new \DateTime("-6 month"))
//            ->setPopularity(850.52)
//            ->setPoster("poster.png")
//            ->setTmdbId(123456)
//            ->setVote(8.5)
//            ->setStatus("Ended");
//
//        $entityManager->persist($serie);
//        $entityManager->flush();
//        dump($serie);
//
//        $serieRepository->save($serie, true);
//
//        dump($serie);
//        $serie->setName("The last of us");
//        $serieRepository->save($serie, true);
//
//        dump($serie);
//
//        $serieRepository->remove($serie, true);
//        dump($serie);


        //TODO recuperer la liste des series en BDD
        return $this->render('serie/add.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id, SerieRepository $serieRepository)
    {
        $serie = $serieRepository->find($id);
        if ($serie) {
            $serieRepository->remove($serie, true);
            $this->addFlash("warning", "Serie is deleted");
        } else {
            throw $this->createNotFoundException("This serie can't be deleted !");
        }

        return $this->redirectToRoute('serie_list');

    }

}