<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $manager_full_class_name; ?>;
use MNC\Bundle\RestBundle\Controller\RestController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use MNC\Bundle\RestBundle\Manager\ResourceManagerException;
use MNC\Bundle\RestBundle\Security\ProtectedResourceVoter;

/**
 * @Route("/<?= $resource_name_plural; ?>")
 */
class <?= $class_name; ?> extends RestController
{
    /**
     * Returns a paginated collection of <?= $resource_name ;?> objects.
     * @Route("", methods={"GET"})
     * @param Request       $request
     * @param <?= $manager_class_name; ?> $manager
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request, <?= $manager_class_name; ?> $manager)
    {
        $query = $manager->indexResource($request);
        return $this->createResourceResponse($manager->getTransformerClass(), $query, 200);
    }

    /**
     * Displays the json-schema form for creating a new <?= $resource_name ?> object.
     * @Route("/new", methods={"GET"})
     * @param <?= $manager_class_name; ?> $manager
     * @return JsonResponse
     */
    public function newAction(<?= $manager_class_name; ?> $manager)
    {
        $form = $manager->createForm();
        $serializedForm = $this->get('liform')->transform($form);

        return JsonResponse::create($serializedForm, 200);
    }

    /**
     * Persists a new <?= $resource_name; ?> object.
     * @Route("", methods={"POST"})
     * @param Request       $request
     * @param <?= $manager_class_name; ?> $manager
     * @return JsonResponse
     * @throws \Exception
     */
    public function storeAction(Request $request, <?= $manager_class_name; ?> $manager)
    {
        $user = $this->getUser();

        $form = $manager
            ->createForm()
            ->submit($request->request->all());

        $<?= $resource_name; ?> = $manager->processForm($form, $user);

        if ($<?= $resource_name;?> === null) {
            throw $this->createValidationErrorException($form);
        }

        return $this->createResourceResponse($manager->getTransformerClass(), $<?= $resource_name;?>, 201, [
            'Location' => $this->createResourceUrl($<?= $resource_name;?>, 'app_<?= $resource_name; ?>_show', $manager->getIdentifier())
        ]);
    }

    /**
     * Returns a single <?= $resource_name; ?> object, or a paginated collection
     * on comma separated ids.
     * @Route("/{id}", methods={"GET"})
     * @param Request       $request
     * @param <?= $manager_class_name; ?> $manager
     * @param               $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function showAction(Request $request, <?= $manager_class_name; ?> $manager, $id)
    {
        $result = $manager->showResource($id);

        $result = $this->applyDynamicPermissionCheck($result);

        return $this->createResourceResponse($manager->getTransformerClass(), $result, 200);
    }

    /**
     * Displays the json-schema form for editing a <?= $resource_name;?> object.
     * @Route("/{id}/edit", methods={"GET"})
     * @param Request       $request
     * @param <?= $manager_class_name; ?> $manager
     * @param               $id
     * @return JsonResponse
     * @throws ResourceManagerException
     */
    public function editAction(Request $request, <?= $manager_class_name; ?> $manager, $id)
    {
        $<?= $resource_name; ?> = $manager->showResource($id, true);
        $form = $manager->createForm($<?= $resource_name;?>);
        $serializedForm = $this->get('liform')->transform($form);

        return JsonResponse::create($serializedForm, 200);
    }

    /**
     * Updates a <?= $resource_name; ?> object in the database. PUT is idempotent.
     * @Route("{id}", methods={"PATCH", "PUT"})
     * @param Request       $request
     * @param <?= $manager_class_name; ?> $manager
     * @param               $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateAction(Request $request, <?= $manager_class_name; ?> $manager, $id)
    {
        $user = $this->getUser();
        $<?= $resource_name;?> = $manager->showResource($id, true);

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
     * @param Request       $request
     * @param <?= $manager_class_name; ?> $manager
     * @param               $id
     * @return Response
     * @throws ResourceManagerException
     */
    public function deleteAction(Request $request, <?= $manager_class_name; ?> $manager, $id)
    {
        $<?= $resource_name;?> = $manager->showResource($id, true);

        $this->denyAccessUnlessGranted(ProtectedResourceVoter::DELETE, $<?= $resource_name;?>);

        $manager->remove($<?= $resource_name;?>);
        $manager->flush();

        return Response::create(null, 204);
    }
}