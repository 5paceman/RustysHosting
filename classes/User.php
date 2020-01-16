<?php
class User {
    private $_db,
            $_data,
            $_sessionName,
            $_isLoggedIn,
            $_cookieName;

    public function __construct($user = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');

        if(!$user) {
            if(Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);

                if($this->find($user)) {
                    $this->_isLoggedIn = true;
                } else {

                }
            }
        } else {
            $this->find($user);
        }
    }

    public function create($fields = array()) {
        if(!$this->_db->insert('users', $fields)) {
            throw new Exception('There was a problem creating an account.');
        }
    }

    public function find($username = null) {
        if($username) {
            $field = (is_numeric($username)) ? 'id' : 'username';
            $data = $this->_db->get('users', array($field, '=', $username));
            if($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
    }

    public function login($username = null, $password = null, $remember = false) {
        
        if(!$username && !$password && $this->exists()) {
            Session::put($this->_sessionName, $this->data()->id);
            return true;
        } else {
            $user = $this->find($username);
            if($user) {
                if($this->data()->password === Hash::make($password, $this->data()->salt)) {
                    Session::put($this->_sessionName, $this->data()->id);

                    if($remember) {
                        $hash = Hash::unique();
                        $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));
                        if(!$hashCheck->count()) {
                            $this->_db->insert('users_session', array(
                                'user_id' => $this->_data->id,
                                'hash' => $hash
                            ));
                        } else {
                            $hash = $hashCheck->first()->hash;
                        }

                        Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                    }

                    return true;
                }
            }
        }
        return false;
    }

    public function exists() {
        return (!empty($this->data())) ? true : false;
    }

    public function logout() {
        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
        $this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
    }

    public function update($fields = array(), $id = null) {
        if(!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }
        if(!$this->_db->update('users', $id, $fields)) {
            var_dump($this->_db->errorInfo());
            var_dump($this->_db->queryString());
            throw new Exception('There was a problem updating your data. ');     
        }
    }

    public function hasPermission($key) {
        $group = $this->_db->get('groups', array('id', '=', $this->data()->group));

        if($group->count()) {
            $permissions = json_decode($group->first()->permissions, true);
            if($permissions[$key] == true)
            {
                return true;
            }
        }
        return false;
    }

    public function isStripeCustomer() {
        return ($this->exists() && !empty($this->data()->stripe_id));
    }

    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }

    public function data() {
        return $this->_data;
    }
}