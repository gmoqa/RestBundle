<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use MNC\Bundle\RestBundle\EntityFactory\FactoryDefinitionInterface;
use Faker\Generator;
use <?= $entity_full_class_name; ?>;

class <?= $class_name; ?> implements FactoryDefinitionInterface
{
    /**
     * @inheritdoc
     */
    public function getEntityClassname(): string
    {
        return <?= $entity_class_name; ?>::class;
    }

    /**
     * @inheritdoc
     */
    public function getData(Generator $faker) : array
    {
    <?php foreach ($lines as $line) :?>
    <?= $line ;?>
    <?php endforeach;?>
    return $data;
    }
}