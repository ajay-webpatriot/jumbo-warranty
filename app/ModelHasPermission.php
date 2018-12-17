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
class ModelHasPermission extends Model
{
    
    public function createModelPermission($data)
	{
		
	        $this->permission_id = $data['permission_id'];
	        $this->model_id = $data['model_id'];

	        $this->save();
	        return 1;
	}
}
