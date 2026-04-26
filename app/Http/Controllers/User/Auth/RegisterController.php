<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller {

    use RegistersUsers;

    public function __construct() {
        parent::__construct();
    }

    public function showRegistrationForm() {
        $pageTitle = "Register";
        Intended::identifyRoute();
        return view('Template::user.auth.register', compact('pageTitle'));
    }

    protected function validator(array $data) {

        $passwordValidation = Password::min(6);

        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validate     = Validator::make($data, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'username'  => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-z0-9_ ]+$/',
                function ($attribute, $value, $fail) {
                    $normalized = Str::lower(preg_replace('/\s+/', ' ', trim((string) $value)));
                    $exists = User::whereRaw('LOWER(username) = ?', [$normalized])->exists();
                    if ($exists) {
                        $fail('اسم المستخدم مستعمل من قبل');
                    }
                },
            ],
            'email'     => 'required|string|email|unique:users',
            'password'  => ['required', 'confirmed', $passwordValidation],
            'captcha'   => 'sometimes|required',
            'agree'     => $agree
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required',
            'username.regex' => 'Username can contain only small letters, numbers, underscore and spaces.'
        ]);

        return $validate;
    }

    public function register(Request $request) {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }

        // Normalize before validation (case-insensitive username)
        if ($request->has('username')) {
            $normalizedUsername = preg_replace('/\s+/', ' ', trim((string) $request->input('username')));
            $request->merge(['username' => Str::lower($normalizedUsername)]);
        }
        if ($request->has('email')) {
            $request->merge(['email' => Str::lower((string) $request->input('email'))]);
        }

        $this->validator($request->all())->validate();
        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }



    protected function create(array $data) {

    // نجيب اسم المحيل من الفورم أو من السيشن (إذا استعملت رابط مستقبلاً)
    $referBy = $data['referBy'] ?? session()->get('reference');
    $referBy = is_string($referBy) ? Str::lower(trim($referBy)) : $referBy;

    if ($referBy) {
        $referUser = User::whereRaw('LOWER(username) = ?', [Str::lower((string) $referBy)])->first();
    } else {
        $referUser = null;
    }

    // User Create
    $user            = new User();
    $user->email     = Str::lower((string) $data['email']);
    $user->firstname = $data['firstname'];
    $user->lastname  = $data['lastname'];
    $user->username  = Str::lower((string) $data['username']);
    $user->password  = Hash::make($data['password']);

    // هنا مربط الفرس: تخزين ref_by
    $user->ref_by    = $referUser ? $referUser->id : 0;

    $user->kv = gs('kv') ? Status::NO : Status::YES;
    $user->ev = gs('ev') ? Status::NO : Status::YES;
    $user->sv = gs('sv') ? Status::NO : Status::YES;
    $user->ts = Status::DISABLE;
    $user->tv = Status::ENABLE;
    $user->save();

    // باقي الكود كما هو (AdminNotification, UserLogin, ...)



        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();


        //Login Log Create
        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = isset($info['long']) ? implode(',', $info['long']) : '';
            $userLogin->latitude     = isset($info['lat']) ? implode(',', $info['lat']) : '';
            $userLogin->city         = isset($info['city']) ? implode(',', $info['city']) : '';
            $userLogin->country_code = isset($info['code']) ? implode(',', $info['code']) : '';
            $userLogin->country      = isset($info['country']) ? implode(',', $info['country']) : '';
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = isset($userAgent['browser']) ? $userAgent['browser'] : '';
        $userLogin->os      = isset($userAgent['os_platform']) ? $userAgent['os_platform'] : '';
        $userLogin->save();

        return $user;
    }

    public function checkUser(Request $request) {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $email = Str::lower(trim((string) $request->email));
            $exist['data'] = User::whereRaw('LOWER(email) = ?', [$email])->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->username) {
            $rawUsername = (string) $request->username;
            $normalized = Str::lower(preg_replace('/\s+/', ' ', trim($rawUsername)));

            $exist['data'] = User::whereRaw('LOWER(username) = ?', [$normalized])->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';

            $exist['suggestions'] = $this->usernameSuggestions($normalized);
        }

        return response()->json($exist);
    }

    private function usernameSuggestions(string $normalized, int $limit = 4): array {
        $normalized = Str::lower(preg_replace('/\s+/', ' ', trim($normalized)));

        if ($normalized === '' || strlen($normalized) < 3) {
            return [];
        }

        if (!preg_match('/^[a-z0-9_ ]+$/', $normalized)) {
            return [];
        }

        $candidates = [];

        // deterministic-ish, related to base
        $candidates[] = $normalized . '12';
        $candidates[] = $normalized . '_21';
        $candidates[] = $normalized . 'x';
        $candidates[] = $normalized . '_93';

        // add a few more options to increase chance of availability
        for ($i = 0; $i < 12; $i++) {
            $n = random_int(10, 99);
            $candidates[] = $normalized . $n;
            $candidates[] = $normalized . '_' . $n;
        }

        // sanitize length + de-dupe
        $candidates = array_values(array_unique(array_filter(array_map(function ($u) {
            $u = (string) $u;
            return strlen($u) > 50 ? substr($u, 0, 50) : $u;
        }, $candidates))));

        // remove same-as input
        $candidates = array_values(array_filter($candidates, fn($u) => $u !== $normalized));

        if (!$candidates) {
            return [];
        }

        // Keep only available
        $taken = User::whereIn('username', $candidates)->pluck('username')->map(fn($u) => Str::lower((string) $u))->all();
        $taken = array_flip($taken);

        $available = [];
        foreach ($candidates as $candidate) {
            if (!isset($taken[$candidate])) {
                $available[] = $candidate;
            }
            if (count($available) >= $limit) {
                break;
            }
        }

        return $available;
    }

    public function registered() {
        return to_route('user.home');
    }
}
