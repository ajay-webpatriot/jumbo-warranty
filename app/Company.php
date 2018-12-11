<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 *
 * @package App
 * @property string $name
 * @property string $credit
 * @property decimal $installation_charge
 * @property string $address_1
 * @property string $address_2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $location
 * @property enum $status
*/
class Company extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'credit', 'installation_charge', 'address_1', 'address_2', 'city', 'state', 'zipcode', 'location', 'status'];
    protected $hidden = [];
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setInstallationChargeAttribute($input)
    {
        $this->attributes['installation_charge'] = $input ? $input : null;
    }
    
}
