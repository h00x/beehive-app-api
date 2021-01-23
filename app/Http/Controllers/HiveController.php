<?php

namespace App\Http\Controllers;

use App\Http\Requests\HiveRequest;
use App\Http\Resources\HiveCollection;
use App\Models\Hive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $hives = auth()->user()->hives()->paginate(10);

        if ($hives->isEmpty()) {
            return (new HiveCollection($hives))
                ->additional([
                    'status' => 'error',
                    'message' => 'No hives found.',
                    'code' => JsonResponse::HTTP_NOT_FOUND,
                ])
                ->response()
                ->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
        }

        return (new HiveCollection($hives))
            ->additional([
                'status' => 'success',
                'message' => 'Successfully found the hives.',
                'code' => JsonResponse::HTTP_OK,
            ])
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param HiveRequest $request
     * @return JsonResponse
     */
    public function store(HiveRequest $request)
    {
        $hive = auth()->user()->hives()->create($request->validated());

        return response()->json([
            'hive' => $hive,
            'status' => 'success',
            'message' => 'Successfully created the hive.',
            'code' => JsonResponse::HTTP_CREATED,
        ])
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Hive $hive
     * @return Response
     */
    public function show(Hive $hive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Hive $hive
     * @return Response
     */
    public function update(Request $request, Hive $hive)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Hive $hive
     * @return Response
     */
    public function destroy(Hive $hive)
    {
        //
    }
}
