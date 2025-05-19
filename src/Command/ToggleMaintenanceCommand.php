<?php

namespace App\Command;

use App\Entity\SiteSettings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'toggle-maintenance',
    description: 'Allows to toggle maintenance mode on or off',
)]
class ToggleMaintenanceCommand extends Command
{
    protected static $defaultName = 'app:toggle-maintenance';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Toggle maintenance mode on or off.')
            ->addArgument('action', InputArgument::OPTIONAL, 'The action to perform: "on", "off", or "status"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');
        $settings = $this->entityManager->getRepository(SiteSettings::class)->find(1);

        if (!$settings) {
            $output->writeln('<error>No settings found in the database.</error>');
            return Command::FAILURE;
        }

        switch (strtolower($action)) {
            case 'on':
                $settings->setMaintenanceMode(true);
                $this->entityManager->flush();
                $output->writeln('<info>Maintenance mode enabled.</info>');
                break;
            case 'off':
                $settings->setMaintenanceMode(false);
                $this->entityManager->flush();
                $output->writeln('<info>Maintenance mode disabled.</info>');
                break;
            case 'status':
                $status = $settings->isMaintenanceMode() ? 'enabled' : 'disabled';
                $output->writeln(sprintf('<info>Maintenance mode is currently %s.</info>', $status));
                break;
            default:
                $settings->setMaintenanceMode(!$settings->isMaintenanceMode());
                $this->entityManager->flush();
                $status = $settings->isMaintenanceMode() ? 'enabled' : 'disabled';
                $output->writeln(sprintf('<info>Maintenance mode %s.</info>', $status));
        }

        return Command::SUCCESS;
    }
}
