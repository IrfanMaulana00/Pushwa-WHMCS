<?php

namespace WHMCS\Module\Notification\Pushwa;


use WHMCS\Config\Setting;
use WHMCS\Exception; 
use WHMCS\Module\Notification\DescriptionTrait;
use WHMCS\Module\Contracts\NotificationModuleInterface;
use WHMCS\Notification\Contracts\NotificationInterface;


class Pushwa implements NotificationModuleInterface
{
	use DescriptionTrait;
	
    public function __construct()
    {
        $this->setDisplayName('Pushwa')
            ->setLogoFileName('logo.png');
    }


	public function settings()
    {
        return [
            'waNumber' => [
                'FriendlyName' => 'Your Whatsapp Number',
                'Type' => 'text',
                'Description' => 'Your Whatsapp Number.',
                'Placeholder' => '',
            ],
          'pushwaToken' => [
                'FriendlyName' => 'Pushwa Token',
                'Type' => 'text',
                'Description' => 'Get your token from <a href="https://Pushwa.com" target="_NEW">Pushwa.com</a>.',
                'Placeholder' => '',
            ],
        ];
    }

	
	public function testConnection($settings)
    {
		$waNumber = $settings['waNumber'];
      	$pushwaToken = $settings['pushwaToken'];
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://dash.pushwa.com/api/kirimPesan',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>json_encode([
          	'token' =>   $pushwaToken,
            'target' =>   $waNumber,
            'type' =>   'text',
            'delay' =>   1,
            'message' =>   'Test ping pushwa.com',
          ]),
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

        if (!$response) { 
			throw new Exception('No response received from API');
		}
    }

	public function notificationSettings()
	{
		return [];
	}
	
	public function getDynamicField($fieldName, $settings)
	{
		return [];
	}


	public function sendNotification(NotificationInterface $notification, $moduleSettings, $notificationSettings)
    {
        $pushwaToken = $moduleSettings['pushwaToken'];
		$waNumber = $moduleSettings['waNumber'];
		
		$messageContent = "*". $notification->getTitle() ."*\n\n". $notification->getMessage() ."\n\n[Open »](". $notification->getUrl() .")";
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://dash.pushwa.com/api/kirimPesan',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>json_encode([
          	'token' =>   $pushwaToken,
            'target' =>   $waNumber,
            'type' =>   'text',
            'delay' =>   1,
            'message' =>   $messageContent,
          ]),
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
        if (!$response) { 
			throw new Exception('No response received from API');
		}
    }
}