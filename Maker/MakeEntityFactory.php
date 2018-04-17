<?php

namespace MNC\Bundle\RestBundle\Maker;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineEntityHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class MakeTransformer
 * @author Matías Navarro Carter <mnavarro@option.cl>s
 */
final class MakeEntityFactory extends AbstractMaker
{
    /**
     * @var DoctrineEntityHelper
     */
    private $entityHelper;

    /**
     * MakeManager constructor.
     * @param DoctrineEntityHelper $entityHelper
     */
    public function __construct(DoctrineEntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    public static function getCommandName(): string
    {
        return 'make:entity-factory';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates an entity factory for an entity.')
            ->addArgument('bound-class', InputArgument::REQUIRED, 'The name of Entity or custom model class that the new entity factory will be bound to.')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeEntityFactory.txt'))
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
     * @throws \Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $shortClass = $input->getArgument('bound-class');

        $entityClassDetails = $generator->createClassNameDetails(
            $shortClass,
            'Entity\\'
        );

        $entityMetadata = $this->entityHelper->getEntityMetadata($entityClassDetails->getFullName());

        if (!class_exists($entityClassDetails->getFullName())) {
            $io->error(sprintf('Class %s does not exist!', $entityClassDetails->getFullName()));
            return;
        }

        $factoryClassDetails = $generator->createClassNameDetails(
            $shortClass,
            'EntityFactory\\',
            'Factory'
        );

        // Generating Manager
        $generator->generateClass(
            $factoryClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Factory.tpl.php',
            [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'lines' => $this->buildFactoryLines($entityMetadata)
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    /**
     * @param DependencyBuilder $dependencies
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {

    }

    /**
     * @param ClassMetadata $metadata
     * @return array
     */
    private function buildFactoryLines(ClassMetadata $metadata)
    {
        $lines = [];
        foreach ($metadata->getFieldNames() as $fieldName) {
            if ($fieldName === 'id') {
                continue;
            }
            $lines[] = sprintf('$data[\'%s\'] = $faker->%s;%s', $fieldName, $this->guessFaker($metadata, $fieldName), PHP_EOL);
        }
        return $lines;
    }

    /**
     * @param ClassMetadata $metadata
     * @param               $fieldName
     * @return string
     */
    private function guessFaker(ClassMetadata $metadata, $fieldName)
    {
        $type = $metadata->getTypeOfField($fieldName);

        switch ($type) {
            case 'integer':
                return 'randomNumber';
            case 'string':
                return 'sentence';
            case 'datetime';
                return 'dateTime';
            case 'boolean';
                return 'boolean';
            case 'float';
                return 'randomFloat';
            default;
                return 'sentence';
        }
    }
}