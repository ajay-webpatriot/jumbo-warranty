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
class ModelHasRole extends Model
{
    
    public function createModelRolePermission($data)
	{
		
	        $this->role_id = $data['role_id'];
	        $this->model_id = $data['model_id'];

	        $this->save();
	        return 1;
	}
}
