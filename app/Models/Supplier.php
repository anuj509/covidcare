<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Supplier
 * @package App\Models
 * @version April 18, 2021, 8:42 am UTC
 *
 * @property string $category
 * @property string $name
 * @property string $location
 * @property string $phone
 */
class Supplier extends Model
{

    public $table = 'suppliers';
    



    public $fillable = [
        'category',
        'name',
        'location',
        'phone'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category' => 'string',
        'name' => 'string',
        'location' => 'string',
        'phone' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'category' => 'required|in:oxygen,beds,medicines,plasma,other',
        'name' => 'required|max:200',
        'location' => 'required',
        'phone' => 'required'
    ];

    
}
