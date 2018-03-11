<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $repository_full_class_name;?>;
use <?= $transformer_full_class_name;?>;
use <?= $form_full_class_name;?>;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use MNC\Bundle\RestBundle\Manager\AbstractResourceManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class <?= $class_name; ?> extends AbstractResourceManager
{
    /**
     * <?= $class_name;?> constructor.
     * @param <?= $repository_class_name; ?> $repository
     * @param ManagerRegistry      $registry
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(<?= $repository_class_name; ?> $repository, ManagerRegistry $registry, FormFactoryInterface $formFactory)
    {
        $this->formClass = <?= $form_class_name; ?>::class;
        $this->transformerClass = <?= $transformer_class_name; ?>::class;
        $this->identifier = 'id';
        parent::__construct($repository, $registry, $formFactory);
    }

    /**
     * @inheritdoc
     */
    public function indexResource(Request $request, $filters = []) : QueryBuilder
    {
        /** @var <?= $repository_class_name; ?> $repo */
        return $this->getRepository()->createQueryBuilder('<?= $entity_alias; ?>');
    }
}