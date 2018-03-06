<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Component\Routing\Annotation\Route;
use MNC\Bundle\RestBundle\Annotations\Resource;
use MNC\Bundle\RestBundle\Controller\RestController;
use MNC\Bundle\RestBundle\Controller\RestfulActionsTrait;

/**
 * @Route("/<?= $resource_name_plural; ?>")
 * @Resource("<?= $resource_name; ?>",
 *     relatedEntity="<?= $entity_full_class_name; ?>",
 *     formClass="<?= $form_full_class_name;?>",
 *     transformerClass="<?= $transformer_full_class_name; ?>")
 */
class <?= $class_name; ?> extends RestController
{
    use RestfulActionsTrait;

    // You can override endpoints or create your custom ones here.
}