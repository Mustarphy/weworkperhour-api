<?php

namespace App\Http\Resources;

use App\Models\SocialMedia;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function socialMedias () {
        if($this->socials->count() == 0) {
            foreach (["Facebook", "Instagram", "Twitter", "LinkedIn", "Youtube"] as $s) {
                SocialMedia::create([
                    "user_id" => $this->id,
                    "label" => $s
                ]);
            }
        }
        return SocialMedia::where("user_id", $this->id)->select("id", "label", "value")->get();
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "status" => strtolower($this->status),
            'role' => $this->role,
            "address" => $this->address,
            "phone_no" => $this->phone_no,
            "avatar" => $this->avatar ? url($this->avatar) : $this->avatar,
            "company" => $this->company,
            "bio" => $this->bio,
            "country" => $this->country,
            "city" => $this->city,
            "state" => $this->state,
            "zip_code" => $this->zip_code,
            "qualification" => $this->qualification,
            "gender" => $this->gender,
            "expected_salary" => $this->expected_salary,
            "social_medias" => $this->socialMedias(),
            'address' => $this->address,
            'education' => $this->education,
            'experience' => $this->experience,
            'skills' => $this->skills,
            'cv' => $this->cv ? url($this->cv) : null,
        ];
    }
}
