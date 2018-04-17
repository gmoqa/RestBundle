<?= "<?php\n"; ?>

namespace <?= $namespace?>;

use <?= $entity_full_class_name;?>;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class <?= $class_name?> extends SQLFilter
{
    /**
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->getReflectionClass()->getName() !== <?= $entity_class_name?>::class) {
        return '';
    }

    //return sprintf('%s.field LIKE %s OR %s.field LIKE %s',
    //    $targetTableAlias,
    //    $this->getParameter('paramValue'),
    //    $targetTableAlias,
    //    $this->getParameter('paramValue')
    //);
    }
}