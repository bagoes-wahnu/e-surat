<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

use App\Http\Models\UserLog;
class EnergeekAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $idRole='')
    {
        $result = 'Forbidden';
        if(Auth::check()){
            $auth = Auth::user();

            $userdata = session('userdata');
            if(!array_key_exists('id_login', $userdata) || empty($userdata['id_login'])){
                $m_user_log = new UserLog();
                $m_user_log->usl_usr_id = $auth->usr_id;
                $m_user_log->usl_url = base_url();
                $m_user_log->usl_ip = get_ip_address();
                $m_user_log->usl_agent = $_SERVER['HTTP_USER_AGENT'];
                $m_user_log->save();

                $session['userdata']['id_user'] = $auth->usr_id;
                $session['userdata']['username'] = $auth->usr_username;
                $session['userdata']['name'] = $auth->usr_name;
                $session['userdata']['email'] = $auth->usr_email;
                $session['userdata']['password'] = $auth->password;
                $session['userdata']['role'] = $auth->usr_role;
                $session['userdata']['id_login'] = $m_user_log->usl_id;
                session($session);
            }

            if(!empty($idRole)){
                $roles = (strpos($idRole, '&') !== false)? explode('&', $idRole) : array($idRole);

                if(in_array($auth->usr_role, $roles)){
                    return $next($request);
                }

                $alerts[] = array('error', 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator', 'Peringatan!');
                session()->flash('alerts', $alerts);
                return $request->expectsJson() ? response()->json(helpResponse(403, [], 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator'), 403) : redirect()->to(route('home'));
            }else{
                return $next($request);
            }

        }else{
            return $request->expectsJson() ? response()->json(helpResponse(401), 401) : redirect()->to(route('login').'?source='.url()->current());
        }
    }    
}
