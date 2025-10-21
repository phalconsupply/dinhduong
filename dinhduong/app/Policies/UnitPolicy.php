<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
class UnitPolicy
{

    protected function is_admin()
    {
        return  Auth::user()->hasRole('admin');
    }
    protected function is_manager()
    {
        return  Auth::user()->hasRole('manager');
    }
    protected function is_member($user, $unit)
    {
        return ($unit->id == $user->unit_id) ? true : false;
    }
    protected function is_creator($user, $unit)
    {
        return ($unit->created_by == $user->id) ? true : false;
    }
    protected function is_super_admin_province($user)
    {
        return ($user->unit->unit_type->role == 'super_admin_province') ? true : false;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->is_admin() || $this->is_manager();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Unit $unit): bool
    {
        return $this->is_admin() || $this->is_member($user, $unit) || $this->is_creator($user, $unit);
    }
    public function view_users(User $user, Unit $unit): bool
    {
        return  $this->is_admin() || ( $this->is_manager() && $this->is_member($user, $unit) ) || ($this->is_manager() && $this->is_creator($user, $unit) );
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->is_admin() || ($this->is_manager() && $this->is_super_admin_province($user));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Unit $unit): bool
    {
        return $this->is_admin() ||
            $this->is_creator($user, $unit) ||
            ($this->is_manager() && $this->is_super_admin_province($user) && $this->is_member($user, $unit));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Unit $unit): bool
    {
        return$this->is_admin() || $this->is_creator($user, $unit) || ($this->is_manager() && $this->is_super_admin_province($user) && $this->is_member($user, $unit));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Unit $unit): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Unit $unit): bool
    {
        //
    }
}
