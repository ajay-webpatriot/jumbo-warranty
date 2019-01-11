<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ServiceCenter
 *
 * @package App
 * @property string $name
 * @property string $address_1
 * @property string $location
 * @property integer $commission
 * @property string $address_2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property enum $status
*/
class ServiceCenter extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address_1', 'commission', 'address_2', 'city', 'state', 'zipcode', 'status', 'location_latitude', 'location_longitude'];
    protected $hidden = [];
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setCommissionAttribute($input)
    {
        $this->attributes['commission'] = $input ? $input : null;
    }
    
}
