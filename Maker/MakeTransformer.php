<?php

namespace MNC\Bundle\RestBundle\Maker;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
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
 * Class MakeTransformer
 * @author Matías Navarro Carter <mnavarro@option.cl>s
 */
final class MakeTransformer extends AbstractMaker
{
    /**
     * @var DoctrineEntityHelper
     */
    private $entityHelper;

    /**
     * MakeTransformer constructor.
     * @param DoctrineEntityHelper $entityHelper
     */
    public function __construct(DoctrineEntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    public static function getCommandName(): string
    {
        return 'make:transformer';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates a transformer for an entity.')
            ->addArgument('bound-class', InputArgument::REQUIRED, 'The name of Entity or custom model class that the new transformer will be bound to')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeTransformer.txt'))
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

        if (empty($shortClass)) {
            $io->error('You must provide an entity name.');
            return;
        }

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

        $classMetadata = $this->entityHelper->getEntityMetadata($entityClassDetails->getFullName());

        $relationCollection = [];
        $relationSingle = [];

        foreach ($classMetadata->getAssociationNames() as $assoc) {
            if ($classMetadata->isCollectionValuedAssociation($assoc)) {
                $relationCollection[] = $assoc;
            } else {
                $relationSingle[] = $assoc;
            }
        }

        $resourceName = Str::asLowerCamelCase($entityClassDetails->getShortName());

        // Generating Transformer
        $generator->generateClass(
            $transformerClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Transformer.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'resource_name' => $resourceName,
                'available_includes' => $this->buildAvailableIncludes($classMetadata->getAssociationNames()),
                'collections' => $this->buildIncludeCollections($classMetadata),
                'singles' => $this->buildIncludeSingles($classMetadata),
                'props' => $this->buildTransformProps($classMetadata)
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {

    }

    /**
     * @param array $assocs
     * @return string
     */
    private function buildAvailableIncludes(array $assocs)
    {
        return implode(', ',array_map(function ($assoc) {
            return sprintf('\'%s\'', $assoc);
        }, $assocs));
    }

    /**
     * @param ClassMetadata $classMetadata
     * @return array
     */
    public function buildTransformProps(ClassMetadata $classMetadata)
    {
        $resourceName = Str::asLowerCamelCase(Str::getShortClassName($classMetadata->getName()));
        $array = [];
        foreach ($classMetadata->getFieldNames() as $prop) {
            $array[] = sprintf('$array[\'%s\'] = $%s->get%s();%s', $prop, $resourceName, ucfirst($prop), PHP_EOL);
        }
        return $array;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @return array
     */
    private function buildIncludeCollections(ClassMetadata $classMetadata)
    {
        $functions = [];
        foreach ($classMetadata->getAssociationNames() as $assoc) {
            if (!$classMetadata->isCollectionValuedAssociation($assoc)) {
                continue;
            }

            $methodName = 'include'.ucfirst($assoc);
            $thisClass = Str::getShortClassName($classMetadata->getName());
            $targetClass = Str::getShortClassName($classMetadata->getAssociationTargetClass($assoc));
            $thisVariable = Str::asLowerCamelCase($thisClass);
            $statement = sprintf('// return $this->collection($%s->get%s(), %sTransformer::class);', $thisVariable, ucfirst($assoc), $targetClass);
            $functions[] = <<<EOT

    public function {$methodName}({$thisClass} \${$thisVariable})
    {
        // Please verify that the given transformer is created and uncomment.
        {$statement}
    }
EOT;
        }
        return $functions;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @return array
     */
    public function buildIncludeSingles(ClassMetadata $classMetadata)
    {
        $functions = [];
        foreach ($classMetadata->getAssociationNames() as $assoc) {
            if (!$classMetadata->isSingleValuedAssociation($assoc)) {
                continue;
            }

            $methodName = 'include'.ucfirst($assoc);
            $thisClass = Str::getShortClassName($classMetadata->getName());
            $targetClass = Str::getShortClassName($classMetadata->getAssociationTargetClass($assoc));
            $thisVariable = Str::asLowerCamelCase($thisClass);
            $statement = sprintf('// return $this->item($%s->get%s(), %sTransformer::class);', $thisVariable, ucfirst($assoc), $targetClass);
            $functions[] = <<<EOT

    public function {$methodName}({$thisClass} \${$thisVariable})
    {
        // Please verify that the given transformer is created and uncomment.
        {$statement}
    }
EOT;
        }
        return $functions;
    }
}