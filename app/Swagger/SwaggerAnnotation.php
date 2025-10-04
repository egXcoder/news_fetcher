<?php


/**
 * @OA\Info(
 *     title="My API",
 *     version="1.0.0",
 *     description="API documentation for My API"
 * )
 */




/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     required={"id", "title"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="News Title"),
 *     @OA\Property(property="description", type="string", example="Content of the news article"),
 *     @OA\Property(property="content", type="string", example="Full Content of the news article"),
 * )
 */