<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;
use Hash;

//permission plugin
// use Spatie\Permission\Traits\HasRoles;
/**
 * Class User
 *
 * @package App
 * @property string $role
 * @property string $company
 * @property string $service_center
 * @property string $name
 * @property string $phone
 * @property string $address_1
 * @property string $address_2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $location
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property enum $status
*/
class User extends Authenticatable
{
    // permission plugin
    // use HasRoles;
//    use SoftDeletes;

    use Notifiable;
    protected $fillable = ['name', 'phone', 'address_1', 'address_2', 'city', 'state', 'zipcode', 'email', 'password', 'remember_token', 'status', 'location_latitude', 'location_longitude', 'role_id', 'company_id', 'service_center_id'];
    protected $hidden = ['password', 'remember_token'];
    
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];
    

    /**
     * Set to null if empty
     * @param $input
     */
    public function setRoleIdAttribute($input)
    {
        $this->attributes['role_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCompanyIdAttribute($input)
    {
        $this->attributes['company_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setServiceCenterIdAttribute($input)
    {
        $this->attributes['service_center_id'] = $input ? $input : null;
    }/**
     * Hash password
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input)
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
    }
    public function getNameAttribute($value) {
        return ucfirst($value);
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withTrashed();
    }
    
    public function service_center()
    {
        return $this->belongsTo(ServiceCenter::class, 'service_center_id')->withTrashed();
    }
    
    public function service_requests() {
        return $this->hasMany(ServiceRequest::class, 'customer_id');
    }
    
    

    public function sendPasswordResetNotification($token)
    {
       $this->notify(new ResetPassword($token));
    }
}
