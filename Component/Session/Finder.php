<?php

namespace Floxim\Floxim\Component\Session;

use Floxim\Floxim\System;
use Floxim\Floxim\System\Fx as fx;

class Finder extends System\Finder
{

    protected $cookie_name = 'fx_sid';

    public function start($data = array())
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $now = time();
        $data = array_merge(array(
            'ip'                 => sprintf("%u", ip2long($ip)),
            'session_key'        => md5(time() . rand(0, 1000) . $ip),
            'start_time'         => $now,
            'last_activity_time' => $now
        ), $data);
        $data['remember'] = $data['remember'] ? 1 : 0;
        $session = $this->create($data);
        $session->save();
        $session->setCookie();
        return $session;
    }

    /*
     * @todo should we do something with www/nowww problem?
     */
    public function setCookie($sid, $time)
    {
        $host = null;
        setcookie($this->cookie_name, $sid, $time, "/", $host);
    }

    public function load()
    {
        static $session = null;
        if (is_null($session)) {
            $this->dropOldSessions();
            $session_key = fx::input()->fetchCookie($this->cookie_name);
            if (!$session_key) {
                return null;
            }
            $session = $this->getByKey($session_key);
            if ($session) {
                $session->set('last_activity_time', time())->save();
            }
        }
        return $session;
    }

    public function dropOldSessions()
    {
        $ttl = (int)fx::config('auth.remember_ttl');
        fx::db()->query('delete from {{session}} ' . 'where ' . 'user_id is not null ' . 'and last_activity_time + ' . $ttl . ' < ' . time());
    }

    public function getByKey($session_key)
    {
        return $this->where('session_key', $session_key)->where('site_id', array(fx::env('site_id'), 0))->one();
    }

    public function stop()
    {
        $session_key = fx::input()->fetchCookie($this->cookie_name);
        if (!$session_key) {
            return;
        }
        $this->setCookie(null, null);
        $session = $this->getByKey($session_key);
        if (!$session) {
            return;
        }
        $session->delete();
    }
}