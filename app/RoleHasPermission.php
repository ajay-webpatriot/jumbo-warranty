<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package App
 * @property string $title
 * @property enum $status
*/
class RoleHasPermission extends Model
{
    
    public function createRolePermission($data)
	{
		
	        $this->permission_id = $data['permission_id'];
	        $this->role_id = $data['role_id'];

	        $this->save();
	        return 1;
	}
}
