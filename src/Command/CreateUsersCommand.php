<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\UserRepository;

#[AsCommand(
    name: 'CreateUsersCommand',
    description: 'Add a short description for your command',
)]
class CreateUsersCommand extends Command
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this->setName('app:create-users')
            ->setDescription('Create users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->userRepository->createFakeUsers();

        $output->writeln('Fake users created successfully.');

        return Command::SUCCESS;
    }
}
