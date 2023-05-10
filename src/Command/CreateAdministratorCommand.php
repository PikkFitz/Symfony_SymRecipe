<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-administrator',
    description: 'Create an administrator',
)]
class CreateAdministratorCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('app:create-administrator');

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fullname', InputArgument::OPTIONAL, 'Full Name')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');

        $fullName = $input->getArgument('fullname');
        if (!$fullName) 
        {
            $question = new Question('Quel est le nom du nouvel administrateur ? '); 
            // Sert à poser une question si aucun l'argument n'est pas rentré lorsque l'on rentre la commande : php bin/console app:create-administrator
            // (ald : php bin/console app:create-administrator "NOM" "EMAIL" "MOT DE PASSE")

            $fullName = $helper->ask($input, $output, $question);
            // Permet de répondre à la question posée précedement
        }

        $email = $input->getArgument('email');
        if (!$email) 
        {
            $question = new Question('Quel est l\'email de '. $fullName .' ? '); 
            // Sert à poser une question si aucun l'argument n'est pas rentré lorsque l'on rentre la commande : php bin/console app:create-administrator
            // (ald : php bin/console app:create-administrator "NOM" "EMAIL" "MOT DE PASSE")

            $email = $helper->ask($input, $output, $question);
            // Permet de répondre à la question posée précedement
        }

        $plainPassword = $input->getArgument('password');
        if (!$plainPassword) 
        {
            $question = new Question('Quel est le mot de passe de '. $fullName .' ? '); 
            // Sert à poser une question si aucun l'argument n'est pas rentré lorsque l'on rentre la commande : php bin/console app:create-administrator
            // (ald : php bin/console app:create-administrator "NOM" "EMAIL" "MOT DE PASSE")

            $plainPassword = $helper->ask($input, $output, $question);
            // Permet de répondre à la question posée précedement
        }

        $user = (new User())->setFullName($fullName)
            ->setEmail($email)
            ->setPlainPassword($plainPassword)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Un nouvel administrateur "'. $fullName .'" a été créé !');

        return Command::SUCCESS;
    }
}
