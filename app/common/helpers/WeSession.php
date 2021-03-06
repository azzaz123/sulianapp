<?php
namespace app\common\helpers;

class WeSession implements \SessionHandlerInterface{

    public static $uniacid;

    public static $openid;

    public static $expire;


    public static function start($uniacid, $openid, $expire = 7200) {
        WeSession::$uniacid = $uniacid;
        WeSession::$openid = $openid;
        WeSession::$expire = $expire;

        $cache_setting = $GLOBALS['_W']['config']['setting'];
        if (extension_loaded('memcache') && !empty($cache_setting['memcache']['server']) && !empty($cache_setting['memcache']['session'])) {
            self::setHandler('memcache');
        } elseif (extension_loaded('redis') && !empty($cache_setting['redis']['server']) && !empty($cache_setting['redis']['session'])) {
            self::setHandler('redis');
        } else {
            self::setHandler('mysql');
        }
        register_shutdown_function('session_write_close');
        session_start();
    }

    public static function setHandler($type = 'mysql') {
        $classname = "app\common\helpers\WeSession{$type}";
        if (class_exists($classname)) {
            $sess = new $classname;
        }
        if (version_compare(PHP_VERSION, '5.5') >= 0) {
            session_set_save_handler($sess, true);
        } else {
            session_set_save_handler(
                array(&$sess, 'open'),
                array(&$sess, 'close'),
                array(&$sess, 'read'),
                array(&$sess, 'write'),
                array(&$sess, 'destroy'),
                array(&$sess, 'gc')
            );
        }
        return true;
    }

    public function open($save_path, $session_name) {
        return true;
    }

    public function close() {
        return true;
    }


    public function read($sessionid) {
        return '';
    }


    public function write($sessionid, $data) {
        return true;
    }


    public function destroy($sessionid) {
        return true;
    }


    public function gc($expire) {
        return true;
    }

}

class WeSessionMemcache extends WeSession {
    protected $session_name;

    protected function key($sessionid) {
        return $this->session_name . ':' . $sessionid;
    }

    public function open($save_path, $session_name) {
        $this->session_name = $session_name;

        if (cache_type() != 'memcache') {
            trigger_error('Memcache ????????????????????????????????????????????? \$config[\'setting\'][\'memcache\'][\'session\'] ?????????0 ');
            return false;
        }
        return true;
    }

    public function read($sessionid) {
        $row = cache_read($this->key($sessionid));

        if ($row['expiretime'] < TIMESTAMP) {
            return '';
        }
        if(is_array($row) && !empty($row['data'])) {
            return $row['data'];
        }
        return '';
    }

    public function write($sessionid, $data) {
        if (empty($data) || (!empty($data) && empty($this->chk_member_id_session($data)))) {
            $read_data = $this->read($sessionid);

            if (!empty($member_data = $this->chk_member_id_session($read_data))) {
                $data .= $member_data;
            }
        }

        $row = array();
        $row['data'] = $data;
        $row['expiretime'] = TIMESTAMP + WeSession::$expire;

        return cache_write($this->key($sessionid), $row);
    }

    public function destroy($sessionid) {
        return cache_write($this->key($sessionid), '');
    }

    public function chk_member_id_session($read_data)
    {
        $member_data = '';

        if (!empty($read_data)) {
            preg_match_all('/yunzshop_([\w]+[^|]*|)/', $read_data, $name_matches);
            preg_match_all('/(a:[\w]+[^}]*})/', $read_data, $value_matches);

            if (!empty($name_matches)) {
                foreach ($name_matches[0] as $key => $val) {
                    if ($val == 'yunzshop_member_id') {
                        $member_data = $val . '|' . $value_matches[0][$key];
                    }
                }
            }
        }

        return $member_data;
    }
}

class WeSessionRedis extends WeSessionMemcache {
    public function __construct()
    {
    }

    public function open($save_path, $session_name) {
        $this->session_name = $session_name;

        if (cache_type() != 'redis') {
            trigger_error('Redis ????????????????????????????????????????????? \$config[\'setting\'][\'redis\'][\'session\'] ?????????0 ');
            return false;
        }
        return true;
    }
}

class WeSessionMysql extends WeSession {
    public function open($save_path, $session_name) {
        return true;
    }

    public function read($sessionid) {
        $sql = 'SELECT * FROM ' . tablename('core_sessions') . ' WHERE `sid`=:sessid AND `expiretime`>:time';
        $params = array();
        $params[':sessid'] = $sessionid;
        $params[':time'] = TIMESTAMP;
        $row = pdo_fetch($sql, $params);

        if(is_array($row) && !empty($row['data'])) {
            return $row['data'];
        }

        return '';
    }


    public function write($sessionid, $data) {
        if (empty($data) || (!empty($data) && empty($this->chk_member_id_session($data)))) {
            $read_data = $this->read($sessionid);

            if (!empty($member_data = $this->chk_member_id_session($read_data))) {
                $data .= $member_data;
            }
        }

        $row = array();
        $row['sid'] = $sessionid;
        $row['uniacid'] = WeSession::$uniacid;
        $row['openid'] = WeSession::$openid;
        $row['data'] = $data;
        $row['expiretime'] = TIMESTAMP + WeSession::$expire;

        return pdo_insert('core_sessions', $row, true) >= 1;
    }


    public function destroy($sessionid) {
        $row = array();
        $row['sid'] = $sessionid;

        return pdo_delete('core_sessions', $row) == 1;
    }


    public function gc($expire) {
        $sql = 'DELETE FROM ' . tablename('core_sessions') . ' WHERE `expiretime`<:expire';

        return pdo_query($sql, array(':expire' => TIMESTAMP)) == 1;
    }

    private function chk_member_id_session($read_data)
    {
        $member_data = '';

        if (!empty($read_data)) {
            preg_match_all('/yunzshop_([\w]+[^|]*|)/', $read_data, $name_matches);
            preg_match_all('/(a:[\w]+[^}]*})/', $read_data, $value_matches);

            if (!empty($name_matches)) {
                foreach ($name_matches[0] as $key => $val) {
                    if ($val == 'yunzshop_member_id') {
                        $member_data = $val . '|' . $value_matches[0][$key];
                    }
                }
            }
        }

        return $member_data;
    }
}