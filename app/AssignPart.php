<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AssignPart
 *
 * @package App
 * @property string $company
 * @property string $product_parts
 * @property integer $quantity
 * @property enum $status
*/

use DB;// DB library

class AssignPart extends Model
{
    use SoftDeletes;

    protected $fillable = ['quantity', 'company_id', 'product_parts_id'];
    protected $hidden = [];
    

    public function getRequestedServiceParts($assignPartId)
    {
        // get total used parts in service request
        return $usedParts = DB::table('service_requests')
            ->select('service_requests.id as articles_id')
            ->join('product_part_service_request', 'service_requests.id', '=', 'product_part_service_request.service_request_id')
            ->join('assign_parts', 'assign_parts.product_parts_id', '=', 'product_part_service_request.product_part_id')
            ->where('assign_parts.product_parts_id',$assignPartId)
            ->get()->count();
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
    public function setProductPartsIdAttribute($input)
    {
        $this->attributes['product_parts_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setQuantityAttribute($input)
    {
        $this->attributes['quantity'] = $input ? $input : null;
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withTrashed();
    }
    
    public function product_parts()
    {
        return $this->belongsTo(ProductPart::class, 'product_parts_id')->withTrashed();
    }
    
}
