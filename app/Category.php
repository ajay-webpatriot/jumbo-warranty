<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 *
 * @package App
 * @property string $name
 * @property decimal $service_charge
 * @property enum $status
*/
class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'service_charge', 'status'];
    protected $hidden = [];
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setServiceChargeAttribute($input)
    {
        $this->attributes['service_charge'] = $input ? $input : null;
    }
    
    public function products() {
        return $this->hasMany(Product::class, 'category_id');
    }
}
