<?php

namespace MNC\Bundle\RestBundle\Maker;

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
final class MakeManager extends AbstractMaker
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
        return 'make:manager';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates a resource manager for an entity.')
            ->addArgument('bound-class', InputArgument::REQUIRED, 'The name of Entity or custom model class that the new resource manager will be bound to.')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeManager.txt'))
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

        if (!class_exists($entityClassDetails->getFullName())) {
            $io->error(sprintf('Class %s does not exist!', $entityClassDetails->getFullName()));
            return;
        }

        $transformerClassDetails = $generator->createClassNameDetails(
            $shortClass,
            'Transformer\\',
            'Transformer'
        );

        $formClassDetails = $generator->createClassNameDetails(
            $shortClass,
            'Form\\',
            'Form'
        );

        $managerClassDetails = $generator->createClassNameDetails(
            $shortClass,
            'ResourceManager\\',
            'Manager'
        );

        // Generating Manager
        $generator->generateClass(
            $managerClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/ResourceManager.tpl.php',
            [
                'transformer_full_class_name' => $transformerClassDetails->getFullName(),
                'transformer_class_name' => $transformerClassDetails->getShortName(),
                'form_full_class_name' => $formClassDetails->getFullName(),
                'form_class_name' => $formClassDetails->getShortName(),
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {

    }
}