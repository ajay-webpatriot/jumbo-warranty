<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ServiceRequest
 *
 * @package App
 * @property string $company
 * @property string $customer
 * @property enum $service_type
 * @property string $service_center
 * @property string $technician
 * @property enum $call_type
 * @property enum $call_location
 * @property enum $priority
 * @property string $product
 * @property string $make
 * @property string $model_no
 * @property enum $is_item_in_warrenty
 * @property string $bill_no
 * @property string $bill_date
 * @property string $serial_no
 * @property enum $mop
 * @property string $purchase_from
 * @property string $adavance_amount
 * @property string $service_charge
 * @property string $service_tag
 * @property text $complain_details
 * @property string $note
 * @property string $completion_date
 * @property decimal $additional_charges
 * @property decimal $amount
 * @property enum $status
*/
class ServiceRequestLog extends Model
{
    use SoftDeletes;

    protected $fillable = ['service_request_id', 'status_made', 'user_id'];
    protected $hidden = [];
    
    
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
    public function setCustomerIdAttribute($input)
    {
        $this->attributes['customer_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setServiceCenterIdAttribute($input)
    {
        $this->attributes['service_center_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setTechnicianIdAttribute($input)
    {
        $this->attributes['technician_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setProductIdAttribute($input)
    {
        $this->attributes['product_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setCompletionDateAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['completion_date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['completion_date'] = null;
        }
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getCompletionDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAdditionalChargesAttribute($input)
    {
        $this->attributes['additional_charges'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setAmountAttribute($input)
    {
        $this->attributes['amount'] = $input ? $input : null;
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withTrashed();
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    public function service_center()
    {
        return $this->belongsTo(ServiceCenter::class, 'service_center_id')->withTrashed();
    }
    
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
    
}
