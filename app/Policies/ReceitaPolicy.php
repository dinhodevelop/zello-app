<?php

namespace App\Policies;

use App\Models\Receita;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReceitaPolicy
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
    public function view(User $user, Receita $receita): bool
    {
        // Permite visualizar se for o criador, responsável ou do mesmo household
        return $user->id === $receita->user_id || 
               $user->id === $receita->created_by || 
               $user->id === $receita->responsible_user_id ||
               ($user->household_id && $user->household_id === $receita->household_id);
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
    public function update(User $user, Receita $receita): bool
    {
        // Permite editar se for o criador ou responsável
        return $user->id === $receita->user_id || 
               $user->id === $receita->created_by || 
               $user->id === $receita->responsible_user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Receita $receita): bool
    {
        // Permite deletar apenas se for o criador
        return $user->id === $receita->created_by || $user->id === $receita->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Receita $receita): bool
    {
        return $user->id === $receita->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Receita $receita): bool
    {
        return $user->id === $receita->user_id;
    }
}
