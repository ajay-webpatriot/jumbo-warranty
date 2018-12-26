<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Customer
 *
 * @package App
 * @property string $firstname
 * @property string $lastname
 * @property string $phone
 * @property string $company
 * @property string $address_1
 * @property string $address_2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $location
 * @property enum $status
*/
class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = ['firstname', 'lastname', 'phone', 'address_1', 'address_2', 'city', 'state', 'zipcode', 'location','location_latitude','location_longitude', 'status', 'company_id'];
    protected $hidden = [];
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCompanyIdAttribute($input)
    {
        $this->attributes['company_id'] = $input ? $input : null;
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withTrashed();
    }
    
    public function service_requests() {
        return $this->hasMany(ServiceRequest::class, 'customer_id');
    }
}
