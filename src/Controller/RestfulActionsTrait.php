<?php

namespace MNC\Bundle\RestBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use League\Fractal\TransformerAbstract;
use MNC\Bundle\RestBundle\ApiProblem\ApiError;
use MNC\Bundle\RestBundle\ApiProblem\ApiProblem;
use MNC\Bundle\RestBundle\ApiProblem\ApiProblemException;
use MNC\Bundle\RestBundle\Fractalizer\Fractalizer;
use MNC\Bundle\RestBundle\Helper\RestInfo;
use MNC\Bundle\RestBundle\Helper\RestInfoInterface;
use MNC\Bundle\RestBundle\Helper\RouteActionVerb;
use MNC\Bundle\RestBundle\Security\OwnableInterface;
use MNC\Bundle\RestBundle\Security\ProtectedResourceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This trait contains some default Rest Actions ready for using in your controllers.
 * @package MNC\Bundle\RestBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
trait RestfulActionsTrait
{
    /**
     * @Route("", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        if ($this->manager !== null) {
            $data = $this->getResourceManager()->indexResource($request);
        } else {
            /** @var EntityRepository $repo */
            $repo = $this->getDoctrine()->getRepository($this->entity);
            $data = $repo->createQueryBuilder($this->name);
        }
        if ($data instanceof Response) {
            return $data;
        }
        return $this->createResourceResponse($data, 200);
    }

    /**
     * @Route("/new", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function newAction(Request $request)
    {
        $entity = new $this->entity;

        $form = $this->createForm($this->form, $entity, [
            'validation_groups' => ['Default', 'New'],
            'csrf_protection' => false
        ]);

        $normalizedForm = $this->get('liform')->transform($form);

        return new JsonResponse($normalizedForm, 200);
    }

    /**
     * @Route("/{id}/edit", methods={"GET"})
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->getResourceByIdentifierQuery($id)
            ->getQuery()->getOneOrNullResult();

        if ($entity === null) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        $form = $this->createForm($this->form, $entity, [
            'validation_groups' => ['Default', 'Update'],
            'csrf_protection' => false
        ]);

        $normalizedForm = $this->get('liform')->transform($form);

        return new JsonResponse($normalizedForm, 200);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @param Request $request
     * @param         $id
     * @return Response
     * @throws \Exception
     */
    public function showAction(Request $request, $id)
    {
        $query = $this->getResourceByIdentifierQuery($id);
        $data = $query->getQuery()->getResult();

        if ($data === null OR sizeof($data) === 0) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        if (sizeof($data) <= 1) {
            $data = array_shift($data);
            $this->denyAccessUnlessGranted(ProtectedResourceVoter::VIEW, $data);
        } else {
            foreach ($data as $item) {
                $this->denyAccessUnlessGranted(ProtectedResourceVoter::VIEW, $item);
            }
        }

        return $this->createResourceResponse($data, 200);
    }

    /**
     * @Route("", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function storeAction(Request $request)
    {
        $user = $this->getUser();

        $entity = new $this->entity;

        $form = $this->createForm($this->form, $entity , [
            'validation_groups' => ['Default', 'New'],
            'csrf_protection' => false
        ]);

        $form->submit($request->request->all());

        if ($form->isValid() && $form->isSubmitted()) {

            if ($entity instanceof OwnableInterface AND $user !== null AND $entity->getOwner() === null) {
                $entity->setOwner($user);
            }

            $em = $this->getManager();
            $em->persist($entity);
            $em->flush();

            $url = $this->buildLocationHeaderUrl($entity);

            return $this->createResourceResponse($entity, 201, [
                'Location' => $url
            ]);
        }
        throw $this->createValidationErrorException($form);
    }

    /**
     * @Route("/{id}", methods={"PATCH", "PUT", "POST"})
     * @param Request $request
     * @param         $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function updateAction(Request $request, $id)
    {
        $token = $this->get('security.token_storage')->getToken();

        $entity = $this->getResourceByIdentifierQuery($id)
            ->getQuery()->getOneOrNullResult();

        if ($entity === null) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        $form = $this->createForm($this->form, $entity, [
            'validation_groups' => ['Default', 'Update'],
            'csrf_protection' => false
        ]);

        $form->submit($request->request->all(), !$request->isMethod('PATCH'));

        $this->denyAccessUnlessGranted(ProtectedResourceVoter::UPDATE, $entity);

        if ($form->isValid() && $form->isSubmitted()) {

            if ($entity instanceof OwnableInterface AND $user !== null AND $entity->getOwner() === null) {
                $entity->setOwner($user);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $url = $this->buildLocationHeaderUrl($entity);

            return $this->createResourceResponse($entity, 200, [
                'Location' => $url
            ]);
        }
        throw $this->createValidationErrorException($form);

    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @param Request $request
     * @param         $id
     * @return Response
     * @throws \Exception
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->getResourceByIdentifierQuery($id)
            ->getQuery()->getOneOrNullResult();

        if ($entity === null) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        $this->denyAccessUnlessGranted(ProtectedResourceVoter::DELETE, $entity);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->createResourceResponse(null, 204);
    }

    /**
     * @Route("/{id}/{subresource}", methods={"GET"})
     * @param Request $request
     * @param         $id
     */
    public function indexSubresourceAction(Request $request, $id)
    {

    }

    /**
     * @Route("/{id}/{subresource}", methods={"POST"})
     * @param Request $request
     */
    public function storeSubresourceAction(Request $request)
    {

    }
}