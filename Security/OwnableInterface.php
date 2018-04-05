<?php

namespace MNC\Bundle\RestBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface OwnableInterface
{
    public function getOwner();

    public function setOwner(UserInterface $user);
}