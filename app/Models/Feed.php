<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class Feed
 * @package App\Models
 * @version April 17, 2021, 9:14 pm UTC
 *
 * @property string $name
 * @property string $content
 */
class Feed extends Model
{
    use SoftDeletes;
    public $table = 'feeds';
    

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'content'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'content' => 'string',
        'user_id' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required',
        'content' => 'required'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\CovidCareUser::class, 'user_id');
    }

    
}
