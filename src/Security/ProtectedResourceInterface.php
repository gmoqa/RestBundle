<?php

namespace MNC\Bundle\RestBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This interface can be applied to a resource that has restricted access in the
 * API.
 * @package MNC\Bundle\RestBundle\Security
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface ProtectedResourceInterface
{
    public function isVisibleBy(UserInterface $user = null);

    public function isEditableBy(UserInterface $user = null);

    public function isDeletableBy(UserInterface $user = null);
}