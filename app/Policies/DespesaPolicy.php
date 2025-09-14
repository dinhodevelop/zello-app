<?php

namespace App\Policies;

use App\Models\Despesa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DespesaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Despesa $despesa): bool
    {
        // Permite visualizar se for o criador, responsável ou do mesmo household
        return $user->id === $despesa->user_id || 
               $user->id === $despesa->created_by || 
               $user->id === $despesa->responsible_user_id ||
               ($user->household_id && $user->household_id === $despesa->household_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Despesa $despesa): bool
    {
        // Permite editar se for o criador ou responsável
        return $user->id === $despesa->user_id || 
               $user->id === $despesa->created_by || 
               $user->id === $despesa->responsible_user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Despesa $despesa): bool
    {
        // Permite deletar apenas se for o criador
        return $user->id === $despesa->created_by || $user->id === $despesa->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Despesa $despesa): bool
    {
        return $user->id === $despesa->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Despesa $despesa): bool
    {
        return $user->id === $despesa->user_id;
    }
}
