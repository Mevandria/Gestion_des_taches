<?php
// src/Controller/NotificationController.php

namespace App\Controller;

use App\Repository\NotifiationRepository;
use App\Entity\Notifiation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;


class NotificationController extends AbstractController
{
    /**
     *  @Route("/notifications", name= "notification_index")
     */
    public function index(NotifiationRepository $notificationRepository, Security $security): Response
    {
        $user = $security->getUser();

        $notifications = $notificationRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * @Route("/notifications/mark-as-read/{id}", name= "notification_mark_as_read")
     */
    public function markAsRead(Notifiation $notification, EntityManagerInterface $em, Security $security): Response
    {
        if ($notification->getUser() !== $security->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $notification->setIsRead(true);
        $em->flush();

        return $this->redirectToRoute('notification_index');
    }

}

?>
