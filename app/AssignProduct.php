<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AssignProduct
 *
 * @package App
 * @property string $company
 * @property enum $status
*/
class AssignProduct extends Model
{
    use SoftDeletes;

    protected $fillable = ['company_id','product_id'];
    protected $hidden = [];
    

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCompanyIdAttribute($input)
    {
        $this->attributes['company_id'] = $input ? $input : null;
    }
    public function setProductIdAttribute($input)
    {
        $this->attributes['product_id'] = $input ? $input : null;
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withTrashed();
    }
    
    public function product()
    {
        // return $this->belongsToMany(Product::class, 'assign_product_product')->withTrashed();
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
    
}
