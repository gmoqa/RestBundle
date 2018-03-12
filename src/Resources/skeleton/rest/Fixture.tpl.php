<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MNC\Bundle\RestBundle\Doctrine\Fixtures\AdvancedFixture;
use Doctrine\Common\Persistence\ObjectManager;
use <?= $entity_full_class_name; ?>;

class <?= $class_name; ?> extends AdvancedFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $collection = $this->make(<?= $entity_class_name; ?>::class, 40);
        $this->persistCollection($collection, $manager);
    }
}