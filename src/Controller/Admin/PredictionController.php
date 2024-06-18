<?php

namespace App\Controller\Admin;

use App\Entity\Prediction;
use App\Form\PredictionType;
use App\Repository\EventRepository;
use App\Repository\PredictionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prediction')]
class PredictionController extends AbstractController
{
    #[Route('/', name: 'app_prediction_index', methods: ['GET'])]
    public function index(PredictionRepository $predictionRepository): Response
    {
        return $this->render('prediction/index.html.twig', [
            'predictions' => $predictionRepository->findAll(),
        ]);
    }

    #[Route('/new_many', name: 'app_prediction_new_many', methods: ['GET', 'POST'])]
    public function createMany(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, UserRepository $userRepository): Response
    {
        if ($request->getMethod() === 'POST') {
            $payload = $request->getPayload()->all();
            foreach ($payload['predictions'] as $userId => $predictions) {
                foreach ($predictions as $eventId => $p) {
                    if (empty($p['hostTeamScores']) && empty($p['guestTeamScores'])) {
                        continue;
                    }
                    $user = $userRepository->find($userId);
                    $event = $eventRepository->find($eventId);

                    $prediction = new Prediction();
                    $prediction->setUser($user);
                    $prediction->setEvent($event);
                    $prediction->setHostTeamScores((int)$p['hostTeamScores']);
                    $prediction->setGuestTeamScores((int)$p['guestTeamScores']);

                    $entityManager->persist($prediction);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
        }
//        $prediction = new Prediction();
//        $form = $this->createForm(PredictionType::class, $prediction);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($prediction);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
//        }

        return $this->render('prediction/create_many.html.twig', [
            'users' => $userRepository->findAll(),
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prediction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prediction = new Prediction();
        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prediction);
            $entityManager->flush();

            return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prediction/new.html.twig', [
            'prediction' => $prediction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prediction_show', methods: ['GET'])]
    public function show(Prediction $prediction): Response
    {
        return $this->render('prediction/show.html.twig', [
            'prediction' => $prediction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prediction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prediction $prediction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prediction/edit.html.twig', [
            'prediction' => $prediction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prediction_delete', methods: ['POST'])]
    public function delete(Request $request, Prediction $prediction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prediction->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prediction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
    }
}
