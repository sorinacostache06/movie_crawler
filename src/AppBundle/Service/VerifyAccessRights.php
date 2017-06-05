<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;

class VerifyAccessRights
{
    private $adminRole;

    public function hasAdminRights(User $user)
    {
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            if ($role == $this->adminRole) {
                return true;
            }
        }
        return false;
    }
    public function setAdminRole($role)
    {
        $this->adminRole = $role;
    }
    public function getAdminRole()
    {
        return $this->adminRole;
    }
}