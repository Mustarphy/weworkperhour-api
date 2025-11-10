<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "user_id" => $this->user_id,
            "overview" => $this->overview,
            "intro" => url($this->intro),
            "resume_files" => $this->resumeFiles,
            "educations" => $this->educations,
            "skills" => $this->skills,
            "resume" => $this->resume,
            "resume_title" => $this->resume_title,
            "experience" => $this->experience,
            "portfolio" => $this->Portfolio->count() > 0 ? PorfolioResource::collection($this->Portfolio) : [],
        ];
    }
}
