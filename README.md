<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

**REQUIRE SANTUM**

(Sanctum is usually inastalled and published by default)

```bash
composer require laravel/sanctum
```

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

**MIGRATE TO CREATE THE personal_access_tokens TABLE**

```bash
php artisan migrate
```

**CREATE AN USER AUTHENTICATION CONTROLLER USING THE User MODEL TO HANDLE OUR API REQUESTS**

```bash
php artisan make:controller UserAuthController
```

**REGISTER THE API ROUTES ON api.php:**

```php
use App\Http\Controllers\AuthController;

Route::post('register', [UserAuthController::class, 'register'])->name('register');

Route::post('login', [UserAuthController::class, 'login'])->name('login');

Route::post('logout', [UserAuthController::class, 'logout'])->name('logout')->name('logout')->middleware('auth:sanctum');
```

**CREATE THE METHODS ON UserAuthController.php TO HANDLE THE REQUESTS:**

```php
use Illuminate\Support\Facades\Hash;

// REGISTER USER METHOD
public function register(Request $request)
    {
        // VALIDATES THE DATA SENT BY THE REQUEST
        $registerUserData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8'
        ]);

        // CREATES A NEW USER AND GENERATES
        $user = User::create([
            'name' => $registerUserData['name'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);

        return response()->json([
            'message' => 'User Created ',
            'user' => $user
        ]);
    }

// LOGIN USER METHOD
public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);

        // CHECKS IF USER EXISTS
        // LOOKS FOR THE USER WITH THE SAME email AS THE loginUserData IN THE DATABASE
        $user = User::where('email', $loginUserData['email'])->first();

        // IS THE USER DOES NOT EXISTS OR PASSWORD IS WRONG, SEND ERROR MESSAGE
        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        //  CREATES A TOKEN AND LOGIN THE USER
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
        ]);
    }

// LOGOUT USER METHOD
public function logout()
    {
        // DELETES THE TOKENS FOR THE CURRENTLY AUTHENTICATED USER
        auth()->user()->tokens()->delete();

        return response()->json([
            "message" => "logged out"
        ]);
    }
```

**POSTMAN REQUESTS**

Register:
```
POST: http://127.0.0.1:8000/api/register

Body (form-data, key: value):

name: Francesco
email: francesco@test.com
password: password
```

Login:
```
POST: http://127.0.0.1:8000/api/login

Body (form-data, key: value):

email: francesco@test.com
password: password
```

Logout:
```
POST:  http://127.0.0.1:8000/api/logout
Authorization: Bearer Token
Token: [Access Token FROM LOGIN RESPONSE]
```