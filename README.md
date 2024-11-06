# PERTANYAAN
1. Apa yang dimaksud dengan Laravel Sanctum?

Laravel Sanctum adalah paket untuk Laravel yang menyediakan solusi sederhana dalam manajemen otentikasi berbasis token untuk aplikasi single-page (SPA), aplikasi mobile, dan API sederhana. Dengan Sanctum, Laravel memungkinkan aplikasi untuk mengeluarkan dan mengelola token API dan session-based authentication tanpa harus menggunakan Laravel Passport, yang lebih kompleks.
   
2. Bagaimana cara mengelola token autentikasi di Laravel?

   Instalasi Sanctum: Instal Sanctum melalui Composer: composer require laravel/sanctum

   Konfigurasi Sanctum: Publikasikan konfigurasi Sanctum dengan perintah: php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

Lalu, jalankan migrasi untuk membuat tabel personal_access_tokens: php artisan migrate

Setup Middleware: Untuk melindungi rute API, tambahkan middleware Sanctum ke grup middleware API di app/Http/Kernel.php: 'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],

Pembuatan Token: Di dalam kontroler, gunakan metode createToken untuk membuat token: $token = $user->createToken('auth_token')->plainTextToken;
return response()->json(['token' => $token]);

Penggunaan Token untuk Autentikasi: Kirim token sebagai header Authorization saat melakukan request ke endpoint yang dilindungi:

http
Copy code
 
Authorization: Bearer <token>

3. Sebutkan langkah-langka untuk menambahkan otorisasi berbasis peran dalam API!
   
   Otorisasi berbasis peran memungkinkan kontrol akses API berdasarkan peran pengguna (misalnya, admin, user). Berikut adalah langkah-langkahnya:

Tambahkan Kolom role pada Tabel users: Buat kolom role dalam tabel users yang dapat menampung jenis peran pengguna: php artisan make:migration add_role_to_users_table --table=users

Di file migrasi, tambahkan: $table->string('role')->default('user');

Perbarui Model User: Tambahkan properti $fillable untuk role di model User: protected $fillable = [
    'name', 'email', 'password', 'role',
];

Buat Middleware CheckRole: Middleware ini akan memeriksa peran pengguna. Buat dengan perintah: php artisan make:middleware CheckRole

Di file app/Http/Middleware/CheckRole.php: namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            return response()->json(['message' => 'Unauthorized access'], 403);
        }
        return $next($request);
    }
}

Daftarkan Middleware: Tambahkan middleware CheckRole di app/Http/Kernel.php: protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
];

Lindungi Rute API Berdasarkan Peran: Gunakan middleware role pada rute yang ingin dibatasi:
Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin', function () {
    return response()->json(['message' => 'Welcome, Admin!']);
});


