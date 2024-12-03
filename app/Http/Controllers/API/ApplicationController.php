<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Http\Controllers\API\BaseController as BaseController;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApplicationController extends BaseController {
    //
     /**
     * Retrieves all User Mobile Applications.
     *
     * Returns all Mobile user application inquiries
     */
    #[OpenApi\Operation(tags: ['applications'])]
    public function applicationPage() {
        $today = Carbon::now();
        $startDate = $today->startOfWeek();
        $endDate = $today->endOfWeek();

        // $day1 = $today->addDays(1);
        // $dt->subDays(29);
        
        $applications = DB::table('applications')->get();
        $totalApplications = $applications->count();
        $applicationApproved = $applications->where('status', 'Approved')->count();
        // $kiosk = DB::table('kiosk')->whereBetween('created_at', [$startDate, $endDate])->get();
        // $Day1 = DB::table('kiosk')->where('created_at', $day1)->get();
        

        $output = [
            'applicationsToday' => $applications,
            'totalApplications' => $totalApplications,
            'applicationApproved' => $applicationApproved,
            'kioskData' => 'coming soon',
            'totalRevenue' => 'Another made up dataSet will change with Henry'
        ];
        return $this->sendResponse($output, 'Application Data returned');

    }

    /**
     * Change Applications status of applicant.
     *
     * Returns all new application status
     */
    #[OpenApi\Operation(tags: ['applications'])]
    public function applicationStatus(Request $request, Application $application) {

        $request->validate([
            'status' => 'required|string|max:100',
        ]);

        $app = $application->id;
        $App = Application::find($app);
        $app->Status = $request->status; 
        $app->save();

        $output = 'Status';

        return $this->sendResponse($output, 'Application submitted succesfully.'); 
    }


}
