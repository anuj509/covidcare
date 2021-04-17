<?php

namespace App\Repositories;

use App\Models\CovidCareUser;
use App\Repositories\BaseRepository;

/**
 * Class CovidCareUserRepository
 * @package App\Repositories
 * @version April 17, 2021, 8:34 pm UTC
*/

class CovidCareUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'phone'
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
        return CovidCareUser::class;
    }
}
