<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/articles",
     *     summary="Get a paginated list of articles",
     *     description="Returns a paginated list of articles with optional ordering and page size",
     *     operationId="getArticles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, minimum=1,maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="orderby",
     *         in="query",
     *         description="Column and direction to order by (e.g., 'created_at desc')",
     *         required=false,
     *         @OA\Schema(type="string", default="created_at desc", pattern="^(created_at|created_at|updated_at)\s+(asc|desc)$")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Article")
     *             ),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="last_page", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function getArticles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'orderby' => [
                'sometimes',
                'string',
                'regex:/^(created_at|updated_at)\s+(asc|desc)$/i'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Set defaults
        $perPage = $validated['perPage'] ?? 10;
        $page = $validated['page'] ?? 1;
        $orderby = $validated['orderby'] ?? 'created_at desc';

        // Parse orderby
        [$column, $direction] = explode(' ', $orderby);

        $cacheKey = "queries_get_articles_".$perPage."_".$page."_".$orderby;

        if($cached = Cache::store('redis')->get($cacheKey)){
            return $cached;
        }

        // Fetch paginated articles
        $articles = Article::orderBy($column, $direction)
        ->paginate($perPage, ['*'], 'page', $page);

        $response = response()->json($articles);

        Cache::store('redis')->get($cacheKey,$response,300);

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/articles/search",
     *     summary="Search articles",
     *     description="Search for articles by keyword with optional pagination and ordering",
     *     operationId="searchArticles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword (required, 3-255 characters)",
     *         required=true,
     *         @OA\Schema(type="string", minLength=3, maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page (optional, default 10, max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, minimum=1, maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number (optional, default 1)",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="orderby",
     *         in="query",
     *         description="Column and direction to order by (optional, e.g., 'created_at desc')",
     *         required=false,
     *         @OA\Schema(type="string", pattern="^(created_at|updated_at)\s+(asc|desc)$", default="created_at desc")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of matching articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Article")
     *             ),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="last_page", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function searchArticles(Request $request){
        $validator = Validator::make($request->all(), [
            'query'=> 'required|string|min:3|max:255',
            'perPage' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'orderby' => [
                'sometimes',
                'string',
                'regex:/^(created_at|updated_at)\s+(asc|desc)$/i'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Set defaults
        $perPage = $validated['perPage'] ?? 10;
        $page = $validated['page'] ?? 1;
        $orderby = $validated['orderby'] ?? 'created_at desc';
        $search = $validated['query'];

        // Parse orderby
        [$column, $direction] = explode(' ', $orderby);

        $articles = Article::orderBy($column,$direction)
        ->where(function(Builder $builder)use($search){
            $builder->where('title','like',"%$search%")
            ->orWhere('description','like',"%$search%")
            ->orWhere('content','like',"%$search%");
        })
        ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($articles);
    }
}
