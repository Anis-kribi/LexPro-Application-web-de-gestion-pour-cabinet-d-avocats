<?php

namespace App\EventSubscriber;

use App\Entity\Client;
use App\Entity\Document;
use App\Entity\Dossier;
use App\Entity\EntreeDeTemps;
use App\Entity\Factures;
use App\Entity\Notification;
use App\Entity\RendezVous;
use App\Entity\Tache;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
#[AsEventListener(event: KernelEvents::TERMINATE, method: 'onKernelTerminate')]
class NotificationSubscriber
{
    private array $pendingNotifications = [];

    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $supportedEntities = [
            Client::class => 'Client',
            Dossier::class => 'Dossier',
            Factures::class => 'Facture',
            RendezVous::class => 'Rendez-vous',
            Document::class => 'Document',
            Tache::class => 'Tâche',
            EntreeDeTemps::class => 'Entrée de temps',
        ];

        $entityClass = get_class($entity);
        if (str_contains($entityClass, "Proxies\\__CG__\\")) {
            $entityClass = get_parent_class($entity);
        }

        if (!array_key_exists($entityClass, $supportedEntities)) {
            return;
        }

        $user = $this->security->getUser();

        // If the creator is an Admin, don't notify admins
        if ($user instanceof User && in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return;
        }

        $creatorName = ($user instanceof User) ? $user->getFullName() : 'Un système';
        $entityName = $supportedEntities[$entityClass];
        
        $message = "Nouveau $entityName ajouté par $creatorName";

        // Find all admins
        $admins = $this->userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_ADMIN"%')
            ->getQuery()
            ->getResult();

        foreach ($admins as $admin) {
            $notification = new Notification();
            $notification->setMessage($message);
            $notification->setType($entityName); // use human readable type
            $notification->setUser($admin);
            $notification->setIsRead(false);

            $this->pendingNotifications[] = $notification;
        }
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (empty($this->pendingNotifications)) {
            return;
        }

        foreach ($this->pendingNotifications as $notification) {
            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();
        $this->pendingNotifications = [];
    }
}
