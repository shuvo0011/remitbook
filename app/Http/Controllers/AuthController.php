<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

// ...........................................................................................




    /**
     * Login
     * @OA\Post (
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Remit book User login",
     *     description="This api create token for new user. This token is use for other validation",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "email":"master@gmail.com",
     *                     "password":"123456",
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Successfully Register",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": 200,
     *                      "success": true,
     *                       "message": "Login successfully",
     *                       "data": {
     *                           "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY1NzA4MTI3MSwiZXhwIjoxNjU3MDg0ODcxLCJuYmYiOjE2NTcwODEyNzEsImp0aSI6IjhkaE1WTEdqVkFSUzdiUjAiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.DhPxxptVkm_-Ya1m-FMXuR6bn_SCnkVWW8UynPNaeCc",
     *                           "token_type": "bearer",
     *                           "expires_in": 3600
     *                            }
     *                       }
     *            )
     *     ), 
     * 
     * 
     * 
     * 
     * )
     */

    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');

        $token = Auth('api')->attempt($credentials);

        if ($token = Auth('api')->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }




    public function logout()
    {
        Auth()->logout();
        return response()->json(
            ['message' => 'Successfully logged out']
        );
    }




//  .......................   all function decleared here  ....................

    protected function respondWithToken($token)
    {
        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Login successfully",
            "data" => [
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => auth::factory()->getTTL() * 60
            ]
        ]);
    }

    public function guard()
    {
        return Auth::guard('api');
    }











}
