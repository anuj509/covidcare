<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class User
 * @package App\Models
 * @version April 18, 2021, 4:01 pm UTC
 *
 * @property varchar $name
 * @property varchar $email
 * @property varchar $password
 */
class User extends Model
{

    public $table = 'users';
    



    public $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:100',
        'email' => 'required|email|unique:users',
        'password' => 'required'
    ];

    
}
