<?php

namespace App\Http\Controllers;

use App\Http\Requests\HiveRequest;
use App\Models\Hive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
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
            'status' => 'success',
            'message' => 'Successfully created the hive.',
            'code' => JsonResponse::HTTP_CREATED,
            'hive' => $hive
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
