<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $entity_full_class_name;?>;
use <?= $form_full_class_name;?>;
use <?= $transformer_full_class_name;?>;
use Doctrine\ORM\QueryBuilder;
use MNC\Bundle\RestBundle\Manager\AbstractResourceManager;
use Symfony\Component\HttpFoundation\Request;

class <?= $class_name; ?> extends AbstractResourceManager
{
    protected $entityClass = <?= $entity_class_name?>::class;
    protected $formClass = <?= $form_class_name?>::class;
    protected $transformerClass = <?= $transformer_class_name?>::class;
    protected $identifier = 'id';

    /**
     * @inheritdoc
     */
    public function indexResource(Request $request, $filters = []) : QueryBuilder
    {
        /** @var <?= $repository_class_name; ?> $repo */
        return $this->getRepository()->createQueryBuilder('<?= $entity_alias; ?>');
    }
}