<?php

namespace App\Http\Controllers\Api\Games;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryGame;
use App\Models\Game;
use App\Models\GameFavorite;
use App\Models\GameLike;
use App\Models\GamesKey;
use App\Models\Gateway;
use App\Models\Provider;
use App\Models\Wallet;
use App\Traits\Providers\PlayFiverTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    use PlayFiverTrait;

    /**
     * @dev  
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = Provider::with(['games', 'games.provider'])
            ->whereHas('games')
            ->orderBy('name', 'desc')
            ->where('status', 1)
            ->get();

        return response()->json(['providers' =>$providers]);
    }
    public function gamesCategories()
    {
        $categories = Category::all();
        $games = [];
    
        foreach ($categories as $category) {
            $categoryGames = CategoryGame::where("category_id", $category->id)->get();
    
            if ($categoryGames->isNotEmpty()) {
                $numIterations = min(12, $categoryGames->count());
    
                for ($i = 0; $i < $numIterations; $i++) {
                    $game = Game::where("id", $categoryGames[$i]->game_id)->where("status", 1)->first();
    
                    if ($game != null) {
                       
                        $games[$category->name]['games'][$game->game_name] = $game;
                    }
                }
                
                $games[$category->name]["quantidade"] = CategoryGame::where("category_id", $category->id)->whereHas("game",function (Builder $query) {
                    $query->where("status",1);
                })->count();
                $games[$category->name]["pagina"] = 1;
                if($games[$category->name]["quantidade"] <= 12){
                    $games[$category->name]["UPagina"] = 1;
                }
                $games[$category->name]["quantidadeA"] = count($games[$category->name]['games']);

            }
        }
    
        return response()->json(['games' => $games]);
    }
    /**
     * @dev  
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        $featured_games = Game::with(['provider'])->where('is_featured', 1)->get();
        return response()->json(['featured_games' => $featured_games]);
    }

    /**
     * Source Provider
     *
     * @dev  
     * @param Request $request
     * @param $token
     * @param $action
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function sourceProvider(Request $request, $token, $action)
    {
        $tokenOpen = \Helper::DecToken($token);
        $validEndpoints = ['session', 'icons', 'spin', 'freenum'];

        if (in_array($action, $validEndpoints)) {
            if(isset($tokenOpen['status']) && $tokenOpen['status'])
            {
                $game = Game::whereStatus(1)->where('game_code', $tokenOpen['game'])->first();
                if(!empty($game)) {
                    $controller = \Helper::createController($game->game_code);

                    switch ($action) {
                        case 'session':
                            return $controller->session($token);
                        case 'spin':
                            return $controller->spin($request, $token);
                        case 'freenum':
                            return $controller->freenum($request, $token);
                        case 'icons':
                            return $controller->icons();
                    }
                }
            }
        } else {
            return response()->json([], 500);
        }
    }

    /**
     * @dev  
     * Store a newly created resource in storage.
     */
    public function toggleFavorite($id)
    {
        if(auth('api')->check()) {
            $checkExist = GameFavorite::where('user_id', auth('api')->id())->where('game_id', $id)->first();
            if(!empty($checkExist)) {
                if($checkExist->delete()) {
                    return response()->json(['status' => true, 'message' => 'Removido com sucesso']);
                }
            }else{
                $gameFavoriteCreate = GameFavorite::create([
                    'user_id' => auth('api')->id(),
                    'game_id' => $id
                ]);

                if($gameFavoriteCreate) {
                    return response()->json(['status' => true, 'message' => 'Criado com sucesso']);
                }
            }
        }
    }

    /**
     * @dev  
     * Store a newly created resource in storage.
     */
    public function toggleLike($id)
    {
        if(auth('api')->check()) {
            $checkExist = GameLike::where('user_id', auth('api')->id())->where('game_id', $id)->first();
            if(!empty($checkExist)) {
                if($checkExist->delete()) {
                    return response()->json(['status' => true, 'message' => 'Removido com sucesso']);
                }
            }else{
                $gameLikeCreate = GameLike::create([
                    'user_id' => auth('api')->id(),
                    'game_id' => $id
                ]);

                if($gameLikeCreate) {
                    return response()->json(['status' => true, 'message' => 'Criado com sucesso']);
                }
            }
        }
    }

    /**
     * @dev  
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
        $game = Game::with(['categories', 'provider'])->whereStatus(1)->find($id);
        if(!empty($game)) {

            if(Auth::guard("api")->check()) {

                $wallet = Wallet::where('user_id', auth('api')->id())->first();
                if($wallet->total_balance > 0) {
                    $game->increment('views');

                    $token = \Helper::MakeToken([
                        'id' => auth('api')->id(),
                        'game' => $game->game_code
                    ]);

                    switch ($game->distribution) {
                        
                        case 'play_fiver':
                            $playfiver = self::playFiverLaunch($game->game_id, $game->only_demo);
                            
                            if(isset($playfiver['launch_url'])) {
                                return response()->json([
                                    'game' => $game,
                                    'gameUrl' => $playfiver['launch_url'],
                                    'token' => $token
                                ]);
                            }
                        
                            return response()->json(['error' => $playfiver, 'status' => false ], 400);

                    }
                }
                return response()->json(['error' => 'Você precisa ter saldo para jogar', 'status' => false, 'action' => 'deposit' ], 200);
            }else{
                return response()->json(['error' => 'Você precisa tá autenticado para jogar', 'status' => false ], 400);

            }
        }
        return response()->json(['error' => '', 'status' => false ], 500);
    }

    /**
     * @dev  
     * Show the form for editing the specified resource.
     */
    public function allGames(Request $request)
    {
        $query = Game::query();
        $query->with(['provider', 'categories']);

        if (!empty($request->provider) && $request->provider != 'all') {
            $query->where('provider_id', $request->provider);
        }

        if (!empty($request->category) && $request->category != 'all') {
            $query->whereHas('categories', function ($categoryQuery) use ($request) {
                $categoryQuery->where('slug', $request->category);
            });
        }

        if (isset($request->searchTerm) && !empty($request->searchTerm) && strlen($request->searchTerm) > 2) {
            $query->whereLike(['game_code', 'game_name', 'distribution', 'provider.name'], $request->searchTerm);
        }else{
            $query->orderBy('views', 'desc');
        }

        $games = $query
            ->where('status', 1)
            ->paginate(12)->appends(request()->query());

        return response()->json(['games' => $games]);
    }

    
    /**
     * @dev isaacroque5
     * Integrando com a API do PlayFiver
     */
    public function webhookPlayFiver(Request $request)
    {
        return self::webhookPlayFiverAPI($request);
    }


    /**
     * @dev  
     * Update the specified resource in storage.
     */
    public function webhookMoneyCallbackMethod(Request $request)
    {
       
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
