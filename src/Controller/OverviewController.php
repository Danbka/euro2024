<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewController extends AbstractController
{
    #[Route('/overview', name: 'app_overview')]
    public function overview(UserRepository $userRepository): Response
    {
        $users = [];

        foreach ($userRepository->fetchAll() as $user) {
            $points = 0;
            foreach ($user->getPredictions() as $prediction) {
                $points += $prediction->getPoints();
            }

            $users[] = [
                'user' => $user,
                'points' => $points,
            ];
        }

        usort($users, fn(array $user1, array $user2): bool => $user1['points'] < $user2['points']);

        return $this->render('overview/index.html.twig', [
            'users' => $users,
        ]);
    }
}
