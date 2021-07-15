<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','token'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					[
                        'actions' => ['create-vendor','fetch-vendor','vendors','sync-vendor','token'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
	
	
	public function actionCreateVendor($VendorName,$VendorID)
	{
		// return $this->renderContent('We are here');
		$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => env('VENDOR_BASE_URL'),
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>'{ 
	  "F_NAME": "'.$VendorName.'",
	  "F_SUPP_ID": "'.$VendorID.'"
	}
	',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Bearer '.$this->actionToken(),
		'Content-Type: application/json'
	  ),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	//echo $response; exit;
	
	
		$filename = 'log/requests.txt';
		$req_dump = print_r($response, TRUE);
		$fp = fopen($filename, 'a');
		fwrite($fp, $req_dump);
		fclose($fp);
	
	return json_decode($response);

	}
	
	public function actionFetchVendor($VendorName)
	{
		$name = $VendorName;
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'http://128.0.0.6:9090/ada/v_1/vendors/'.$name,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
			'Authorization: Bearer '.$this->actionToken()
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		 //echo $response;
		
		
		return json_decode($response);

	}
	
	// Get Token
	
	public function actionToken()
	{
		$user = env('API_PASSWORD');
		$password = env('API_USER');

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'http://128.0.0.6:9090/authenticate',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{ 
		  "password": "'.$user.'",
		  "username": "'.$password.'"
		}
		',
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$res = json_decode($response);
		
		return $res->token;

	}
	
	
	
	// Get Vendors
	
	public function actionVendors()
	{
		$service = Yii::$app->params['ServiceName']['VendorList'];
		
		$filter = [];
		
		$result = Yii::$app->navHelper->getData($service, $filter);
		
		return $result;
			
	}
	
	
	// Update DMS LINK FOR EACH VENDOR
	
	public function actionSyncVendor()
	{
		$vendors = $this->actionVendors();
		foreach($vendors as $vendor)
		{
			
			if(empty($vendor->Name))
			{
				continue;
			}
			
			
			
			
			$exists = $this->actionFetchVendor($vendor->No);	
			
				
			if(property_exists($exists,'F_SUPP_ID'))	{
					
				 $this->updateLink($exists->F_SUPP_ID); 
				
				sleep(5);
			}else
			{
				//Create Vendor and Update their link
			
				$createVendor = $this->actionCreateVendor($vendor->Name,$vendor->No);
				print('CREATED AFRESH IN DMS'); // FOR NODE LOGS
				return $this->updateLink($createVendor->F_SUPP_ID);
				sleep(5);
			}
			
			
		
		
		}
		
	}
	
	public function updateLink($supplierID)
	{
		$service = Yii::$app->params['ServiceName']['SupplierCard'];
		
		
		$result = Yii::$app->navHelper->findOne($service, '','No',$supplierID);
		//return $result->Key;
		// Get Vendor To Update
		if(is_object($result))
		{
			$dmsLink = 'http://128.0.0.6/ada.web/openQuery.aspx?SERVER_ID=POLICE_SACCO&APP=1&LIB=19&QYNUM=1&QUERYPARAMS=F_SUPP_ID='.$supplierID.'&LANG=ENGLISH';
			
			
			
			$args = [
				'Key' => $result->Key,
				'DMS_Url' => $dmsLink
			];
			
			$updateResult = Yii::$app->navHelper->updateData($service,$args);
			
			
			$message = json_encode($updateResult);
			
			
			$this->log($message);
			print_r($updateResult);// for node js logs
			//return $updateResult;
			if(!is_string($updateResult))
			{
				
				return('JOB DONE.'); 
			}
		}else{
			return('Error: Could not update the vendor DMS LINK.');
		}		
	}
	
	public function log($message) {
		$filename = 'log/requests.txt';
		$fp = fopen($filename, 'a');
		fwrite($fp, $message);
		fclose($fp);
	}


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
	
	public function actionLinkedvendors()
	{
		$service = Yii::$app->params['ServiceName']['VendorList'];
		
		$filter = ['DMS_Url' => '<>" "'];
		
		$result = Yii::$app->navHelper->getData($service, $filter);
		
		$vendor_array = [];
		
		
		foreach($result as $res) {
			if(empty($res->Name))
			{
				continue;
			}
			if(property_exists($res,'DMS_Url') ){
				$vendor_array[] = [
					'No' => $res->No,
					'Name' => $res->Name,
					'DMS_Url' => !empty($res->DMS_Url)?$res->DMS_Url:''
				];
			}
		}
		
		echo 'Vendors with DMS Url: '.count($vendor_array);
		print('<pre>');
		var_dump($result);
		exit;
		
		

		
			
	}
	
	/*For Unprocessed vendors metrics only*/
	
	public function actionUnlinkedvendors()
	{
		$service = Yii::$app->params['ServiceName']['VendorList'];
		
		$filter = ['DMS_Url' => " "];
		$result = Yii::$app->navHelper->getData($service,$filter);
		
		$vendor_array = [];
		
		
		foreach($result as $res) {
			if(!empty($res->Name) && !isset($res->DMS_Url)) //Has a name and doesnt have a  dms url
			{
				$vendor_array[] = [
				'No' => $res->No,
				'Name' => $res->Name,
				'DMS_Url' => !empty($res->DMS_Url)?$res->DMS_Url:''
				];
			}
			
		}
		echo 'Vendors without DMS Url: '.count($vendor_array);
		print('<pre>');
		var_dump($vendor_array);
		exit;	
			
	}
	
	
	/*For Capturing unprocessed vendors - for integration purposes only, similar to metrics one by design and intention */
	
	
	public function actionUnprocessedvendors()
	{
		$service = Yii::$app->params['ServiceName']['VendorList'];
		
		$filter = ['DMS_Url' => " "];
		$result = Yii::$app->navHelper->getData($service,$filter);
		
		$vendor_array = [];
		
		
		foreach($result as $res) {
			if(!empty($res->Name) && !isset($res->DMS_Url)) //Has a name and doesnt have a  dms url
			{
				$vendor_array[] = [
				'No' => $res->No,
				'Name' => $res->Name,
				'DMS_Url' => !empty($res->DMS_Url)?$res->DMS_Url:''
				];
			}
			
		}
		
		return $vendor_array;
		
			
	}
	
	
	// Specifi action for only creating new vendors - No updates
	
	public function actionNewVendor()
	{
		$vendors = $this->actionUnprocessedvendors();
		
		
		if(is_array($vendors))
		{
			foreach($vendors as $vendor)
				{
					
						//Create Vendor and Update their link
						$createVendor = $this->actionCreateVendor($vendor['Name'],$vendor['No']);
						if($createVendor->success)
						{
							$result =  $this->updateLink($createVendor->F_SUPP_ID);
						}else{
							$result = $this->updateLink($vendor['No']);
						}
						$this->logger($result);
						exit;
					
				}
		}
		return Json_encode(['State' => 'No New Vendors to Synchronize.']);
		
		
	}
	
	private function logger($message)
	{
		$filename = 'log/ada.txt';
		$req_dump = print_r($message, TRUE);
		$fp = fopen($filename, 'a');
		fwrite($fp, $req_dump);
		fclose($fp);
	}

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
