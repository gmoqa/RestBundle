<?php

namespace MNC\Bundle\RestBundle\Maker;

use Doctrine\Common\Util\Inflector;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineEntityHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class MakeRestController
 * @author Matías Navarro Carter <mnavarro@option.cl>s
 */
final class MakeRestController extends AbstractMaker
{
    /**
     * @var DoctrineEntityHelper
     */
    private $entityHelper;

    /**
     * MakeRestController constructor.
     * @param DoctrineEntityHelper $entityHelper
     */
    public function __construct(DoctrineEntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    /**
     * @return string
     */
    public static function getCommandName(): string
    {
        return 'make:rest-controller';
    }

    /**
     * @param Command            $command
     * @param InputConfiguration $inputConfig
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates a rest controller for an entity.')
            ->addArgument('bound-class', InputArgument::REQUIRED, 'The name of Entity or custom model class that the new rest controller will be bound to')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeRestController.txt'))
        ;

        $inputConfig->setArgumentAsNonInteractive('bound-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('bound-class')) {
            $argument = $command->getDefinition()->getArgument('bound-class');

            $entities = $this->entityHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setValidator(function ($answer) use ($entities) {return Validator::existsOrNull($answer, $entities); });
            $question->setAutocompleterValues($entities);
            $question->setMaxAttempts(3);

            $input->setArgument('bound-class', $io->askQuestion($question));
        }
    }

    /**
     * @param InputInterface $input
     * @param ConsoleStyle   $io
     * @param Generator      $generator
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityName = $input->getArgument('bound-class');

        $entityClassDetails = $generator->createClassNameDetails(
            $entityName,
            'Entity\\'
        );

        $controllerClassDetails = $generator->createClassNameDetails(
            $entityName,
            'Controller\\',
            'Controller'
        );

        $managerClassDetails = $generator->createClassNameDetails(
            $entityName,
            'ResourceManager\\',
            'Manager'
        );


        $generator->generateClass(
            $controllerClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Controller.tpl.php',
            [
                'resource_name' => Str::asLowerCamelCase($entityClassDetails->getShortName()),
                'resource_plural' => Inflector::pluralize(Str::asLowerCamelCase($entityClassDetails->getShortName())),
                'manager_full_class_name' => $managerClassDetails->getFullName(),
                'manager_class_name' => $managerClassDetails->getShortName(),
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }
}