<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $manager_full_class_name?>
use MNC\Bundle\RestBundle\Controller\RestController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use MNC\Bundle\RestBundle\Security\ProtectedResourceVoter;

/**
 * @Route("/<?= $resource_plural;?>")
 */
class <?= $class_name; ?> extends RestController
{
    /**
     * Returns a paginated collection of <?= $resource_name;?> objects.
     * @Route("", methods={"GET"})
     * @param <?= $manager_class_name;?> $manager
     * @return Response
     * @throws \Exception
     */
    public function indexAction(<?= $manager_class_name;?> $manager)
    {
        $result = $manager->findCollectionBy();
        return $this->createResourceResponse($manager->getTransformerClass(), $result, 200);
    }

    /**
     * Displays the json-schema form for creating a new <?= $resource_name;?> object.
     * @Route("/new", methods={"GET"})
     * @param <?= $manager_class_name;?> $manager
     * @return Response
     */
    public function newAction(<?= $manager_class_name;?> $manager)
    {
        if ($this->has('liform')) {
            $form = $manager->createForm();
            $serializedForm = $this->get('liform')->transform($form);
            return JsonResponse::create($serializedForm, 200);
        }
        return Response::create('', 204);
    }

    /**
     * Persists a new <?= $resource_name;?> object.
     * @Route("", methods={"POST"})
     * @param Request       $request
     * @param <?= $manager_class_name;?> $manager
     * @return Response
     * @throws \Exception
     */
    public function storeAction(Request $request, <?= $manager_class_name;?> $manager)
    {
        $user = $this->getUser();

        $form = $manager
            ->createForm()
            ->submit($request->request->all());

        $<?= $resource_name;?> = $manager->processForm($form, $user);

        if ($<?= $resource_name;?> === null) {
            throw $this->createValidationErrorException($form);
        }

        return $this->createResourceResponse($manager->getTransformerClass(), $<?= $resource_name;?>, 201, [
            'Location' => $this->createResourceUrl($<?= $resource_name;?>, 'app_<?= $resource_name;?>_show', $manager->getIdentifier())
        ]);
    }

    /**
     * Returns a single <?= $resource_name;?> object, or a paginated collection
     * on comma separated ids.
     * @Route("/{id}", methods={"GET"})
     * @param <?= $manager_class_name;?> $manager
     * @param                $id
     * @return Response
     * @throws \Exception
     */
    public function showAction(<?= $manager_class_name;?> $manager, $id)
    {
        $result = $manager->find($id);

        $result = $this->applyDynamicPermissionCheck($result);

        return $this->createResourceResponse($manager->getTransformerClass(), $result, 200);
    }

    /**
     * Displays the json-schema form for editing a <?= $resource_name;?> object.
     * @Route("/{id}/edit", methods={"GET"})
     * @param <?= $manager_class_name;?> $manager
     * @param                $id
     * @return Response
     */
    public function editAction(<?= $manager_class_name;?> $manager, $id)
    {
        $<?= $resource_name;?> = $manager->findOne($id);
        if ($this->has('liform')) {
            $form = $manager->createForm($<?= $resource_name;?>);
            $serializedForm = $this->get('liform')->transform($form);
        return JsonResponse::create($serializedForm, 200);
        }
        return Response::create('', 204);
    }

    /**
     * Updates a <?= $resource_name;?> object in the database. PUT is idempotent.
     * @Route("{id}", methods={"PATCH", "PUT"})
     * @param Request        $request
     * @param <?= $manager_class_name;?> $manager
     * @param                $id
     * @return Response
     * @throws \Exception
     */
    public function updateAction(Request $request, <?= $manager_class_name;?> $manager, $id)
    {
        $user = $this->getUser();
        $<?= $resource_name;?> = $manager->findOne($id);

        $this->denyAccessUnlessGranted(ProtectedResourceVoter::UPDATE, $<?= $resource_name;?>);

        $form = $manager->createForm($<?= $resource_name;?>)
            ->submit($request->request->all(), !$request->isMethod('PATCH'));

        $<?= $resource_name;?> = $manager->processForm($form, $user);

        if ($<?= $resource_name;?> === null) {
            throw $this->createValidationErrorException($form);
        }

        return $this->createResourceResponse($manager->getTransformerClass(), $<?= $resource_name;?>, 201, [
            'Location' => $this->createResourceUrl($<?= $resource_name;?>, 'app_<?= $resource_name;?>_show', $manager->getIdentifier())
        ]);
    }

    /**
     * Deletes a <?= $resource_name;?> from the database.
     * @Route("/{id}", methods={"DELETE"})
     * @param <?= $manager_class_name;?> $manager
     * @param                $id
     * @return Response
     */
    public function deleteAction(<?= $manager_class_name;?> $manager, $id)
    {
        $<?= $resource_name;?> = $manager->findOne($id);

        $this->denyAccessUnlessGranted(ProtectedResourceVoter::DELETE, $<?= $resource_name;?>);

        $manager->remove($<?= $resource_name;?>);
        $manager->flush();

        return Response::create(null, 204);
    }
}