<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Não pode excluir a si mesmo
        if ($user->id === $model->id) {
            return false;
        }

        // Apenas admins podem excluir usuários
        if (!$user->isAdmin()) {
            return false;
        }

        // Se está excluindo um admin, verifica se não é o último
        if ($model->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            return $adminCount > 1;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can update roles.
     */
    public function updateRole(User $user, User $model): bool
    {
        // Não pode alterar a própria role
        if ($user->id === $model->id) {
            return false;
        }

        // Apenas admins podem alterar roles
        if (!$user->isAdmin()) {
            return false;
        }

        // Se está removendo privilégios de admin, verifica se não é o último admin
        if ($model->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            return $adminCount > 1;
        }

        return true;
    }

    /**
     * Determine whether the user can manage household membership.
     */
    public function manageHouseholdMembership(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}