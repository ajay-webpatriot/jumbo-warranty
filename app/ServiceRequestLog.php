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
       
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
