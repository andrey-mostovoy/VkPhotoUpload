<?php
class VkPhotoUpload {
    public function __construct() {

    }

    public function setFilesToUpload() {

    }

    public function upload($token) {
        $this->getUploadServerUrl($token);
    }

    private function getUploadServerUrl($token) {
//76040944_168845309
//12834623_134327956
        $url = 'https://api.vk.com/method/photos.getUploadServer?' .
                'aid=168845309' . '&' .
                'access_token=' . $token;
//        http://vk.com/album76040944_168845309
        $Result = $this->request($url);
        echo '<pre>';
        print_r($Result);
        echo '</pre>';
    }

    private function request($url) {
        $Curl = curl_init($url);
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
        $Result = curl_exec($Curl);
        curl_close($Curl);

        if ($Result) {
            $Result = json_decode($Result);
            return $Result;
        }
        return false;
    }
}
