<?php

namespace App\Controller;


use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SerieController extends AbstractController
{

    #[Route('/serie/detail/{id}', name: 'details', methods: ['GET'])]
    public function detail(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        if (!$serie) {
            throw $this->createNotFoundException('This serie does not exist!');
        }
        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/serie/list/{page}', name: 'list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'],)]
    public function list(SerieRepository $serieRepository, int $page, ParameterBagInterface $parameters): Response
    {
        //$series = $serieRepository->findAll();

        $nbPerPage = $parameters->get('serie')['nb_max'];
        $offset = ($page - 1) * $nbPerPage;
        $criterias = [
            //   'status' => 'Returning',
            //   'genre' => 'Drama'
        ];


        $series = $serieRepository->findBy(
            $criterias,
            ['popularity' => 'DESC'],
            $nbPerPage,
            $offset
        );

        $total = $serieRepository->count($criterias);
        $totalPages = ceil($total / $nbPerPage);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => $page,
            'total_pages' => $totalPages
        ]);
    }


    #[Route('/custom-list', name: 'custom-list')]
    public function listCustom(SerieRepository $serieRepository): Response
    {

        //$series = $serieRepository->findSeriesCustom(400, 8);
        $series = $serieRepository->findSeriesWithDQl(400, 8);

        // Le requête SQL raw nécessite que l'on adapte le template (firstAirDate -> first_air_date
        //$series = $serieRepository->findSeriesWithSQl(400,8);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => 1,
            'total_pages' => 10
        ]);
    }

    #[Route('/create', name: 'serie_create')]
    public function createSerie(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Nouvelle série ajoutée');

            return $this->redirectToRoute('details', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'serie_update', requirements: ['id' => '\d+'])]
    public function update(Serie $serie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash('success', 'Une série a été mise à jour');

            return $this->redirectToRoute('details', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'serie_delete',requirements: ['id'=>'\d+'])]
    public function delete(Serie $serie,Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serie->getId(), $request->get('token'))){
            $em->remove($serie);
            $em->flush();

            $this->addFlash('success', 'La série a été supprimée');

        } else {
            $this->addFlash('danger', 'Suppression impossible');
        }

        return $this->redirectToRoute('list');

    }
}


