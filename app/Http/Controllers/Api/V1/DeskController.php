<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeskStoreRequest;
use App\Http\Requests\DeskUpdateRequest;
use App\Http\Resources\DeskResource;
use App\Models\Desk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeskController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return DeskResource::collection(Desk::orderBy('created_at', 'desc')->get());
    }

    /**
     * @param DeskStoreRequest $request
     * @return DeskResource
     */
    public function store(DeskStoreRequest $request)
    {

        $created_desk = Desk::create($request->validated());

        return new DeskResource($created_desk);
    }

    /**
     * Display the specified resource.
     *
     * @param  Desk $desk
     * @return DeskResource
     */
    public function show(Desk $desk)
    {
        return new DeskResource($desk);
    }

    /**
     * @param DeskStoreRequest $request
     * @param Desk $desk
     * @return DeskResource
     */
    public function update(DeskStoreRequest $request, Desk $desk)
    {
        $desk->update($request->validated());

        return new DeskResource($desk);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Desk $desk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Desk $desk)
    {
        $desk->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
