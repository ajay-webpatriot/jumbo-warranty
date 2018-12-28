<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ManageCharge
 *
 * @package App
 * @property string $km_charge
 * @property enum $status
*/
class ManageCharge extends Model
{
    use SoftDeletes;

    protected $fillable = ['km_charge', 'status'];
    protected $hidden = [];
    
    

    public static $enum_status = ["Active" => "Active", "Inactive" => "Inactive"];
    
}
