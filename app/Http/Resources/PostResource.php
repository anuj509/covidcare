<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'oxygen_level' => $this->oxygen_level,
            'poc_name' => $this->poc_name,
            'poc_phone' => $this->poc_phone,
            'patient_currently_admitted_at' => $this->patient_currently_admitted_at,
            'ward' => $this->ward,
            'requirement' => $this->requirement,
            'oxygen' => $this->oxygen,
            'plasma' => $this->plasma,
            'medicines' => $this->medicines,
            'bed' => $this->bed,
            'other' => $this->other,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
