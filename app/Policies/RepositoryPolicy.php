<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Repository;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepositoryPolicy
{
    use HandlesAuthorization;

    public function author(User $user, Repository $repository)
    {
        if($user->id == $repository->user_id){
            return true;
        }else{
            return false;
        }
    }
}
