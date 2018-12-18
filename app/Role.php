<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

// permission work
// use Spatie\Permission\Traits\HasRoles;
// use Spatie\Permission\Models\Role as RolePermission;
// use Spatie\Permission\Models\Permission as perm;

use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Guard;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

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
    
    public static function findByRoleName(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('title', $name)->where('guard_name', $guardName)->first();

        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }
}
