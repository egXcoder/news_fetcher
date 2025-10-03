<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    /**
     * Get articles for public API.
     *
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get a paginated list of articles",
     *     description="Returns a paginated list of articles with optional ordering and page size",
     *     operationId="getArticles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, minimum=1)
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
        // Validate query parameters
        $validated = $request->validate([
            'perPage' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'orderby' => [
                'sometimes',
                'string',
                'regex:/^(created_at|updated_at)\s+(asc|desc)$/i'
            ],
        ]);


        // Set defaults
        $perPage = $validated['perPage'] ?? 10;
        $page = $validated['page'] ?? 1;
        $orderby = $validated['orderby'] ?? 'created_at desc';

        // Parse orderby
        [$column, $direction] = explode(' ', $orderby);

        // Fetch paginated articles
        $articles = Article::orderBy($column, $direction)
        ->paginate($perPage, ['*'], 'page', $page);


        return response()->json($articles);
    }
}
