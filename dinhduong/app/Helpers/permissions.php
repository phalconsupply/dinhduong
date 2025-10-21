<?php

use Illuminate\Support\Facades\Auth;

function is_admin(){
    return Auth::user()->hasRole('admin');
}
function is_manager(){
    return Auth::user()->hasRole('manager');
}
function is_employee(){
    return Auth::user()->hasRole('employee');
}
function is_roles($roles){
    return Auth::user()->hasAnyRole($roles);
}
function is_super_admin_province(){
    return Auth::user()->hasRole('manager') && Auth::user()->unit->unit_type->role == 'super_admin_province';
}
function admin_province(){
    return Auth::user()->hasRole('manager') && Auth::user()->unit->unit_type->role == 'admin_province';
}




function is_edit_user($user):bool{

    if(is_admin()){
        return true;
    }
    if(is_manager()){
        if($user->created_by == Auth::user()->id){
            return true;
        }
    }
    if($user->role === 'employee' && (is_admin() || is_manager())){
        return true;
    }
    return false;
}
function is_destroy_user($user):bool{

    if(is_admin()){
        return true;
    }
    if(is_manager()){
        if($user->created_by == Auth::user()->id){
            return true;
        }
    }
    if($user->role === 'employee' && (is_admin() || is_manager())){
        return true;
    }
    return false;
}

?>
