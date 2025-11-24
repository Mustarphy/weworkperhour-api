<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentJobResource;
use App\Http\Resources\JobResource;
use App\Models\Department;
use App\Models\JobDepartment;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\WwphJob;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $title    = $request->query("title");
        $location = $request->query("location");
        $jobType  = $request->query("jobType");

        $jobs = WwphJob::with(['company', 'jobtype', 'worktype'])
            ->where("status", "active");

        if ($title != "") {
            $jobs = $jobs->where("title", "LIKE", "%{$title}%");
        }

        if ($location != "") {
            $jobs = $jobs->where("location", "LIKE", "%{$location}%");
        }

        if ($jobType != "") {
            $thJobType = JobType::where("title", $jobType)->first();
            if ($thJobType) {
                $jobs = $jobs->where("job_type", $thJobType->id);
            }
        }

        $jobs = $jobs->get();
        $recent = JobResource::collection($jobs);

        return okResponse("fetched jobs", $recent);
    }

    public function alert(Request $request)
    {
        $title    = $request->query("title");
        $location = $request->query("location");
        $jobType  = $request->query("jobType");

        $jobs = WwphJob::with(['company', 'jobtype', 'worktype', 'departments.department'])
            ->where("status", "active");

        if ($title != "") {
            $jobs = $jobs->where("title", "LIKE", "%{$title}%");
        }

        if ($location != "") {
            $jobs = $jobs->where("location", "LIKE", "%{$location}%");
        }

        if ($jobType != "") {
            $thJobType = JobType::where("title", $jobType)->first();
            if ($thJobType) {
                $jobs = $jobs->where("job_type", $thJobType->id);
            }
        }

        $jobs = $jobs->take(10)->get();
        $recent = JobResource::collection($jobs);

        return okResponse("fetched jobs", $recent);
    }

    public function saved()
    {
        $savedJobs = SavedJob::where("user_id", auth()->user()->id)->get();
        $jobs = [];
        foreach ($savedJobs as $savedJob) {
            $job = WwphJob::with(["company", "jobtype", "worktype"])
                ->where("status", "active")
                ->where("id", $savedJob->job_id)
                ->first();
            if ($job) $jobs[] = $job;
        }

        $recent = JobResource::collection($jobs);
        return okResponse("fetched saved jobs", $recent);
    }

    public function savedPost($id)
    {
        if (!SavedJob::where("job_id", $id)->where("user_id", auth()->user()->id)->first()) {
            SavedJob::create([
                "job_id" => $id,
                "user_id" => auth()->user()->id
            ]);
        }

        return okResponse("Job saved");
    }

    public function deletesaved($id)
    {
        SavedJob::where("job_id", $id)
            ->where("user_id", auth()->user()->id)
            ->delete();

        return okResponse("Job deleted");
    }

    public function applyJob(Request $request, $jobId)
    {
        $request->validate([
            'cv_url' => 'required|string',
            'experience_years' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
        ]);
    

        $user = auth()->user();

        // Save application using SmartCV URL
        $application = JobApplication::create([
            'job_id' => $jobId,
            'user_id' => $user->id,
            'cv' => $request->cv_url,
            'experience_years' => $request->experience_years,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);    

        return response()->json([
            'status' => 'success',
            'message' => 'Application submitted successfully.',
            'data' => $application
        ]);
    }

    // public function homepage()
    // {
    //     $recent = WwphJob::with(["company", "jobtype", "worktype"])
    //         ->where("status", "active")
    //         ->orderBy("id", "DESC")
    //         ->take(10)
    //         ->get();

    //     $departments = Department::with("DepartmentJobs")
    //         ->whereHas("DepartmentJobs")
    //         ->where("status", "active")
    //         ->take(5)
    //         ->get();

    //     $recent = JobResource::collection($recent);
    //     $departments = DepartmentJobResource::collection($departments);

    //     return okResponse("jobs fetched", [
    //         "recent" => $recent,
    //         "departments" => $departments,
    //     ]);
    // }

    public function fetchJobTypes()
    {
        $jobtypes = JobType::where("status", "active")->get();
        return okResponse("fetched", $jobtypes);
    }
}
