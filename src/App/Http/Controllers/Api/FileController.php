<?php

namespace MrWebappDeveloper\Webchat\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MrWebappDeveloper\Webchat\App\Http\Controllers\Facade\MessageFacade;
use MrWebappDeveloper\Webchat\App\Http\Requests\StoreFileRequest;

class FileController extends Controller
{
    /**
     * API آپلود فایل بر روی سرور
     *
     * فایل را در سرور ذخیره می کند و لینک دسترسی به
     * آن را در پاسخ به کالینت می فرستد
     */
    public function store(StoreFileRequest $request): JsonResponse
    {
        if ($link = MessageFacade::saveFile($request->file('file')))
            return response()->json([
                'status' => 'ok',
                'message' => 'file stored successfully !',
                'link' => $link
            ]);
        else
            return response()->json([
                'status' => 'server error',
                'message' => 'There are some errors in upload and save file !'
            ]);
    }
}
