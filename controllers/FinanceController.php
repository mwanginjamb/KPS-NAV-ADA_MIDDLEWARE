<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class FinanceController extends Controller
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
		
		$token = $this->token();
        return $this->renderContent($token);
    }
	
	
	
	// Get Token
	
	public function token()
	{

			$user = env('NAV_USER').time();
			Yii::$app->session->set('USER', $user);

			  $curl = curl_init();

			  curl_setopt_array($curl, array(
			  CURLOPT_URL => env('PROFITS_LIVE_BASEURL'),
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_SSL_VERIFYPEER => false, // DON'T VERIFY SSL CERTIFICATE
			  CURLOPT_SSL_VERIFYHOST => 0, // DON'T VERIFY HOST NAME
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
				<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				  <soap:Body>
					<CI3499V_GetAuthorized xmlns="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
					  <import />
					  <executionParameters>
						<ChannelId>'.env('CHANNEL_ID').'</ChannelId>
						<Password>'.env('PROF_PASSWORD').'</Password>
						<ExtUniqueUserId>'.Yii::$app->session->get('USER').'</ExtUniqueUserId>
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
	
	
	// Post GL A/c to Profilts
	
	public function Postgl($account)
	{
		

			$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => env('PROFITS_LIVE_BASEURL'),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYPEER => false, // DON'T VERIFY SSL CERTIFICATE
  CURLOPT_SSL_VERIFYHOST => 0, // DON'T VERIFY HOST NAME
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:prof="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
   <soapenv:Header/>
   <soapenv:Body>
      <prof:G0501V_GlAccountValidation>
         <!--Optional:-->
         <prof:import>
            <!--Optional:-->
            <prof:Command>CREATE</prof:Command>
            <!--Optional:-->
            <prof:InAssLiabChangeIefSuppliedChar1></prof:InAssLiabChangeIefSuppliedChar1>
            <!--Optional:-->
            <prof:InAuthorGrantedIefSuppliedFlag>0</prof:InAuthorGrantedIefSuppliedFlag>
            <!--Optional:-->
            <prof:InBalTypeGenericDetailDescription>MIXED</prof:InBalTypeGenericDetailDescription>
            <!--Optional:-->
            <prof:InBalTypeGenericDetailParameterType>GLBAL</prof:InBalTypeGenericDetailParameterType>
            <prof:InBalTypeGenericDetailSerialNum>4</prof:InBalTypeGenericDetailSerialNum>
            <prof:InBalshTypeGenericDetailSerialNum>0</prof:InBalshTypeGenericDetailSerialNum>
            <!--Optional:-->
            <prof:InBistaGenericDetailDescription></prof:InBistaGenericDetailDescription>
            <prof:InBistaGenericDetailSerialNum>0</prof:InBistaGenericDetailSerialNum>
            <!--Optional:-->
            <prof:InCategUnitGenericDetailDescription>ALL UNITS</prof:InCategUnitGenericDetailDescription>
            <prof:InCategUnitGenericDetailSerialNum>1</prof:InCategUnitGenericDetailSerialNum>
            <!--Optional:-->
            <prof:InGlgAccountAccountId>'.$account->No.'</prof:InGlgAccountAccountId>
            <!--Optional:-->
            <prof:InGlgAccountBopFlg>0</prof:InGlgAccountBopFlg>
            <prof:InGlgAccountBopGroupAccount>0</prof:InGlgAccountBopGroupAccount>
            <!--Optional:-->
            <prof:InGlgAccountCentralFlag>2</prof:InGlgAccountCentralFlag>
            <!--Optional:-->
            <prof:InGlgAccountDbCrBalFlag>4</prof:InGlgAccountDbCrBalFlag>
            <prof:InGlgAccountDeactivationDate>0001-01-01T00:00:00</prof:InGlgAccountDeactivationDate>
            <!--Optional:-->
            <prof:InGlgAccountDescr>'.$account->Name.'</prof:InGlgAccountDescr>
            <!--Optional:-->
            <prof:InGlgAccountDsubTrnFlag>1</prof:InGlgAccountDsubTrnFlag>
            <!--Optional:-->
            <prof:InGlgAccountEvalFlag>2</prof:InGlgAccountEvalFlag>
            <!--Optional:-->
            <prof:InGlgAccountFcconvFlag>0</prof:InGlgAccountFcconvFlag>
            <prof:InGlgAccountLastUpdDate>0001-01-01T00:00:00</prof:InGlgAccountLastUpdDate>
            <!--Optional:-->
            <prof:InGlgAccountLevel></prof:InGlgAccountLevel>
            <!--Optional:-->
            <prof:InGlgAccountMandAdditionalInfo>0</prof:InGlgAccountMandAdditionalInfo>
            <!--Optional:-->
            <prof:InGlgAccountMandCustInfo>0</prof:InGlgAccountMandCustInfo>
            <prof:InGlgAccountModifyDate>0001-01-01T00:00:00</prof:InGlgAccountModifyDate>
            <prof:InGlgAccountOpenDate>0001-01-01T00:00:00</prof:InGlgAccountOpenDate>
            <!--Optional:-->
            <prof:InGlgAccountOptionalFlag>0</prof:InGlgAccountOptionalFlag>
            <!--Optional:-->
            <prof:InGlgAccountPositionFlag>0</prof:InGlgAccountPositionFlag>
            <!--Optional:-->
            <prof:InGlgAccountRealTimeFlag>0</prof:InGlgAccountRealTimeFlag>
            <!--Optional:-->
            <prof:InGlgAccountReconFlag>0</prof:InGlgAccountReconFlag>
            <prof:InGlgAccountReconRunDt>0001-01-01T00:00:00</prof:InGlgAccountReconRunDt>
            <prof:InGlgAccountReconStartDt>0001-01-01T00:00:00</prof:InGlgAccountReconStartDt>
            <prof:InGlgAccountSecLevel>0</prof:InGlgAccountSecLevel>
            <!--Optional:-->
            <prof:InGlgAccountShortDescr>'.$account->Account_Category.'</prof:InGlgAccountShortDescr>
            <!--Optional:-->
            <prof:InGlgAccountState>1</prof:InGlgAccountState>
            <!--Optional:-->
            <prof:InGlgAccountStatus></prof:InGlgAccountStatus>
            <!--Optional:-->
            <prof:InGlgAccountSubsConsFlag>1</prof:InGlgAccountSubsConsFlag>
            <prof:InGlgAccountSubsidCount>0</prof:InGlgAccountSubsidCount>
            <prof:InGlgAccountTimestmp>0001-01-01T00:00:00</prof:InGlgAccountTimestmp>
            <!--Optional:-->
            <prof:InGlgAccountUnitAppliedFor></prof:InGlgAccountUnitAppliedFor>
            <!--Optional:-->
            <prof:InGlgAccountUnitRealTime>0</prof:InGlgAccountUnitRealTime>
            <!--Optional:-->
            <prof:InGlgAccountUpdateWayInd>3</prof:InGlgAccountUpdateWayInd>
            <!--Optional:-->
            <prof:InGlgAccountUpdatedFlag>0</prof:InGlgAccountUpdatedFlag>
            <!--Optional:-->
            <prof:InGlgAccountValeurDateFlag>1</prof:InGlgAccountValeurDateFlag>
            <!--Optional:-->
            <prof:InGlgAccountValeurFlg>0</prof:InGlgAccountValeurFlg>
            <!--Optional:-->
            <prof:InGlgAccountDeleteGlgAccountAccountId></prof:InGlgAccountDeleteGlgAccountAccountId>
            <prof:InGlgAccountDeleteGlgAccountTimestmp>0001-01-01T00:00:00</prof:InGlgAccountDeleteGlgAccountTimestmp>
            <!--Optional:-->
            <prof:InGlgAccountPkeyGlgAccountAccountId></prof:InGlgAccountPkeyGlgAccountAccountId>
            <!--Optional:-->
            <prof:InGlgHCurrGroupCurrGroupId>ALL</prof:InGlgHCurrGroupCurrGroupId>
            <prof:InJustificIdJustific>99011</prof:InJustificIdJustific>
            <!--Optional:-->
            <prof:InLogMntRecordingAuthorizer1></prof:InLogMntRecordingAuthorizer1>
            <!--Optional:-->
            <prof:InLogMntRecordingAuthorizer2></prof:InLogMntRecordingAuthorizer2>
            <!--Optional:-->
            <prof:InLogMntRecordingReversalFlag>0</prof:InLogMntRecordingReversalFlag>
            <!--Optional:-->
            <prof:InLogMntRecordingTerminalNumber>172.21.42.190</prof:InLogMntRecordingTerminalNumber>
            <prof:InLogMntRecordingTrxCode>5011</prof:InLogMntRecordingTrxCode>
            <!--Optional:-->
            <prof:InPermitUnitGenericDetailDescription></prof:InPermitUnitGenericDetailDescription>
            <prof:InPermitUnitGenericDetailSerialNum>0</prof:InPermitUnitGenericDetailSerialNum>
            <!--Optional:-->
            <prof:InPrctpGenericDetailDescription></prof:InPrctpGenericDetailDescription>
            <prof:InPrctpGenericDetailSerialNum>0</prof:InPrctpGenericDetailSerialNum>
            <prof:InPrftTransactionIdTransact>5011</prof:InPrftTransactionIdTransact>
            <prof:InProductIdProduct>99011</prof:InProductIdProduct>
            <!--Optional:-->
            <prof:InSubsGenericDetailDescription></prof:InSubsGenericDetailDescription>
            <prof:InSubsGenericDetailSerialNum>0</prof:InSubsGenericDetailSerialNum>
            <!--Optional:-->
            <prof:InTeamInformationJustificationDescription></prof:InTeamInformationJustificationDescription>
            <prof:InTeamInformationJustificationId>0</prof:InTeamInformationJustificationId>
            <!--Optional:-->
            <prof:InTeamInformationProductDescription></prof:InTeamInformationProductDescription>
            <prof:InTeamInformationProductId>0</prof:InTeamInformationProductId>
            <!--Optional:-->
            <prof:InTeamInformationTeamComments>COMMENTS</prof:InTeamInformationTeamComments>
            <!--Optional:-->
            <prof:InTeamInformationTransactionDescription></prof:InTeamInformationTransactionDescription>
            <prof:InTeamInformationTransactionId>0</prof:InTeamInformationTransactionId>
			
			
            <prof:InTeamInformationUnitCode>0</prof:InTeamInformationUnitCode>
            <!--Optional:-->
            <prof:InTeamInformationUserTerminalId>122.11.22.11</prof:InTeamInformationUserTerminalId>
            <prof:InGlgTempAccountsFkAccountingRule>0</prof:InGlgTempAccountsFkAccountingRule>
            <!--Optional:-->
            <prof:InGlgTempAccountsFkAccRuleDescr></prof:InGlgTempAccountsFkAccRuleDescr>
            <prof:InGlgTempAccountsFkChargeCode>0</prof:InGlgTempAccountsFkChargeCode>
            <!--Optional:-->
            <prof:InGlgTempAccountsFkGlgEntepCtl></prof:InGlgTempAccountsFkGlgEntepCtl>
            <prof:InGlgTempAccountsFkLgBatchParameter>0</prof:InGlgTempAccountsFkLgBatchParameter>
            <!--Optional:-->
            <prof:InGlgTempAccountsFkOriginId>0</prof:InGlgTempAccountsFkOriginId>
            <!--Optional:-->
            <prof:InGlgTempAccountsFkOriginType>0</prof:InGlgTempAccountsFkOriginType>
            <!--Optional:-->
            <prof:InGlgTempAccountsFkProductDescr></prof:InGlgTempAccountsFkProductDescr>
            <prof:InGlgTempAccountsFkProductidProdu>0</prof:InGlgTempAccountsFkProductidProdu>
            <!--Optional:-->
            <prof:InGlgTempAccountsGlaccount>0</prof:InGlgTempAccountsGlaccount>
            <prof:InGlgTempAccountsSn>0</prof:InGlgTempAccountsSn>
            <prof:InGlgTempAccountsTrxDate>0001-01-01T00:00:00</prof:InGlgTempAccountsTrxDate>
            <!--Optional:-->
            <prof:InGlgTempAccountsTrxUsr>ERPUSER</prof:InGlgTempAccountsTrxUsr>
            <!--Optional:-->
            <prof:InGlgTempAccountsWhereis>0</prof:InGlgTempAccountsWhereis>
            <!--Optional:-->
            <prof:InProductDescription></prof:InProductDescription>
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
            <prof:ReferenceKey>'.$account->No.'</prof:ReferenceKey>
            <!--Optional:-->
            <prof:SotfOtp></prof:SotfOtp>
            <!--Optional:-->
            <prof:BranchCode></prof:BranchCode>
            <!--Optional:-->
            <prof:ExtUniqueUserId>'.Yii::$app->session->get('USER').'</prof:ExtUniqueUserId>
            <!--Optional:-->
            <prof:ExtDeviceAuthCode></prof:ExtDeviceAuthCode>
         </prof:executionParameters>
      </prof:G0501V_GlAccountValidation>
   </soapenv:Body>
</soapenv:Envelope>',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: text/xml'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
			
			if(!empty($response))
			{
				$xml_object = simplexml_load_string($response); 

			// register your used namespace prefixes
			$xml_object->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance'); 
			$xml_object->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope'); 
			$nodes = $xml_object->xpath("/soap:Envelope/soap:Body");

			return ($nodes[0]->G0501V_GlAccountValidationResponse->G0501V_GlAccountValidationResult->Result->Message);
			}

	}
	
	
	
	
	public function actionPostImprest(object $record)
	{
		

				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => env('PROFITS_LIVE_BASEURL'),
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_SSL_VERIFYPEER => false, // DON'T VERIFY SSL CERTIFICATE
				  CURLOPT_SSL_VERIFYHOST => 0, // DON'T VERIFY HOST NAME
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:prof="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
				   <soapenv:Header/>
				   <soapenv:Body>
					  <prof:Adds01_AdditionalTransactionPosting>
						 <!--Optional:-->
						 <prof:import>
							<!--Optional:-->
							<prof:Command>INSERT</prof:Command>
							<!--Optional:-->
							<prof:InAuthorisationGrantedIefSuppliedFlag>0</prof:InAuthorisationGrantedIefSuppliedFlag>
							<!--Optional:-->
							<prof:InBopGenericDetailShortDescription></prof:InBopGenericDetailShortDescription>
							<!--Optional:-->
							<prof:InCountryGenericDetailShortDescription></prof:InCountryGenericDetailShortDescription>
							<prof:InFxftProductIdProduct>12502</prof:InFxftProductIdProduct>
							<prof:InFxftServiceIdProduct>12502</prof:InFxftServiceIdProduct>
							<!--Optional:-->
							<prof:InGeneralFxFtRecordingCustIdPasspNum>TEST2</prof:InGeneralFxFtRecordingCustIdPasspNum>
							<!--Optional:-->
							<prof:InGlgAccountAccountId></prof:InGlgAccountAccountId>
							<prof:InGlgAccountSecLevel>0</prof:InGlgAccountSecLevel>
							<prof:InInputCurrencyIdCurrency>0</prof:InInputCurrencyIdCurrency>
							<!--Optional:-->
							<prof:InInputCurrencyShortDescr></prof:InInputCurrencyShortDescr>
							<!--Optional:-->
							<prof:InMainFxftFxFtRecordingComments>'.htmlspecialchars($record->Posting_Desricption).'</prof:InMainFxftFxFtRecordingComments>
							<prof:InMainFxftPrftTransactionIdTransact>12501</prof:InMainFxftPrftTransactionIdTransact>
							<prof:InRecordCaseFxFtRecordingIDrCrFlag>0</prof:InRecordCaseFxFtRecordingIDrCrFlag>
							<prof:InRecordCaseFxFtRecordingISegmentType>0</prof:InRecordCaseFxFtRecordingISegmentType>
							<!--Optional:-->
							<prof:InSectorGenericDetailShortDescription></prof:InSectorGenericDetailShortDescription>
							<prof:InSecurityInBankParametersMaxAmntRateTbl>0</prof:InSecurityInBankParametersMaxAmntRateTbl>
							<prof:InSecurityInBankParametersMaxAmntSrs>0</prof:InSecurityInBankParametersMaxAmntSrs>
							<!--Optional:-->
							<prof:InSecurityInTerminalTerminalNumber></prof:InSecurityInTerminalTerminalNumber>
							<prof:InToBeConvertedIefSuppliedAmount>0</prof:InToBeConvertedIefSuppliedAmount>
							<!--Optional:-->
							<prof:InExportPosting>
							   <!--Zero or more repetitions:-->
							   <prof:Adds01InExportPostingItem>
								  <prof:InExportPostingInGroupJustificIdJustific>0</prof:InExportPostingInGroupJustificIdJustific>
								  <prof:InExportPostingInGrpChequeBookItemItemSerialNumber>0</prof:InExportPostingInGrpChequeBookItemItemSerialNumber>
								  <prof:InExportPostingInGrpCurrencyIdCurrency>22</prof:InExportPostingInGrpCurrencyIdCurrency>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpCurrencyShortDescr>KES</prof:InExportPostingInGrpCurrencyShortDescr>
								  <prof:InExportPostingInGrpDepUnclearTransAvailabilityDate>0001-01-01T00:00:00</prof:InExportPostingInGrpDepUnclearTransAvailabilityDate>
								  <prof:InExportPostingInGrpDepositAccountCDigit>0</prof:InExportPostingInGrpDepositAccountCDigit>
								  <prof:InExportPostingInGrpDpTrxSpecialAgrAvailDateSpread>0</prof:InExportPostingInGrpDpTrxSpecialAgrAvailDateSpread>
								  <prof:InExportPostingInGrpDpTrxSpecialAgrValueDateSpread>0</prof:InExportPostingInGrpDpTrxSpecialAgrValueDateSpread>
								  <prof:InExportPostingInGrpFdValeurBalanceValueDate>0001-01-01T00:00:00</prof:InExportPostingInGrpFdValeurBalanceValueDate>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpFxFtRecordingComments>'.htmlspecialchars($record->Posting_Desricption).'</prof:InExportPostingInGrpFxFtRecordingComments>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpFxFtRecordingGlAccount>'.$record->GL_Account.'</prof:InExportPostingInGrpFxFtRecordingGlAccount>
								  <prof:InExportPostingInGrpFxFtRecordingIDomesticAmount>'.$record->Amount.' </prof:InExportPostingInGrpFxFtRecordingIDomesticAmount>
								  <prof:InExportPostingInGrpFxFtRecordingIDrCrFlag>1</prof:InExportPostingInGrpFxFtRecordingIDrCrFlag>
								  <prof:InExportPostingInGrpFxFtRecordingIRate>000001.000000</prof:InExportPostingInGrpFxFtRecordingIRate>
								  <prof:InExportPostingInGrpFxFtRecordingISegmentType>2</prof:InExportPostingInGrpFxFtRecordingISegmentType>
								  <prof:InExportPostingInGrpFxFtRecordingITrxAmount>'.$record->Amount.'</prof:InExportPostingInGrpFxFtRecordingITrxAmount>
								  <prof:InExportPostingInGrpFxftJustificIdJustific>12501</prof:InExportPostingInGrpFxftJustificIdJustific>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpIefSuppliedFlag></prof:InExportPostingInGrpIefSuppliedFlag>
								  <prof:InExportPostingInGrpPrftTransactionIdTransact>3191</prof:InExportPostingInGrpPrftTransactionIdTransact>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpProfitsAccountAccountNumber></prof:InExportPostingInGrpProfitsAccountAccountNumber>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpTeamInformationAuthorizationResult></prof:InExportPostingInGrpTeamInformationAuthorizationResult>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpTeamInformationSuper1Code></prof:InExportPostingInGrpTeamInformationSuper1Code>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpTeamInformationSuper2Code></prof:InExportPostingInGrpTeamInformationSuper2Code>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField1></prof:InExportPostingInGrpThirdpartyPaymentTppField1>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField2></prof:InExportPostingInGrpThirdpartyPaymentTppField2>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField3></prof:InExportPostingInGrpThirdpartyPaymentTppField3>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField4></prof:InExportPostingInGrpThirdpartyPaymentTppField4>
								  <prof:InExportPostingInGrpUnitCode>143</prof:InExportPostingInGrpUnitCode>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpUnitUnitName></prof:InExportPostingInGrpUnitUnitName>
							   </prof:Adds01InExportPostingItem>
						  
							<prof:Adds01InExportPostingItem>
								  <prof:InExportPostingInGroupJustificIdJustific>33100</prof:InExportPostingInGroupJustificIdJustific>
								  <prof:InExportPostingInGrpChequeBookItemItemSerialNumber>0</prof:InExportPostingInGrpChequeBookItemItemSerialNumber>
								  <prof:InExportPostingInGrpCurrencyIdCurrency>22</prof:InExportPostingInGrpCurrencyIdCurrency>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpCurrencyShortDescr>KES</prof:InExportPostingInGrpCurrencyShortDescr>
								  <prof:InExportPostingInGrpDepUnclearTransAvailabilityDate>0001-01-01T00:00:00</prof:InExportPostingInGrpDepUnclearTransAvailabilityDate>
								  <prof:InExportPostingInGrpDepositAccountCDigit>0</prof:InExportPostingInGrpDepositAccountCDigit>
								  <prof:InExportPostingInGrpDpTrxSpecialAgrAvailDateSpread>0</prof:InExportPostingInGrpDpTrxSpecialAgrAvailDateSpread>
								  <prof:InExportPostingInGrpDpTrxSpecialAgrValueDateSpread>0</prof:InExportPostingInGrpDpTrxSpecialAgrValueDateSpread>
								  <prof:InExportPostingInGrpFdValeurBalanceValueDate>0001-01-01T00:00:00</prof:InExportPostingInGrpFdValeurBalanceValueDate>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpFxFtRecordingComments>'.htmlspecialchars($record->Posting_Desricption).'</prof:InExportPostingInGrpFxFtRecordingComments>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpFxFtRecordingGlAccount></prof:InExportPostingInGrpFxFtRecordingGlAccount>
								  <prof:InExportPostingInGrpFxFtRecordingIDomesticAmount>'.$record->Amount.'</prof:InExportPostingInGrpFxFtRecordingIDomesticAmount>
								  <prof:InExportPostingInGrpFxFtRecordingIDrCrFlag>2</prof:InExportPostingInGrpFxFtRecordingIDrCrFlag>
								  <prof:InExportPostingInGrpFxFtRecordingIRate>000001.000000</prof:InExportPostingInGrpFxFtRecordingIRate>
								  <prof:InExportPostingInGrpFxFtRecordingISegmentType>0</prof:InExportPostingInGrpFxFtRecordingISegmentType>
								  <prof:InExportPostingInGrpFxFtRecordingITrxAmount>'.$record->Amount.'</prof:InExportPostingInGrpFxFtRecordingITrxAmount>
								  <prof:InExportPostingInGrpFxftJustificIdJustific>12502</prof:InExportPostingInGrpFxftJustificIdJustific>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpIefSuppliedFlag></prof:InExportPostingInGrpIefSuppliedFlag>
								  <prof:InExportPostingInGrpPrftTransactionIdTransact>3181</prof:InExportPostingInGrpPrftTransactionIdTransact>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpProfitsAccountAccountNumber>'.$record->Fosa_Account.'</prof:InExportPostingInGrpProfitsAccountAccountNumber>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpTeamInformationAuthorizationResult></prof:InExportPostingInGrpTeamInformationAuthorizationResult>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpTeamInformationSuper1Code></prof:InExportPostingInGrpTeamInformationSuper1Code>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpTeamInformationSuper2Code></prof:InExportPostingInGrpTeamInformationSuper2Code>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField1></prof:InExportPostingInGrpThirdpartyPaymentTppField1>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField2></prof:InExportPostingInGrpThirdpartyPaymentTppField2>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField3></prof:InExportPostingInGrpThirdpartyPaymentTppField3>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpThirdpartyPaymentTppField4></prof:InExportPostingInGrpThirdpartyPaymentTppField4>
								  <prof:InExportPostingInGrpUnitCode>0</prof:InExportPostingInGrpUnitCode>
								  <!--Optional:-->
								  <prof:InExportPostingInGrpUnitUnitName></prof:InExportPostingInGrpUnitUnitName>
							   </prof:Adds01InExportPostingItem>
							</prof:InExportPosting>
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
							<prof:ExtUniqueUserId>'.Yii::$app->session->get('USER').'</prof:ExtUniqueUserId>
							<!--Optional:-->
							<prof:ExtDeviceAuthCode></prof:ExtDeviceAuthCode>
						 </prof:executionParameters>
					  </prof:Adds01_AdditionalTransactionPosting>
				   </soapenv:Body>
				</soapenv:Envelope>',
				  CURLOPT_HTTPHEADER => array(
					'Content-Type: text/xml'
				  ),
				));

				$response = curl_exec($curl);

				if (curl_errno($curl)) {
					$error_msg = curl_error($curl);
					echo $error_msg;
				}

				curl_close($curl);
				
				if(!empty($response))
				{
					$xml_object = simplexml_load_string($response); 

				// register your used namespace prefixes
				$xml_object->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance'); 
				$xml_object->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope'); 
				$nodes = $xml_object->xpath("/soap:Envelope/soap:Body");
				// return ($nodes[0]->G0501V_GlAccountValidationResponse->G0501V_GlAccountValidationResult->Result->Message);
				return json_encode($nodes[0]->Adds01_AdditionalTransactionPostingResponse->Adds01_AdditionalTransactionPostingResult);
				}

	
	}
	
	
	
	
	
	// Get Gl to Create
	
	/* Direct Posting - true, statut - pending*/
	
	public function actionGetgl()
	{
		$service = Yii::$app->params['ServiceName']['ChartOfAccount'];
		
		$filter = [
			'Direct_Posting' => true,
			'Status' => 'Pending'
		];
		$result = Yii::$app->navHelper->getData($service,$filter);
		
		/*print '<pre>';
		print_r($result);
		exit;*/
		return $result;
	}
	
	
	public function actionSyncglaccount()
	{
		$service = Yii::$app->params['ServiceName']['GL_Account'];
		$chartofaccounts = $this->actionGetgl();
		/*print '<pre>';
		print_r($chartofaccounts);
		exit;*/
		
		if(is_array($chartofaccounts)) {
				foreach($chartofaccounts as $account) {
			
					$ac = Yii::$app->navHelper->readByKey($service, $account->Key);
					$result = $this->Postgl($ac);
					print '<pre>';
					var_dump($result);
					$this->logger($result);
					
					if($result == 'ACC_AE')
					{
						// Update account on ERP
						$params = [
							'Key' => $account->Key,
							'Status' => 'Completed',
							'Comments' => $result
						];
						
						$update = Yii::$app->navHelper->updateData($service, $params);
						
						/*print_r($update);*/
						$this->logger($update);
					}else 
					{
						// Update account on ERP
						$params = [
							'Key' => $account->Key,
							'Status' => 'Completed',
							'Comments' => $result
						];
						
						$update = Yii::$app->navHelper->updateData($service, $params);
						
						print_r($update);
						$this->logger($update);
					}				
					
					exit;	
				}
		}
		
		return Json_encode(['State' => 'No transactions to synchronize.']);
			
	}
	
	
	// Get Imprest Records to Sync
	
	public function actionGetImprest()
	{
		$service = Yii::$app->params['ServiceName']['Imprest_Profits'];
		
		$filter = [
			'Status' => "Pending"
		];
		$result = Yii::$app->navHelper->getData($service,$filter);
		
		/*print '<pre>';
		print_r($result);
		exit;*/
		
		return $result;
	}
	
	// Sync Imprest
	
	public function actionSyncImprest()
	{
		$service = Yii::$app->params['ServiceName']['Imprest_Profits'];
		$ImprestRecords = $this->actionGetImprest();
		/*print '<pre>';
		print_r($ImprestRecords);
		exit;*/
		
		if(is_array($ImprestRecords)) {
				foreach($ImprestRecords as $account) {
				
				$account->Posting_Desricption = property_exists($account,'Posting_Desricption')?$account->Posting_Desricption:'';

				// Trancate the posting Desc to a max of 40 chars
				$account->Posting_Desricption = ($account->Posting_Desricption)?substr($account->Posting_Desricption,0,40):'';
				
				$account->Comments = property_exists($account,'Comments')?$account->Comments:'Comments Not Set';
					
					$result = json_decode($this->actionPostImprest($account));
					
					print '<pre>';
					print_r($result);
					
					
					$this->imprestLogger($result);
					
					if($result->Result->Message == 'success')
					{
						// Update Imprest Transaction on ERP
						$params = [
							'Key' => $account->Key,
							'TrxDate' => $this->processDate($result->OutDuplicateFxFtRecordingTrxDate) ,
							'TrxSn' => $result->OutDuplicateFxFtRecordingTrxSn ,
							'TrxUnit' => $result->OutDuplicateFxFtRecordingTrxUnit ,
							'TrxUsr' => $result->OutDuplicateFxFtRecordingTrxUsr,
							'Status' => 'Completed',
							//'Reversal' => true, //For testing reversals only
							
						];
						
						$update = Yii::$app->navHelper->updateData($service, $params);
						print '<pre>';
						print_r($update);
						$this->imprestLogger($update);
					}elseif($result->Result->Type == 'Error' || $result->Result->Type == 'Unknown')
					{
						
						// Update Imprest Transaction on ERP
						$params = [
							'Key' => $account->Key,
							'TrxDate' => $this->processDate($result->Tun->TrxDate) ,
							'TrxSn' => $result->Tun->TunInternalSn ,
							'TrxUnit' => $result->Tun->TrxUnit ,
							'TrxUsr' => $result->Tun->TrxUser,
							'Status' => 'Failed',
							'Comments' =>  $result->Result->Message
							
						];
						
						$update = Yii::$app->navHelper->updateData($service, $params);
						print '<pre>';
						print_r($update);
						$this->imprestLogger($update);
					}				
					
					exit;	
				}
		}
		
		return Json_encode(['State' => 'No Imprest transactions to synchronize.']);
			
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
					
					$account->Posting_Desricption = property_exists($account,'Posting_Desricption')?htmlspecialchars($account->Posting_Desricption):'';
				
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
									'Comments' =>  $result->Result->Message
									
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
				  CURLOPT_URL => env('PROFITS_LIVE_BASEURL'),
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_SSL_VERIFYPEER => false, // DON'T VERIFY SSL CERTIFICATE
				  CURLOPT_SSL_VERIFYHOST => 0, // DON'T VERIFY HOST NAME
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
								<prof:ExtUniqueUserId>'.Yii::$app->session->get('USER').'</prof:ExtUniqueUserId>
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
				  CURLOPT_URL => env('PROFITS_LIVE_BASEURL'),
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_SSL_VERIFYPEER => false, // DON'T VERIFY SSL CERTIFICATE
				  CURLOPT_SSL_VERIFYHOST => 0, // DON'T VERIFY HOST NAME
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
											<prof:ExtUniqueUserId>'.Yii::$app->session->get('USER').'</prof:ExtUniqueUserId>
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
		$filename = 'log/imprest.txt';
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
	


	public function actionToken()
	{
		

		

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://igateway.policesacco.com/iProfits2.GatewayService/ProfitsExtGateway.asmx',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYPEER => false, // DON'T VERIFY SSL CERTIFICATE
  CURLOPT_SSL_VERIFYHOST => 0, // DON'T VERIFY HOST NAME
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <CI3499V_GetAuthorized xmlns="http://www.intrasoft-internatinal.com/GatewayService/ProfitsExt">
      <import />
      <executionParameters>
        <ChannelId>9912</ChannelId>
        <Password>!*#IANSOFT*#!</Password>
        <ExtUniqueUserId>ERPPRDAPP01\\KPSADMIN</ExtUniqueUserId>
      </executionParameters>
    </CI3499V_GetAuthorized>
  </soap:Body>
</soap:Envelope>',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: text/xml'
  ),
));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
	echo $error_msg;
}

curl_close($curl);
echo $response;


	}
	
	
	
	
	
	
}
