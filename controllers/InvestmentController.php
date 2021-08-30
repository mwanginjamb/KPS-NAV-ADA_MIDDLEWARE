<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class InvestmentController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout','token'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					[
                        'actions' => ['index','investments','fetch-vendor','vendors','sync-vendor','token'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
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
		
		$token = $this->token();
        return $this->renderContent($token);
    }

	// Get Investments

	public function actionInvestments()
	{
		$service = Yii::$app->params['INV_ServiceName']['FosaTransactions'];
		
		$filter = [
			'Int_Direction' => 'Outgoing',
			'Sent' => "Pending",
			'Posted' => false
		];
		$result = Yii::$app->investment->getData($service, $filter);
		
		/*print '<pre>';
		print_r($result);
		exit;*/
		
		return $result;
	}
	
	
	
	// Get Token
	
	public function token()
	{
			  $curl = curl_init();

			  curl_setopt_array($curl, array(
			  CURLOPT_URL => env('PROFT_TEST_BASEURL'),
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
				<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				  <soap:Body>
					<CI3499V_GetAuthorized xmlns="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
					  <import />
					  <executionParameters>
						<ChannelId>'.env('CHANNEL_ID').'</ChannelId>
						<Password>'.env('PROF_PASSWORD').'</Password>
						<ExtUniqueUserId>'.env('NAV_USER').'</ExtUniqueUserId>
					  </executionParameters>
					</CI3499V_GetAuthorized>
				  </soap:Body>
				</soap:Envelope>',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: text/xml'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			//echo $response;
			
			if(!empty($response))
			{
				$xml_object = simplexml_load_string($response); 

			// register your used namespace prefixes
			$xml_object->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance'); 
			$xml_object->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope'); 
			$nodes = $xml_object->xpath("/soap:Envelope/soap:Body");

			return ($nodes[0]->CI3499V_GetAuthorizedResponse->CI3499V_GetAuthorizedResult->UniqueId);
			}
			
			
			

	}
	
	
	
	public function actionPostInvestment(object $record )
	{
		


		$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL =>  env('PROFT_TEST_BASEURL'),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:prof="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
   <soapenv:Header/>
   <soapenv:Body>
      <prof:FEXS01_FundsTransferWithExchange>
         <!--Optional:-->
 <prof:import>
				<prof:Command>INSERT</prof:Command>
				<prof:InAuthorIefSuppliedFlag>1</prof:InAuthorIefSuppliedFlag>
				<prof:InBlackListIefSuppliedExchangePurchaseDocNo/>
				<prof:InBoughtAmountIefSuppliedCheckDigit>0</prof:InBoughtAmountIefSuppliedCheckDigit>
				<prof:InBoughtAmountIefSuppliedPayableAmount>'.$record->Amount.'</prof:InBoughtAmountIefSuppliedPayableAmount>
				<prof:InBoughtProfitsAccountNumber>'.$record->Profits_Debit_Account.'</prof:InBoughtProfitsAccountNumber>
				<prof:InBoughtProfitsAccountCd>0</prof:InBoughtProfitsAccountCd>
				<prof:InBoughtProfitsAccountPrftSystem>3</prof:InBoughtProfitsAccountPrftSystem>
				<prof:InBoughtDepositAccountDesignation/>
				<prof:InBoughtDepositAccountEntryStatus/>
				<prof:InBoughtIbanWorkSetChar37/>
				<prof:InBoughtJustificIdJustific>34001</prof:InBoughtJustificIdJustific>
				<prof:InBoughtPrftTransactionIdTransact>3191</prof:InBoughtPrftTransactionIdTransact>
				<prof:InBoughtRepCustomerCDigit>0</prof:InBoughtRepCustomerCDigit>
				<prof:InBoughtRepCustomerCustId>0</prof:InBoughtRepCustomerCustId>
				<prof:InBoughtValueDaysIefSuppliedValueDays>0</prof:InBoughtValueDaysIefSuppliedValueDays>
				<prof:InBoughtValueWorkDatesProductionDate>0001-01-01T00:00:00</prof:InBoughtValueWorkDatesProductionDate>
				<prof:InChargesAccountIefSuppliedFlag>1</prof:InChargesAccountIefSuppliedFlag>
				<prof:InChargesDiscountIefSuppliedGenPercentage>0</prof:InChargesDiscountIefSuppliedGenPercentage>
				<prof:InChequeBookItemIssueDate>0001-01-01T00:00:00</prof:InChequeBookItemIssueDate>
				<prof:InChequeBookItemItemSerialNumber>0</prof:InChequeBookItemItemSerialNumber>
				<prof:InCommentsGenericDetailDescription>REF_NUMBER</prof:InCommentsGenericDetailDescription>
				<prof:InCommentsGenericDetailSerialNum>0</prof:InCommentsGenericDetailSerialNum>
				<prof:InCommissionsDiscountIefSuppliedGenPercentage>0</prof:InCommissionsDiscountIefSuppliedGenPercentage>
				<prof:InCreditDepTrxRecordingIComments>AGENCY WITHDRAWAL</prof:InCreditDepTrxRecordingIComments>
				<prof:InCustAdditionalCustomerTelephone1/>
				<prof:InCustAddressAddress1/>
				<prof:InCustAddressAddress2/>
				<prof:InCustAddressCity/>
				<prof:InCustAddressZipCode/>
				<prof:InCustCountryGenericDetailDescription/>
				<prof:InCustCountryGenericDetailSerialNum>0</prof:InCustCountryGenericDetailSerialNum>
				<prof:InCustListSetDescription/>
				<prof:InCustNationalityGenericDetailDescription/>
				<prof:InCustNationalityGenericDetailParameterType/>
				<prof:InCustNationalityGenericDetailSerialNum>0</prof:InCustNationalityGenericDetailSerialNum>
				<prof:InCustOtherAfmAfmNo/>
				<prof:InCustomerCDigit>0</prof:InCustomerCDigit>
				<prof:InCustomerCustId>47267</prof:InCustomerCustId>
				<prof:InDealerPenaltyUsrCode/>
				<prof:InDealerSpecialRateDealerRefNo/>
				<prof:InDealerUsrCode/>
				<prof:InDebitDepTrxRecordingIComments>AGENCY WITHDRAWAL</prof:InDebitDepTrxRecordingIComments>
				<prof:InDepositCDigitIefSuppliedCheckDigit>0</prof:InDepositCDigitIefSuppliedCheckDigit>
				<prof:InFwdSwapContractsContractDate>0001-01-01T00:00:00</prof:InFwdSwapContractsContractDate>
				<prof:InFwdSwapContractsCurrencyRate>0</prof:InFwdSwapContractsCurrencyRate>
				<prof:InFwdSwapContractsDealerRefNo/>
				<prof:InFwdSwapContractsEntryComments/>
				<prof:InFwdSwapContractsEntryStatus/>
				<prof:InFwdSwapContractsExecDate>0001-01-01T00:00:00</prof:InFwdSwapContractsExecDate>
				<prof:InFwdSwapContractsMaturityDate>0001-01-01T00:00:00</prof:InFwdSwapContractsMaturityDate>
				<prof:InFwdSwapContractsNotificationDate>0001-01-01T00:00:00</prof:InFwdSwapContractsNotificationDate>
				<prof:InFwdSwapContractsOrgSourceAmount>0</prof:InFwdSwapContractsOrgSourceAmount>
				<prof:InFwdSwapContractsOrgTargetAmount>0</prof:InFwdSwapContractsOrgTargetAmount>
				<prof:InFwdSwapContractsReferenceNo>0</prof:InFwdSwapContractsReferenceNo>
				<prof:InFwdSwapContractsSourceUtilBal>0</prof:InFwdSwapContractsSourceUtilBal>
				<prof:InFwdSwapContractsStartDate>0001-01-01T00:00:00</prof:InFwdSwapContractsStartDate>
				<prof:InFwdSwapContractsTargetUtilBal>0</prof:InFwdSwapContractsTargetUtilBal>
				<prof:InFwdSwapContractsWayOfUtilization/>
				<prof:InGenericIdIefSuppliedIdentificationType/>
				<prof:InGenericIdIefSuppliedIdentityPassportNo/>
				<prof:InGenericIdIefSuppliedIssueAuthority/>
				<prof:InGrpParametersInGrmBankParametersMaxAmntRateTbl>0</prof:InGrpParametersInGrmBankParametersMaxAmntRateTbl>
				<prof:InGrpParametersInGrmGenericDetailSerialNum>0</prof:InGrpParametersInGrmGenericDetailSerialNum>
				<prof:InGrpParametersInGrmTerminalTerminalNumber>10.1.1.18</prof:InGrpParametersInGrmTerminalTerminalNumber>
				<prof:InGrpParametersInGrmTrxCountTrxCounter>0</prof:InGrpParametersInGrmTrxCountTrxCounter>
				<prof:InGrpParametersInGrmWorkDaysWorkDatesProductionDate>0001-01-01T00:00:00</prof:InGrpParametersInGrmWorkDaysWorkDatesProductionDate>
				<prof:InIdentCountryGenericDetailDescription/>
				<prof:InIdentCountryGenericDetailSerialNum>0</prof:InIdentCountryGenericDetailSerialNum>
				<prof:InJustificIdJustific>9108</prof:InJustificIdJustific>
				<prof:InOtherIdIdNo/>
				<prof:InPenaltyDealerSpecialRateDealerRefNo/>
				<prof:InPostIefSuppliedFlag>Y</prof:InPostIefSuppliedFlag>
				<prof:InPrftTransactionIdTransact>11041</prof:InPrftTransactionIdTransact>
				<prof:InProductIdProduct>9102</prof:InProductIdProduct>
				<prof:InResidentIefSuppliedFlag/>
				<prof:InSoldAmountIefSuppliedPayableAmount>0</prof:InSoldAmountIefSuppliedPayableAmount>
				<prof:InSoldAvailabilityDaysIefSuppliedValueDays>0</prof:InSoldAvailabilityDaysIefSuppliedValueDays>
				<prof:InSoldAvailabilityWorkDatesProductionDate>2020-06-03T00:00:00</prof:InSoldAvailabilityWorkDatesProductionDate>
				<prof:InSoldProfitsAccountNumber>'.$record->Integration_Account.'</prof:InSoldProfitsAccountNumber>
				<prof:InSoldProfitsAccountCd>0</prof:InSoldProfitsAccountCd>
				<prof:InSoldProfitsAccountPrftSystem>3</prof:InSoldProfitsAccountPrftSystem>
				<prof:InSoldDepositAccountDesignation/>
				<prof:InSoldDepositAccountEntryStatus/>
				<prof:InSoldIbanWorkSetChar37/>
				<prof:InSoldJustificIdJustific>33100</prof:InSoldJustificIdJustific>
				<prof:InSoldPrftTransactionIdTransact>3181</prof:InSoldPrftTransactionIdTransact>
				<prof:InSoldRepCustomerCDigit>0</prof:InSoldRepCustomerCDigit>
				<prof:InSoldRepCustomerCustId>0</prof:InSoldRepCustomerCustId>
				<prof:InSoldValueDaysIefSuppliedValueDays>0</prof:InSoldValueDaysIefSuppliedValueDays>
				<prof:InSoldValueWorkDatesProductionDate>2020-06-03T00:00:00</prof:InSoldValueWorkDatesProductionDate>
				<prof:InSpecialRateTableIefSuppliedFlag/>
				<prof:InTrxFxFtRecordingSourceTrnType/>
				<prof:InTrxFxFtRecordingTargetTrnType/>
				<prof:InUseWayIefSuppliedFlag/>
				<prof:InGrpAuth>
					<prof:FEXS01InGrpAuthItem>
						<prof:InGrpAuthInGrmTeamInformationSuper1Code/>
						<prof:InGrpAuthInGrmTeamInformationSuper2Code/>
						<prof:InGrpAuthInGrmTeamInformationTransactionId>0</prof:InGrpAuthInGrmTeamInformationTransactionId>
					</prof:FEXS01InGrpAuthItem>
					<prof:FEXS01InGrpAuthItem>
						<prof:InGrpAuthInGrmTeamInformationSuper1Code/>
						<prof:InGrpAuthInGrmTeamInformationSuper2Code/>
						<prof:InGrpAuthInGrmTeamInformationTransactionId>0</prof:InGrpAuthInGrmTeamInformationTransactionId>
					</prof:FEXS01InGrpAuthItem>
				</prof:InGrpAuth>
				<prof:InGrpChargesRecording>
					<prof:FEXS01InGrpChargesRecordingItem>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargeCode>0</prof:InGrpChargesRecordingInGrmChargesRecordingChargeCode>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargeType/>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargedAmn>0</prof:InGrpChargesRecordingInGrmChargesRecordingChargedAmn>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargesCurrId>0</prof:InGrpChargesRecordingInGrmChargesRecordingChargesCurrId>
						<prof:InGrpChargesRecordingInGrmChargesRecordingDbCrFlg/>
						<prof:InGrpChargesRecordingInGrmChargesRecordingDiscountedAmn>0</prof:InGrpChargesRecordingInGrmChargesRecordingDiscountedAmn>
					</prof:FEXS01InGrpChargesRecordingItem>
					<prof:FEXS01InGrpChargesRecordingItem>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargeCode>0</prof:InGrpChargesRecordingInGrmChargesRecordingChargeCode>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargeType/>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargedAmn>0</prof:InGrpChargesRecordingInGrmChargesRecordingChargedAmn>
						<prof:InGrpChargesRecordingInGrmChargesRecordingChargesCurrId>0</prof:InGrpChargesRecordingInGrmChargesRecordingChargesCurrId>
						<prof:InGrpChargesRecordingInGrmChargesRecordingDbCrFlg/>
						<prof:InGrpChargesRecordingInGrmChargesRecordingDiscountedAmn>0</prof:InGrpChargesRecordingInGrmChargesRecordingDiscountedAmn>
					</prof:FEXS01InGrpChargesRecordingItem>
				</prof:InGrpChargesRecording>
				<prof:InSoldCurrencyIdCurrency>0</prof:InSoldCurrencyIdCurrency>
				<prof:InBoughtCurrencyIdCurrency>0</prof:InBoughtCurrencyIdCurrency>
				<prof:InChkFlagIefSuppliedFxFlag/>
			</prof:import>

         <!--Optional:-->
         <prof:executionParameters>
            <prof:ChannelId>'.env('CHANNEL_ID').'</prof:ChannelId>
            <!--Optional:-->
            <prof:Password>'.env('PROF_PASSWORD').'</prof:Password>
            <!--Optional:-->
            <prof:UniqueId>'.$this->token().'</prof:UniqueId>
            <!--Optional:-->
            <prof:CultureName>en</prof:CultureName>
            <prof:ForcastFlag>false</prof:ForcastFlag>
            <!--Optional:-->
            <prof:ReferenceKey>'.time().'</prof:ReferenceKey>
            <!--Optional:-->
            <prof:SotfOtp></prof:SotfOtp>
            <!--Optional:-->
            <prof:BranchCode></prof:BranchCode>
            <!--Optional:-->
            <prof:ExtUniqueUserId>'.env('NAV_USER').'</prof:ExtUniqueUserId>
            <!--Optional:-->
            <prof:ExtDeviceAuthCode></prof:ExtDeviceAuthCode>
         </prof:executionParameters>
      </prof:FEXS01_FundsTransferWithExchange>
   </soapenv:Body>
</soapenv:Envelope>',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: text/xml'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;


				if(!empty($response))
				{
					$xml_object = simplexml_load_string($response); 
					// register your used namespace prefixes
					$xml_object->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance'); 
					$xml_object->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope'); 
					$nodes = $xml_object->xpath("/soap:Envelope/soap:Body");
					// return ($nodes[0]->G0501V_GlAccountValidationResponse->G0501V_GlAccountValidationResult->Result->Message);
					return json_encode($nodes[0]->FEXS01_FundsTransferWithExchangeResponse->FEXS01_FundsTransferWithExchangeResult);
				}
		

	
	}
	
	
	
	
	
	
	public function actionSyncInvestment()
	{
	
		$service = Yii::$app->params['INV_ServiceName']['FosaTransactions'];
		$ImprestRecords = $this->actionInvestments();
		/*print '<pre>';
		print_r($ImprestRecords);
		exit;*/
		
		if(is_array($ImprestRecords)) {
				foreach($ImprestRecords as $account) {
				
							
				
					
					$result = json_decode($this->actionPostInvestment($account));
					/*print '<pre>';
					print_r($result);*/
					
					
					$this->imprestLogger($result);
					
					if($result->Result->Type == 'Success')
					{
						// Update Imprest Transaction on ERP
						$params = [
							'Key' => $account->Key,
							'TrxDate' => $this->processDate($result->OutDuplicateFxFtRecordingTrxDate) ,
							'TrxSn' => $result->OutDuplicateFxFtRecordingTrxSn ,
							'TrxUnit' => $result->OutDuplicateFxFtRecordingTrxUnit ,
							'TrxUsr' => $result->OutDuplicateFxFtRecordingTrxUsr,
							'Sent' => 'Sent',
							'Posted' => true, 
							
						];
						
						$update = Yii::$app->investment->updateData($service, $params);
						print '<pre>';
						print_r($update);
						$this->imprestLogger($update);
					}elseif($result->Result->Type == 'Error')
					{
						
						// Update Imprest Transaction on ERP
						$params = [
							'Key' => $account->Key,
							'TrxDate' => $this->processDate($result->Tun->TrxDate) ,
							'TrxSn' => $result->Tun->TunInternalSn ,
							'TrxUnit' => $result->Tun->TrxUnit ,
							'TrxUsr' => $result->Tun->TrxUser,
							'Sent' => 'Failed', 
							
						];
						
						$update = Yii::$app->investment->updateData($service, $params);
						print '<pre>';
						print_r($update);
						$this->imprestLogger($update);
					}				
					
					exit;	
				}
		}
		
		return Json_encode(['State' => 'No Investment transactions to synchronize.']);
			
	}
	
	
	// Get Reversals
	
	public function actionGetReversals()
	{
		$service = Yii::$app->params['ServiceName']['Imprest_Profits'];
		
		$filter = [
			'Reversal' => true,
			'Reversed' => "Pending"
		];
		$result = Yii::$app->navHelper->getData($service,$filter);
		
		/*print '<pre>';
		print_r($result);
		exit;*/
		
		return $result;
	}
	
	
	// Sync and Process Reversals
	
		public function actionSyncReversal()
	{
		$service = Yii::$app->params['ServiceName']['Imprest_Profits'];
		$ImprestRecords = $this->actionGetReversals();
		/*print '<pre>';
		print_r($ImprestRecords);
		exit;*/
		
		if(is_array($ImprestRecords)) {
				foreach($ImprestRecords as $account) {
					
					$account->Posting_Desricption = property_exists($account,'Posting_Desricption')?$account->Posting_Desricption:'';
				
					$result = json_decode($this->actionReverse($account));
					
					/*print '<pre>';
					print_r($result);
					exit;*/
					
					
					$this->reversalLogger($result);
					
					if($result)
					{
						if($result->Result->Type == 'Success')
							{
								// Update Imprest Transaction on ERP
								$params = [
									'Key' => $account->Key,
									'Reversed' => 'Sent'
									
								];
								
								$update = Yii::$app->navHelper->updateData($service, $params);
								print '<pre>';
								print_r($update);
								$this->reversalLogger($update);
							}else
							{
								
								// Update Imprest Transaction on ERP
								$params = [
									'Key' => $account->Key,
									'Reversed' => 'Failed', 
									
								];
								
								$update = Yii::$app->navHelper->updateData($service, $params);
								print '<pre>';
								print_r($update);
								$this->reversalLogger($update);
							}
					}else{
						
						$params = [
									'Key' => $account->Key,
									'Reversed' => 'Failed', 
									
								];
								
						$account = Yii::$app->navHelper->updateData($service, $params);
						
						print '<pre>';
						print_r($result);
						
						
						$logData = Json_encode($result);
						
						$this->reversalLogger($logData);
					}
									
					
					exit;	
				}
		}
		$this->reversalLogger(Json_encode(['State' => 'No Reversal transactions to synchronize.']));
		return Json_encode(['State' => 'No Reversal transactions to synchronize.']);
			
	}
	
	// Post Reversal
	
	public function actionPostReversal(object $record)
	{
		

							

				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'http://10.1.4.12/iProfits2.GatewayService/ProfitsExtGateway.asmx',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:prof="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
					<soapenv:Header/>
					<soapenv:Body>
						<prof:ADDS03_CancelAdditionalTransactions>
							<!--Optional:-->
							<prof:import>
								<!--Optional:-->
								<prof:Command>INSERT</prof:Command>
								<!--Optional:-->
								<prof:InCommandIefSuppliedCommand/>
								<!--Optional:-->
								<prof:InFxFtRecordingComments />
								<prof:InFxFtRecordingTrxDate>'.$this->processProfitsDate($record->TrxDate).'</prof:InFxFtRecordingTrxDate>
								<prof:InFxFtRecordingTrxSn>'.$record->TrxSn.'</prof:InFxFtRecordingTrxSn>
								<prof:InFxFtRecordingTrxUnit>'.$record->TrxUnit.'</prof:InFxFtRecordingTrxUnit>
								<!--Optional:-->
								<prof:InFxFtRecordingTrxUsr>'.$record->TrxUsr.'</prof:InFxFtRecordingTrxUsr>
								<prof:InPrftTransactionIdTransact>12511</prof:InPrftTransactionIdTransact>
								<!--Optional:-->
								<prof:InTerminalTerminalNumber></prof:InTerminalTerminalNumber>
							</prof:import>
							<!--Optional:-->
							<prof:executionParameters>
								<prof:ChannelId>'.env('CHANNEL_ID').'</prof:ChannelId>
								<!--Optional:-->
								<prof:Password>'.env('PROF_PASSWORD').'</prof:Password>
								<!--Optional:-->
								<prof:UniqueId>'.$this->token().'</prof:UniqueId>
								<!--Optional:-->
								<prof:CultureName>en</prof:CultureName>
								<prof:ForcastFlag>false</prof:ForcastFlag>
								<!--Optional:-->
								<prof:ReferenceKey>J888888BaX8$*8*8W</prof:ReferenceKey>
								<!--Optional:-->
								<prof:SotfOtp/>
								<!--Optional:-->
								<prof:BranchCode/>
								<!--Optional:-->
								<prof:ExtUniqueUserId>'.env('NAV_USER').'</prof:ExtUniqueUserId>
								<!--Optional:-->
								<prof:ExtDeviceAuthCode/>
							</prof:executionParameters>
						</prof:ADDS03_CancelAdditionalTransactions>
					</soapenv:Body>
				</soapenv:Envelope>',
				  CURLOPT_HTTPHEADER => array(
					'Content-Type: text/xml'
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				echo $response;


				
				
				if(!empty($response))
				{
					$xml_object = simplexml_load_string($response); 

					// register your used namespace prefixes
					$xml_object->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance'); 
					$xml_object->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope'); 
					$nodes = $xml_object->xpath("/soap:Envelope/soap:Body");
					// $nodes[0]->ADDS03_CancelAdditionalTransactionsResponse->ADDS03_CancelAdditionalTransactionsResult
			return json_encode($nodes[0]->ADDS03_CancelAdditionalTransactionsResponse->ADDS03_CancelAdditionalTransactionsResult);
				}

	
	}
	
	
	/*Reversal function V2*/
	
	public function actionReverse(object $record)
	{
		

							

				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => env('PROFT_TEST_BASEURL'),
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:prof="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
								<soapenv:Header/>
								<soapenv:Body>
									<prof:ADDS03_CancelAdditionalTransactions>
										<!--Optional:-->
										<prof:import>
											<!--Optional:-->
											<prof:Command>INSERT</prof:Command>
											<!--Optional:-->
											<prof:InCommandIefSuppliedCommand/>
											<!--Optional:-->
											<prof:InFxFtRecordingComments>'.$record->Posting_Desricption.'</prof:InFxFtRecordingComments>
											<prof:InFxFtRecordingTrxDate>'.$this->processProfitsDate($record->TrxDate).'</prof:InFxFtRecordingTrxDate>
											<prof:InFxFtRecordingTrxSn>'.$record->TrxSn.'</prof:InFxFtRecordingTrxSn>
											<prof:InFxFtRecordingTrxUnit>'.$record->TrxUnit.'</prof:InFxFtRecordingTrxUnit>
											<!--Optional:-->
											<prof:InFxFtRecordingTrxUsr>'.$record->TrxUsr.'</prof:InFxFtRecordingTrxUsr>
											<prof:InPrftTransactionIdTransact>12511</prof:InPrftTransactionIdTransact>
											<!--Optional:-->
											<prof:InTerminalTerminalNumber></prof:InTerminalTerminalNumber>
										</prof:import>
										<!--Optional:-->
										<prof:executionParameters>
											<prof:ChannelId>'.env('CHANNEL_ID').'</prof:ChannelId>
											<!--Optional:-->
											<prof:Password>'.env('PROF_PASSWORD').'</prof:Password>
											<!--Optional:-->
											<prof:UniqueId>'.$this->token().'</prof:UniqueId>
											<!--Optional:-->
											<prof:CultureName>en</prof:CultureName>
											<prof:ForcastFlag>false</prof:ForcastFlag>
											<!--Optional:-->
											<prof:ReferenceKey>J8888888BaX8$*8*8W</prof:ReferenceKey>
											<!--Optional:-->
											<prof:SotfOtp/>
											<!--Optional:-->
											<prof:BranchCode/>
											<!--Optional:-->
											<prof:ExtUniqueUserId>'.env('NAV_USER').'</prof:ExtUniqueUserId>
											<!--Optional:-->
											<prof:ExtDeviceAuthCode/>
										</prof:executionParameters>
									</prof:ADDS03_CancelAdditionalTransactions>
								</soapenv:Body>
							</soapenv:Envelope>',
				  CURLOPT_HTTPHEADER => array(
					'Content-Type: text/xml'
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				echo $response;


				
				
				if(!empty($response))
				{
					$xml_object = simplexml_load_string($response); 

					// register your used namespace prefixes
					$xml_object->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance'); 
					$xml_object->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope'); 
					$nodes = $xml_object->xpath("/soap:Envelope/soap:Body");
					// $nodes[0]->ADDS03_CancelAdditionalTransactionsResponse->ADDS03_CancelAdditionalTransactionsResult
					return json_encode($nodes[0]->ADDS03_CancelAdditionalTransactionsResponse->ADDS03_CancelAdditionalTransactionsResult);
				}
				
				

	
	}
	
	
	private function logger($message)
	{
		$filename = 'log/glaccounts.txt';
		$req_dump = print_r($message, TRUE);
		$fp = fopen($filename, 'a');
		fwrite($fp, $req_dump);
		fclose($fp);
	}
	
	private function imprestLogger($message)
	{
		$filename = 'log/Investment.txt';
		$req_dump = print_r($message, TRUE);
		$fp = fopen($filename, 'a');
		fwrite($fp, $req_dump);
		fclose($fp);
	}
	
	private function reversalLogger($message)
	{
		$filename = 'log/reversal.txt';
		$req_dump = print_r($message, TRUE);
		$fp = fopen($filename, 'a');
		fwrite($fp, $req_dump);
		fclose($fp);
	}
	
	/*Creates a Nav compatible date*/
	private function processDate($date)
	{
		list($date,$time) = explode('T',$date);
		return date('Y-m-d',strtotime($date));
	}
	
	/* Create a Profits compatible timestamp for reversals*/
	private function processProfitsDate($date)
	{
		//2021-05-31T00:00:00
		return $date.'T00:00:00';
		
	}
	
	
	
	
	
	
	
}
