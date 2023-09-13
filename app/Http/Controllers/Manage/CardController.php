<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;

class CardController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    // private $Card;

    // public function __construct()
    // {
    //     $this->middleware('auth:api,teacher');

    //     parent::__construct('cards', true);

    //     $this->Card = Card::whereHas('cardable.branches', function ($query) {
    //         $query->where('id', 1);
    //     });
    // }

    // public function index()
    // {
    //     return response()->json([
    //         "data" => $this->Card
    //     ]);
    // }
}
