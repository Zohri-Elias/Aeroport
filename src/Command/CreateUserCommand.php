<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Crée un nouvel utilisateur'
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe')
            ->addArgument('role', InputArgument::OPTIONAL, 'Rôle (par défaut ROLE_USER)', 'ROLE_USER');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new Utilisateur();
        $user->setEmail($input->getArgument('email'));
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $input->getArgument('password')
            )
        );
        $user->setRoles([$input->getArgument('role')]);
        $user->setNom('Admin');
        $user->setPrenom('System');
        $user->setVille('Paris');
        $user->setDateNaissance(new \DateTime('1980-01-01'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln(sprintf('Utilisateur %s créé avec le rôle %s', $user->getEmail(), $input->getArgument('role')));

        return Command::SUCCESS;
    }
}