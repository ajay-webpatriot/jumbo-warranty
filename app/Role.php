<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

// permission work
// use Spatie\Permission\Traits\HasRoles;
// use Spatie\Permission\Models\Role as RolePermission;
// use Spatie\Permission\Models\Permission as perm;

/**
 * Class Role
 *
 * @package App
 * @property string $title
 * @property enum $status
*/
class Role extends \Spatie\Permission\Models\Role
{
	// use HasRoles;
   	// protected $guard_name = 'web';
    protected $fillable = ['title', 'status'];
    protected $hidden = [];
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];
    
}
