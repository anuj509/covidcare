<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class Post
 * @package App\Models
 * @version April 17, 2021, 9:05 pm UTC
 *
 * @property string $name
 * @property string $dob
 * @property string $gender
 * @property string $blood_group
 * @property string $oxygen_level
 * @property string $poc_name
 * @property string $poc_phone
 * @property string $patient_currently_admitted_at
 * @property string $ward
 * @property string $requirement
 * @property string $oxygen
 * @property string $plasma
 * @property string $medicines
 * @property string $bed
 * @property string $other
 * @property string $user_id
 */
class Post extends Model
{
    use SoftDeletes;
    public $table = 'posts';
    

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'dob',
        'gender',
        'blood_group',
        'oxygen_level',
        'poc_name',
        'poc_phone',
        'patient_currently_admitted_at',
        'ward',
        'requirement',
        'oxygen',
        'plasma',
        'medicines',
        'bed',
        'other',
        'user_id',
        'closted_at',
        'marked_by_user',
        'comment',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'dob' => 'date',
        'gender' => 'string',
        'blood_group' => 'string',
        'oxygen_level' => 'string',
        'poc_name' => 'string',
        'poc_phone' => 'string',
        'patient_currently_admitted_at' => 'string',
        'ward' => 'string',
        'requirement' => 'string',
        'oxygen' => 'string',
        'plasma' => 'string',
        'medicines' => 'string',
        'bed' => 'string',
        'other' => 'string',
        'user_id' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:100',
        'dob' => 'required|date',
        'gender' => 'required|in:male,female,other',
        'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        'oxygen_level' => 'required',
        'poc_name' => 'required|max:100',
        'poc_phone' => 'required|min:10|max:10',
        'patient_currently_admitted_at' => 'required',
        'ward' => 'required',
        'requirement' => 'required|array',
        'user_id' => 'required'
    ];

    public static $updatePostRules = [
        'post_id' => 'required',
    ];


    public function user()
    {
        return $this->belongsTo(\App\Models\CovidCareUser::class, 'user_id');
    }
    
}
