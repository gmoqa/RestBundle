<?php

namespace MNC\Bundle\RestBundle\Command;

use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateResourceSetCommand extends Command
{
    protected static $defaultName = 'mncrest:create-resource-set';

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Creates a set of useful classes for rapid API development bounded to a resource.')
            ->addArgument('resource-name', InputArgument::REQUIRED, 'The resource name (e.g. <fg=yellow>post</>)')
            //->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeResource.txt'))
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $resourceName = strtolower($input->getArgument('resource-name'));
        $className = Str::asClassName($resourceName);

        $this->runCommand($output, 'make:entity', [
            'command' => 'make:entity',
            'name' => $className,
        ]);

        $this->runCommand($output, 'make:form', [
            'command' => 'make:form',
            'name' => $className.'Type',
            'bound-class' => $className,
        ]);

        $this->runCommand($output, 'make:transformer', [
            'command' => 'make:transformer',
            'bound-class' => $className,
        ]);

        $this->runCommand($output, 'make:rest-controller', [
            'command' => 'make:rest-controller',
            'bound-class' => $className,
        ]);

        $this->runCommand($output, 'make:entity-factory', [
            'command' => 'make:entity-factory',
            'bound-class' => $className,
        ]);

        $this->runCommand($output, 'make:advanced-fixture', [
            'command' => 'make:advanced-fixture',
            'bound-class' => $className,
        ]);
    }

    /**
     * @param string          $commandName
     * @param array           $arguments
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    private function runCommand( OutputInterface $output, string $commandName, array $arguments)
    {
        $command = $this->getApplication()->find($commandName);
        $input = new ArrayInput($arguments);
        return  $command->run($input, $output);
    }
}