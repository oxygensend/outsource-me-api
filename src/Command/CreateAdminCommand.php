<?php

namespace App\Command;

use App\Entity\Admin;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create admin for outsource me appliaction',

)]
class CreateAdminCommand extends Command
{

    public function __construct(
        private EntityManagerInterface      $em,
        private ValidatorInterface          $validator,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Create admin for outsource appliaction")
            ->setHelp("This command help you to create admin user");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln([
            "Admin Creator",
            "-------------",
            ''
        ]);


        $emailQuestion = new Question("Please provide your address email");
        $emailQuestion->setValidator(fn($answer) => count($this->validator->validate($answer, new Email())) ?
            throw new \Exception("Provided email is not proper format") : $answer
        );
        $emailQuestion->setValidator(fn($answer) => count($this->validator->validate($answer, new NotBlank())) ?
            throw new \Exception("Email field cannot be blank") : $answer
        );
        $email = $io->askQuestion($emailQuestion);

        $passwordQuestion = new Question("Please provide your password");

        $passwordQuestion->setValidator(fn($answer) => count($this->validator->validate($answer, new NotBlank())) ?
            throw new \Exception("Password field cannot be blank") : $answer
        );

        $passwordQuestion->setHidden(true);
        $password = $io->askQuestion($passwordQuestion);

        $admin = new User();
        $admin->setName('test');
        $admin->setEmail($email);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
        $admin->setRoles(['ROLE_ADMIN']);

        $this->em->persist($admin);
        $this->em->flush();

        $io->success('You have new admin user');

        return Command::SUCCESS;
    }
}
