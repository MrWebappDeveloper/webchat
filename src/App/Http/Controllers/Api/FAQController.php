<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MrWebappDeveloper\Webchat\App\Models\FAQ;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\FetchFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\StoreFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Requests\UpdateFAQRequest;
use MrWebappDeveloper\Webchat\App\Http\Services\FAQ\FAQServiceProxy;

class FAQController extends Controller
{
    public function __construct(
        private FAQServiceProxy $service
    )
    {
    }

    /**
     * Fetch list of FAQs
     */
    public function index(FetchFAQRequest $request): JsonResponse
    {
        return ($faqs = $this->service->fetchByPaginate($request)) ?
            response()->json([
                'status' => 'ok',
                'faqs' => $faqs,
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
     * Create new FAQ API
     *
     * @param StoreFAQRequest $request
     * @return JsonResponse
     */
    public function store(StoreFAQRequest $request): JsonResponse
    {
        return $this->service->store($request) ?
            response()->json([
                'status' => 'ok',
                'message' => 'New FAQ stored !',
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        if ($faq = $this->service->fetchSingle($id))
            return response()->json([
                'status' => 'ok',
                'data' => $faq,
            ]);
        else
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in the server .'
            ], 500);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FAQ $FAQ)
    {
        return view('webchat::edit');
    }

    /**
     * Update FAQ API method
     */
    public function update(UpdateFAQRequest $request, FAQ $faq): JsonResponse
    {
        return $this->service->update($request, $faq) ?
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
    public function destroy(FAQ $faq)
    {
        return $this->service->delete($faq) ?
            response()->json([
                'status' => 'ok',
                'message' => 'Deleted !',
            ]) :
            response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in server !',
            ], 500);
    }
}
