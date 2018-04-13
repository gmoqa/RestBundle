<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use League\Fractal\TransformerAbstract;
use <?= $entity_full_class_name ?>;

class <?= $class_name ?> extends TransformerAbstract
{
    protected $availableIncludes = [<?= $available_includes ;?>];

    protected $defaultIncludes = [];

    protected $validParams = [];

    public function transform(<?= $entity_class_name ?> $<?= $resource_name ?>)
    {
    <?php foreach ($props as $prop) :?>
    <?= $prop?>
    <?php endforeach;?>
    return $array;
    }

    <?php foreach ($singles as $single):?>
    <?= $single ?>
    <?php endforeach;?>

    <?php foreach ($collections as $collection):?>
        <?= $collection ?>
    <?php endforeach;?>

}