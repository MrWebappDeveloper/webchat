<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Webchat\app\Http\Requests\SendWizardMenuManually;
use MrWebappDeveloper\Webchat\App\Models\Wizard;
use MrWebappDeveloper\Webchat\App\Http\Requests\FetchWizardsRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\SendWizardMenuManuallyRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreWizardRequest;
use MrWebappDeveloper\Webchat\App\Http\Requests\UpdateWizardRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\Wizard\WizardServiceProxy;

class WizardController extends Controller
{
    public function __construct(
        private WizardServiceProxy $service,
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FetchWizardsRequest $request)
    {
        return ($wizards = $this->service->fetchByPaginate($request)) ?
            response()->json([
                'status' => 'ok',
                'wizards' => $wizards
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('webchat::create');
    }

    /**
     * Store new wizard API method
     */
    public function store(StoreWizardRequest $request): JsonResponse
    {
        return $this->service->store($request) ?
            response()->json([
                'status' => 'ok',
                'message' => 'New wizard stored !',
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }

    /**
     * Show the specified resource.
     */
    public function show(Wizard $wizard)
    {
        return $this->service->send($wizard) ?
            response()->json([
                'status' => 'ok',
                'message' => 'Sent !',
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wizard $wizard)
    {
        return response()->json([
            'faqs' => $this->service->faqs($wizard)
        ]);
    }

    /**
     * Update Wizard API method
     */
    public function update(UpdateWizardRequest $request, Wizard $wizard): JsonResponse
    {
        return $this->service->update($request, $wizard) ?
            response()->json([
                'status' => 'ok',
                'message' => 'Updated !',
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wizard $wizard)
    {
        return $this->service->delete($wizard) ?
            response()->json([
                'status' => 'ok',
                'message' => 'Deleted !',
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }

    /**
     * منوی اصلی (صفحه اول) ویزارد ها را برای کاربر ارسال می کند
     *
     * @param SendWizardMenuManuallyRequest $request
     * @return JsonResponse
     */
    public function sendMenu(SendWizardMenuManuallyRequest $request):JsonResponse
    {
        return $this->service->send(null) ?
            response()->json([
                'status' => 'ok',
                'message' => 'wizards menu sent to you through socket connection !',
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }
}
