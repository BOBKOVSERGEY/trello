<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardStoreRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Models\Desk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * @param CardStoreRequest $request
     * @return CardResource
     */
    public function store(CardStoreRequest $request)
    {
        $newCard = Card::create($request->validated());

        return new CardResource($newCard);
    }


    public function show(Card $card)
    {
        return new CardResource($card);
    }


    public function update(CardStoreRequest $request, Card $card)
    {
        $card->update($request->validated());
        return new CardResource($card);
    }


    /**
     * @param Card $card
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function destroy(Card $card)
    {
        $card->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
