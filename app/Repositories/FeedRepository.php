<?php

namespace App\Repositories;

use App\Models\Feed;
use App\Repositories\BaseRepository;

/**
 * Class FeedRepository
 * @package App\Repositories
 * @version April 17, 2021, 9:14 pm UTC
*/

class FeedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'content',
        'user_id'
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
        return Feed::class;
    }
}
