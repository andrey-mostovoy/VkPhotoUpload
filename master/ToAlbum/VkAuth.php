<?php
class VkAuth {
    private $id = 3392771;
    private $key = 'JGWvCEqDEJA2ttBdkNa8';
    private $settings = 'photos';
    private $redirectUri = 'https://oauth.vk.com/blank.html';
    private $code = '';
    private $accessToken = '';
    private $sessionKey = 'vat';

    public function __construct() {
        $requestUri = $_SERVER['REQUEST_URI'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $requestUri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $requestUri);
        }
        $this->redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . $requestUri;
    }

    public function authorize() {
        if (!$this->isTokenExpire()) {
            $this->accessToken = $_SESSION[$this->sessionKey]['token'];
            return true;
        }
        if (empty($_GET['code'])) {
            if (!empty($_GET['error']) && !empty($_GET['error_description'])) {
                // ошибко. ааа
                var_dump('Ошибко');
                var_dump($_GET['error']);
                var_dump($_GET['error_description']);
            } else {
                $this->showAuthDialog();
            }
        } else {
            $this->code = $_GET['code'];
            if ($this->retrieveAccessToken()) {
                header('Location: ' . $this->redirectUri);
            }
        }
    }

    private function isTokenExpire() {
        if (empty($_SESSION[$this->sessionKey]) || empty($_SESSION[$this->sessionKey]['expire']) ||
            time() > $_SESSION[$this->sessionKey]['expire']
        ) {
            return true;
        }
        return false;
    }

    private function showAuthDialog() {
        header('Location: ' . $this->getAuthDialogUrl());
    }

    private function getAuthDialogUrl() {
        return 'https://oauth.vk.com/authorize?' .
                'client_id=' . $this->id . '&' .
                (empty($this->settings) ? '' : ('scope=' . $this->settings . '&')) .
                'display=page&' .
                'redirect_uri=' . urlencode($this->redirectUri) . '&' .
                'response_type=code';
    }

    private function retrieveAccessToken() {
        $Curl = curl_init($this->getAccessTokenUrl());
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
        $Result = curl_exec($Curl);
        curl_close($Curl);

        if ($Result) {
            $Result = json_decode($Result);
            if (!empty($Result->access_token)) {
                $_SESSION[$this->sessionKey] = array(
                    'token' => $Result->access_token,
                    'expire' => (time() + $Result->expires_in),
                );
                return true;
            } else {
                // ошибко. ааа
                var_dump('Ошибко');
                var_dump($Result->error);
                var_dump($Result->error_description);
            }
        }
        return false;
    }

    private function getAccessTokenUrl() {
        return 'https://oauth.vk.com/access_token?' .
                'client_id=' . $this->id . '&' .
                'client_secret=' . $this->key . '&' .
                'code=' . $this->code . '&' .
                'redirect_uri=' . urlencode($this->redirectUri);
    }

    public function getAccessToken() {
        return $this->accessToken;
    }
}
