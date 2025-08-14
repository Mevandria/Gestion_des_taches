<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\TacheRepository;
use App\Repository\UserRepository;
use App\Entity\Notifiation;
use Doctrine\ORM\EntityManagerInterface;

class CheckDeadlinesCommand extends Command
{
    protected static $defaultName = 'app:check-deadlines';

    private $tacheRepository;
    private $em;

    public function __construct(TacheRepository $tacheRepository, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->tacheRepository = $tacheRepository;
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Vérifie les deadlines proches et envoie des notifications.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTime();
        $tomorrow = (clone $now)->modify('+1 day');

        $taches = $this->tacheRepository->findDeadlineBefore($tomorrow);

        foreach ($taches as $tache) {
            // Évite les doublons de notifications
            foreach ($tache->getPartagerAvec() as $user) {
                $existing = $this->em->getRepository(Notifiation::class)
                    ->findOneBy(['user' => $user, 'message' => "La tâche \"{$tache->getTitre()}\" est proche de sa deadline !"]);
                if (!$existing) {
                    $notif = new Notifiation();
                    $notif->setUser($user);
                    $notif->setMessage("La tâche \"{$tache->getTitre()}\" est proche de sa deadline !");
                    $notif->setCreatedAt(new \DateTime());
                    $notif->setIsRead(false);

                    $this->em->persist($notif);
                }
            }
        }

        $this->em->flush();
        $output->writeln('Notifications envoyées avec succès.');

        return Command::SUCCESS;
    }
}
