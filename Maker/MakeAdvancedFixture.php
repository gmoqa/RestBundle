<?php

namespace MNC\Bundle\RestBundle\Maker;

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
 * Class MakeAdvancedFixture
 * @author Matías Navarro Carter <mnavarro@option.cl>s
 */
final class MakeAdvancedFixture extends AbstractMaker
{
    /**
     * @var DoctrineEntityHelper
     */
    private $entityHelper;

    public function __construct(DoctrineEntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    public static function getCommandName(): string
    {
        return 'make:advanced-fixture';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates an advanced fixture for an entity.')
            ->addArgument('bound-class', InputArgument::REQUIRED, 'The name of Entity or custom model class that the new advanced fixture will be bound to')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeAdvancedFixture.txt'))
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

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityName = $input->getArgument('resource-name');

        $entityClassDetails = $generator->createClassNameDetails(
            $entityName,
        'Entity\\'
        );

        $fixtureClassDetails = $generator->createClassNameDetails(
            $entityName,
            'DataFixtures\\',
            'Fixture'
        );

        // Generating Fixture
        $generator->generateClass(
            $fixtureClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Fixture.tpl.php',
            [
                'resource_name' => Str::asLowerCamelCase($entityClassDetails->getShortName()),
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
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