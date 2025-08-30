<?php
    namespace App\Services;
    use Firebase\JWT\JWT;

    class AuthService {
        private $secret;
        public function __construct(){
            $this->secret = getenv('JWT_SECRET') ?: 'CHANGE_THIS_SECRET';
        }
        public function attempt($user,$pass){
            if ($user === 'admin' && $pass === 'password'){
                $payload = [
                    'iat' => time(),
                    'exp' => time() + 3600,
                    'sub' => $user
                ];
                return JWT::encode($payload, $this->secret, 'HS256');
            }
            return false;
        }
        public function verify($token){
            try { return (array) JWT::decode($token, $this->secret, ['HS256']); }
            catch (\Exception $e) { return false; }
        }
    }
