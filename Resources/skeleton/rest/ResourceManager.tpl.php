<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $entity_full_class_name;?>;
use <?= $form_full_class_name;?>;
use <?= $transformer_full_class_name;?>;
use MNC\Bundle\RestBundle\Manager\AbstractResourceManager;

class <?= $class_name; ?> extends AbstractResourceManager
{
    /** @inheritdoc */
    public function getEntityClass()
    {
        return <?= $entity_class_name?>::class;
    }

    /** @inheritdoc */
    public function getTransformerClass()
    {
        return <?= $transformer_class_name?>::class;
    }

    /** @inheritdoc */
    public function getFormClass()
    {
        return <?= $form_class_name?>::class;
    }
}