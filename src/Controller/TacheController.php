<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Notifiation;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\FichierTache;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/tache")
 */
class TacheController extends AbstractController
{
    /**
     * @Route("/", name="app_tache_index", methods={"GET"})
     */
    public function index(TacheRepository $tacheRepository, Security $security): Response
    {
        $user = $security->getUser();

        // Tâches créées par l'utilisateur
        $ownTaches = $tacheRepository->findBy(['owner' => $user]);

        // Tâches partagées avec lui
        $partageTache = $tacheRepository->findTachePartagerAvecUser($user);

        // Fusionner les deux sans doublons
        $allTaches = array_merge($ownTaches, $partageTache);

        // Supprimer les doublons manuellement par ID
        $tachesUniques = [];
        $ids = [];

        foreach ($allTaches as $t) {
            if (!in_array($t->getId(), $ids)) {
                $ids[] = $t->getId();
                $tachesUniques[] = $t;
            }
        }

        return $this->render('tache/index.html.twig', [
            'tache' =>  $tachesUniques,
        ]);
    }

    /**
     * @Route("/new", name="app_tache_new", methods={"GET", "POST"})
     */
    public function new(Request $request,EntityManagerInterface $entityManager, Security $security, SluggerInterface $slugger): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        //notification
        foreach ($tache->getPartagerAvec() as $sharedUser) {
            if ($sharedUser !== $this->getUser()) {
                $notification = new Notifiation();
                $notification->setUser($sharedUser);
                $notification->setMessage("Une nouvelle tâche vous a été partagée : " . $tache->getTitre());
                $notification->setIsRead(false);
                $notification->setCreatedAt(new \DateTime());

                $entityManager->persist($notification);
            }
        }

        $entityManager->flush();

        if ($form->isSubmitted() && $form->isValid()) {
            $tache->setOwner($security->getUser()); // 👈 on définit automatiquement le propriétaire
            $files = $form->get('Fichier')->getData();

            foreach ($files as $file) {
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                    try {
                        $file->move(
                            $this->getParameter('fichier_directory'), // à définir dans services.yaml
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // gérer l’erreur
                    }

                    $attachment = new FichierTache();
                    $attachment->setNomFichier($originalFilename);
                    $attachment->setRouteFichier($newFilename);
                    $attachment->setTache($tache);
                    $entityManager->persist($attachment);
                }
            }

            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tache/new.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/tache/{id}", name="app_tache_show", methods={"GET"})
     */
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_tache_edit", methods={"GET", "POST"})
     */
    public function edit(EntityManagerInterface $entityManager,Request $request, Tache $tache, TacheRepository $tacheRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

        /** @var UploadedFile[] $fichiers */
        $files = $form->get('Fichier')->getData(); // assure-toi que ce champ s'appelle 'fichiers'

        foreach ($files as $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                    $file->move(
                    $this->getParameter('fichier_directory'), // à définir dans services.yaml
                    $newFilename
                    );
            } catch (FileException $e) {
                        // gérer l’erreur
            }

            $newFichier = new FichierTache();
            $newFichier->setNomFichier($originalFilename);
            $newFichier->setRouteFichier($newFilename);
            $newFichier->setTache($tache); // relation inverse
            $entityManager->persist($newFichier);

            $tache->addFichierTache($newFichier); // assure-toi que cette méthode existe bien
        }

        $tacheRepository->add($tache, true);

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }

        return $this->renderForm('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tache_delete", methods={"POST"})
     */
    public function delete(Request $request, Tache $tache, TacheRepository $tacheRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tache->getId(), $request->request->get('_token'))) {
            $tacheRepository->remove($tache, true);
        }

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }

    /** 
     *  @Route("/tache/{id}/toggle", name= "tache_toggle", methods= {"POST"})
    */
    public function toggle(Tache $tache, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Vérifie que l'utilisateur est bien le propriétaire
        if ($tache->getCollab() !== $security->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette tâche.');
        }

        $tache->setEstFait(!$tache->getEstFait()); // Inversion
        $entityManager->flush();

        return $this->redirectToRoute('app_tache_index');
    }


     /**
     * @Route("/calendar", name="calendar_view", methods={"GET"})
     */
    public function callendrier(): Response
    {
        return $this->render('tache/calendar.html.twig');
    }

    /**
     * API qui fournit les événements du calendrier
     * 
     * @Route("/calendar/events", name="app_tache_calendar", methods={"GET"})
     */
    public function calendar(TacheRepository $tacheRepository, Security $security): JsonResponse
    {
       $user = $security->getUser();
        $events = $tacheRepository->findBy(['owner' => $user]);

       $formattedEvents = array_map(function ($event) {
            return [
                'title' => $event->getTitre(),
                'start' => $event->getDateDebut() ? $event->getDateDebut()->format('Y-m-d\TH:i:s') : null,
                'end'   => $event->getDeadline() ? $event->getDeadline()->format('Y-m-d\TH:i:s') : null,
            ];
        }, $events);

        return new JsonResponse($formattedEvents);
    }

}
