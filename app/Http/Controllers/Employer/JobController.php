<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\JobType;
use App\Models\WwphJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;


class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = $request->query("title");
        $status = $request->query("status") ? $request->query("status") : "";
        $jobType = $request->query("jobType");
        $jobs = WwphJob::where("company_id", auth()->user()->id);
        if($status) {
            $jobs = $jobs->where("status", $status);
        }
        if($title != "") $jobs = $jobs->where("title", "LIKE", "%".$title."%");
        if($jobType != "") {
            $thJobType = JobType::where("title", $jobType)->first();
            if($thJobType) {
                $jobs = $jobs->where("job_type", $thJobType->id);
            }
        }
        $jobs = $jobs->get();
        $recent = JobResource::collection($jobs);

        return okResponse("fetched jobs", $recent);
    }
    public function shareJob($id)
    {
        $job = WwphJob::where("id", $id)->first();
        if(!$job) {
            return errorResponse("Job not found");
        }
        // sendMail2($request->to, "Shared Job - ". $job->title, )
        return okResponse("Job shared");
    }

    public function deleteJob($id)
    {
        $job = WwphJob::where("id", $id)->where("company_id", auth()->user()->id)->delete();
        return okResponse("Job deleted");
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    try {
        \Log::info('Incoming Job Post Request', $request->all());

        // Get only fillable fields
        $data = $request->only((new WwphJob)->getFillable());

        // Set defaults for all NOT NULL columns
        $data = array_merge([
            'company_id' => auth()->user()->id ?? 1, // fallback in case auth fails
            'title' => 'Untitled Job',
            'description' => '',
            'requirements' => '',
            'work_type' => 1,
            'job_type' => 1,
            'category' => 1,
            'salary' => 'Monthly',
            'budget' => '0',
            'experience' => '',
            'job_cover' => '',
            'skills' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'naration' => '',
            'job_role' => '', 
            'location' => '',
            'status' => 'active',
        ], $data);

        // Convert skills array to comma-separated string
        if (isset($data['skills']) && is_array($data['skills'])) {
            $data['skills'] = implode(',', $data['skills']);
        }

        // Validation
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required|string|min:3',
            'requirements' => 'required|string|min:3',
            'work_type' => 'required|integer',
            'job_type' => 'required|integer',
            'category' => 'required|integer',
            'salary' => 'required|string',
            'budget' => 'required|string',
            'experience' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Debugging log before insert
        \Log::info('Job Data to Insert:', $data);

        // Create job
        $job = WwphJob::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Job created successfully',
            'job' => new \App\Http\Resources\JobResource($job)
        ]);

    } catch (\Exception $e) {
        \Log::error('Job Creation Failed: '.$e->getMessage(), $request->all());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create job: '.$e->getMessage()
        ], 500);
    }
}



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
