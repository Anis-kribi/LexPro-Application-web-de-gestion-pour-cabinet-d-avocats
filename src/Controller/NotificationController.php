<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/notifications')]
#[IsGranted('ROLE_ADMIN')]
class NotificationController extends AbstractController
{
    #[Route('', name: 'api_notifications_get', methods: ['GET'])]
    public function getNotifications(NotificationRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        
        $notifications = $repo->findUnreadForUser($user, 10);
        $count = $repo->countUnreadForUser($user);

        $data = [];
        foreach ($notifications as $n) {
            $data[] = [
                'id' => $n->getId(),
                'message' => $n->getMessage(),
                'type' => $n->getType(),
                'time' => $n->getCreatedAt()->format('d/m/Y H:i'),
                'link' => $n->getLink(),
            ];
        }

        return $this->json([
            'count' => $count,
            'notifications' => $data
        ]);
    }

    #[Route('/{id}/read', name: 'api_notifications_read', methods: ['POST'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): JsonResponse
    {
        // Security check
        if ($notification->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'You do not own this notification'], 403);
        }

        $notification->setIsRead(true);
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/read-all', name: 'api_notifications_read_all', methods: ['POST'])]
    public function markAllAsRead(NotificationRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $notifications = $repo->findBy(['user' => $user, 'isRead' => false]);

        foreach ($notifications as $n) {
            $n->setIsRead(true);
        }

        $em->flush();

        return $this->json(['success' => true]);
    }
}
