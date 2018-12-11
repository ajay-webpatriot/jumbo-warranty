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
class AssignPart extends Model
{
    use SoftDeletes;

    protected $fillable = ['quantity', 'status', 'company_id', 'product_parts_id'];
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
