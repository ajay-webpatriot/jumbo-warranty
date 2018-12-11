<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductPart
 *
 * @package App
 * @property string $name
 * @property enum $status
*/
class ProductPart extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'status'];
    protected $hidden = [];
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];
    
}
