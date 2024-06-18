<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\PredictionRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PredictionController extends AbstractController
{
    #[Route('/my-predictions', name: 'app_my_predictions_store', methods: ['POST'])]
    public function storeMyPredictions(#[CurrentUser] ?User $user, Request $request, EventRepository $eventRepository, PredictionRepository $predictionRepository, EntityManagerInterface $entityManager)
    {
        $payload = $request->getPayload()->all();
        foreach ($payload['predictions'] as $eventId => $p) {
            if (empty($p['hostTeamScores']) && empty($p['guestTeamScores'])) {
                continue;
            }

            $event = $eventRepository->find($eventId);
            if (!$event->isPredictable()) {
                continue;
            }


            $prediction = $predictionRepository->findOneBy(['event' => $event, 'user' => $user]) ?? new Prediction();
            $prediction->setUser($user);
            $prediction->setEvent($event);
            $prediction->setHostTeamScores((int)$p['hostTeamScores']);
            $prediction->setGuestTeamScores((int)$p['guestTeamScores']);

            $entityManager->persist($prediction);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_my_predictions_show', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/my-predictions', name: 'app_my_predictions_show', methods: ['GET'])]
    public function showMyPredictions(#[CurrentUser] ?User $user, EventRepository $eventRepository): Response
    {
        $predictions = [];
        foreach ($user->getPredictions() as $prediction) {
            $predictions[$prediction->getEvent()->getId()] = $prediction;
        }

        return $this->render('predictions/my.html.twig', [
            'events' => $eventRepository->findAll(),
            'predictions' => $predictions,
            'currentTime' => new \DateTime('-1 hour'),
        ]);
    }

    #[Route('/all-predictions', name: 'app_all_predictions_show')]
    public function showAllPredictions(EventRepository $eventRepository, UserRepository $userRepository): Response
    {
        return $this->render('predictions/all.html.twig', [
            'events' => $eventRepository->findAll(),
            'users' => $userRepository->fetchAll(),
        ]);
    }
}
