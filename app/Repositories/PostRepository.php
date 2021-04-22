<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\BaseRepository;

/**
 * Class PostRepository
 * @package App\Repositories
 * @version April 17, 2021, 9:05 pm UTC
*/

class PostRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
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
        'closed_at',
        'marked_by_user',
        'comment'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Post::class;
    }
}
