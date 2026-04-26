<?php

namespace App\Notify;

use App\Notify\NotifyProcess;
use App\Notify\Notifiable;

class Push extends NotifyProcess implements Notifiable{

    /**
    * Device Id of receiver
    *
    * @var array
    */
	public $deviceId;

    public $redirectUrl;

    public $pushImage;


    /**
    * Assign value to properties
    *
    * @return void
    */
	public function __construct(){
		$this->statusField = 'push_status';
		$this->body = 'push_body';
		$this->globalTemplate = 'push_template';
		$this->notifyConfig = 'firebase_config';
	}


    public function redirectForApp($getTemplateName){

        $screens = [

        ];

        foreach($screens as $screen => $array){
            if(in_array($getTemplateName ,$array)){
                return $screen;
            }
        }

        return 'HOME';
    }


    /**
    * Send notification
    *
    * @return void|bool
    */
	public function send(){

        if (!gs('pn')) {
			return false;
		}

        //get message from parent
        $message = $this->getMessage();
        if ($message) {
            try {
                $client = new \Google_Client();

                $credentialsPath = config('firebase.credentials_path');
                $credentialsJsonBase64 = config('firebase.credentials_json_base64');

                if ($credentialsJsonBase64) {
                    $decoded = base64_decode($credentialsJsonBase64, true);
                    if ($decoded === false) {
                        throw new \RuntimeException('Invalid FIREBASE_CREDENTIALS_JSON_BASE64');
                    }
                    $credentialsArray = json_decode($decoded, true);
                    if (!is_array($credentialsArray)) {
                        throw new \RuntimeException('Invalid FIREBASE_CREDENTIALS_JSON_BASE64 JSON');
                    }
                    $client->setAuthConfig($credentialsArray);
                } else {
                    if (!$credentialsPath || !file_exists($credentialsPath)) {
                        throw new \RuntimeException('Firebase credentials file not found');
                    }
                    $client->setAuthConfig($credentialsPath);
                }

                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $client->fetchAccessTokenWithAssertion();
                $token = $client->getAccessToken();
                $access_token = $token['access_token'];
                $headers = [
                    "Authorization: Bearer $access_token",
                    'Content-Type: application/json'
                ];
                $data['notification'] = [
                    'body'=>$message,
                    'title'=>$this->getTitle(),
                    'image'=>$this->pushImage ? asset(getFilePath('push')).'/'.$this->pushImage : null,
                ];

                $data['data'] = [
                    'icon'=>siteFavicon(),
                    'click_action'=>$this->redirectUrl,
                    'app_click_action'=>$this->redirectForApp($this->templateName)
                ];
                foreach ($this->toAddress as $toAddress) {
                    $data['token'] = $toAddress;
                    $payloadData['message'] = $data;
                    $payload = json_encode($payloadData);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.gs('firebase_config')->projectId.'/messages:send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    curl_exec($ch);
                    curl_close($ch);
                }
                $this->createLog('push');
            } catch(\Exception $e){
                $this->createErrorLog($e->getMessage());
                session()->flash('firebase_error',$e->getMessage());
            }
        }

    }



    /**
    * Configure some properties
    *
    * @return void
    */
	public function prevConfiguration(){
		if ($this->user) {
            $this->deviceId = $this->user->deviceTokens()->pluck('token')->toArray();
			$this->receiverName = $this->user->fullname;
		}
		$this->toAddress = $this->deviceId;
	}

    private function getTitle(){
        return $this->replaceTemplateShortCode($this->template->push_title ?? gs('push_title'));
    }
}