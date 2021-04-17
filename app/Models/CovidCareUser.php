<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

/**
 * Class CovidCareUser
 * @package App\Models
 * @version April 17, 2021, 8:34 pm UTC
 *
 * @property string $name
 * @property string $phone
 */
class CovidCareUser extends Model
{

    use Uuid;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $table = 'covid_care_users';
    



    public $fillable = [
        'name',
        'phone'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'phone' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:100',
        'phone' => 'required|min:10|max:10'
    ];

    public function posts()
    {
        return $this->hasMany(\App\Models\Post::class, 'user_id');
    }
}
