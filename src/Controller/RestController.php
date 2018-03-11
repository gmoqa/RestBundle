<?php

namespace MNC\Bundle\RestBundle\Controller;

use League\Fractal\TransformerAbstract;
use MNC\Bundle\RestBundle\ApiProblem\ApiError;
use MNC\Bundle\RestBundle\ApiProblem\ApiProblem;
use MNC\Bundle\RestBundle\ApiProblem\ApiProblemException;
use MNC\Bundle\RestBundle\Fractalizer\Fractalizer;
use MNC\Bundle\RestBundle\Helper\RouteActionVerb;
use MNC\Bundle\RestBundle\Security\ProtectedResourceInterface;
use MNC\Bundle\RestBundle\Security\ProtectedResourceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * This controller serves as a base controller for Rapid Api development. To
 * learn how to use it, see the docs for more info.
 *
 * @package ApiBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 * @docs https://github.com/mnavarrocarter/rest-bundle/blob/master/src/Resources/docs/1.rest-controller.md
 */
abstract class RestController extends Controller
{
    /**
     * Build an absolute route to include in the location header.
     * @param      $entity
     * @param null $route
     * @param      $identifier
     * @return string
     */
    protected function createResourceUrl($entity, $route, $identifier)
    {
        $pa = PropertyAccess::createPropertyAccessor();
        $router = $this->get('router');
        return $router->generate($route, ['id' => $pa->getValue($entity, $identifier)], 0);
    }

    /**
     * @param mixed  $attributes
     * @param null   $subject
     * @param string $message
     */
    protected function denyAccessUnlessGranted($attributes, $subject = null, string $message = 'Access Denied.')
    {
        if (!$this->isGranted($attributes, $subject)) {
            $request = $this->get('request_stack')->getCurrentRequest();
            $message = sprintf('You do not have permissions to %s the requested resource', RouteActionVerb::findVerb($request->attributes->get('_route')));
            throw $this->createAccessDeniedException($message);
        }
    }

    /**
     * @param FormInterface $form
     * @return ApiProblemException
     */
    protected function createValidationErrorException(FormInterface $form)
    {
        $normalizedForm = $this->get('serializer')->normalize($form);
        $apiProblem = ApiProblem::create(400, $normalizedForm['errors'], ApiError::TYPE_VALIDATION_ERROR);
        return $apiProblem->toException();
    }

    /**
     * Builds a response based on the data provided. Tries to guess if should be
     * paginated or not.
     * @param       $transformerClass
     * @param       $data
     * @param int   $statusCode
     * @param array $headers
     * @return JsonResponse
     * @throws \Exception
     * @throws \HttpResponseException
     */
    protected function createResourceResponse($transformerClass, $data = null, $statusCode = 200, $headers = [])
    {
        if ($data === null && $statusCode !== 204) {
            throw new \HttpResponseException("You cannot have null data without a 204 status code");
        }

        if ($data === null) {
            return new JsonResponse(null, 204);
        }
        /** @var TransformerAbstract $transformer */
        $transformer = $this->get($transformerClass);
        $array = $this->fractalize($data, $transformer);

        return JsonResponse::create($array, $statusCode, $headers);
    }

    /**
     * @param                     $data
     * @param TransformerAbstract $transformer
     * @return array
     * @throws \Exception
     */
    protected function fractalize($data, TransformerAbstract $transformer)
    {
        return $this->get(Fractalizer::class)->fractalize($data, $transformer);
    }

    /**
     * @param $resultSet
     * @return array
     */
    protected function applyDynamicPermissionCheck($resultSet)
    {
        $user = $this->getUser();

        if (is_array($resultSet)) {
            $resultSet = array_filter($resultSet, function ($element) use ($user) {
                if ($element instanceof ProtectedResourceInterface) {
                    return $element->isVisibleBy($user);
                }
                return true;
            });
        } else {
            if ($resultSet instanceof ProtectedResourceInterface) {
                $this->denyAccessUnlessGranted(ProtectedResourceVoter::VIEW, $resultSet);
            }
        }

        return $resultSet;
    }
}