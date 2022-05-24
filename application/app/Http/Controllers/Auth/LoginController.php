<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Http\Models\User;
use App\Http\Models\UserLog;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->title = 'Login';
        $this->middleware('guest')->except('logout');
    }

    public function index(Request $request)
    {
        $source = $request->get('source');
        if(Auth::check()){
            return redirect(empty($source)? 'home' : $source);
        }else{
            $data['title'] = $this->title;
            $data['source'] = $source;
            return view('login', $data);
        }
    }

    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    // public function username()
    // {
    //     return 'email';
    // }

    public function authenticate(Request $request)
    {
        if(Auth::check()){
            return $request->expectsJson() ? response()->json(helpResponse(200, [], 'Anda sudah login')) : redirect()->intended('home');
        }else{
            $credentials = $request->only('username', 'password');
            $usr_username = $request->input('username');
            $password = $request->input('password');

            $user = User::get_auth($usr_username);

            if (!empty($user) && (Hash::check($password, $user->usr_password))) {
                Auth::login($user);
                $session['userdata']['id_user'] = $user->usr_id;
                $session['userdata']['username'] = $user->usr_username;
                $session['userdata']['name'] = $user->usr_name;
                $session['userdata']['email'] = $user->usr_email;
                $session['userdata']['role'] = $user->usr_role;
                $session['userdata']['grade'] = $user->usr_grade;
                $session['userdata']['id_jabatan'] = $user->usr_jbt_id;
                $session['userdata']['id_atasan'] = $user->jbt_induk;
                $session['userdata']['uptd'] = $user->jbt_uptd;

                unset($user->usr_password);

                $m_user_log = new UserLog();
                $m_user_log->usl_usr_id = $user->usr_id;
                $m_user_log->usl_url = base_url();
                $m_user_log->usl_ip = get_ip_address();
                $m_user_log->usl_agent = $_SERVER['HTTP_USER_AGENT'];
                $m_user_log->save();
                $session['userdata']['id_login'] = $m_user_log->usl_id;
                session($session);

                $alerts[] = array('success', 'Anda berhasil login', 'OK');
                $request->session()->flash('alerts', $alerts);
                return $request->expectsJson() ? response()->json(helpResponse(200, $user, 'Selamat Anda berhasil login'), 200) : redirect()->intended('home');
            }else{
                $alerts[] = array('warning', 'Username atau Password Anda salah', 'Pemberitahuan');
                $request->session()->flash('alerts', $alerts);
                return $request->expectsJson() ? response()->json(helpResponse(401, [], 'Username atau Password Anda salah'), 401) : redirect()->intended('login');
            }
        }
    }

    public function logout(Request $request)
    {
        if(Auth::check()){
            Auth::logout();
            $request->session()->invalidate();
        }

        return redirect('/');
    }
}
