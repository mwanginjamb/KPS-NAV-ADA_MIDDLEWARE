<?php

namespace app\library;
use yii;
use yii\base\Component;
class Navision extends Component
{
    public function init()
    {
        parent::init();
    }
    public function isUp($url)
    {
        //Check if service is up
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        //echo function_exists('curl_version');
        //return($httpcode);
        curl_close($ch);
        if(($httpcode == "200" ) || ( $httpcode == "302" )|| ( $httpcode == "401" )){
            return true;
        }else{
            return $httpcode;
        }
    }
    public function readEntries($credentials, $soapWsdl, $filter='')
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->ReadMultiple(['filter' => $filter, 'setSize' => 1000]);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


     public function readEntry($credentials, $soapWsdl,$filter,$filterValue)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->Read([$filter => $filterValue]);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


     // Get Record Id from Key

      public function getRecordID($credentials, $soapWsdl,$Key)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->GetRecIdFromKey(['Key' => $Key]);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    // ReaD By RecordId


     public function readByRecID($credentials, $soapWsdl,$Key)
    {

        $RecordId = $this->getRecordID($credentials, $soapWsdl, $Key);

        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->ReadByRecId(['recId' => $RecordId->GetRecIdFromKey_Result]);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }





    public function addEntry($credentials, $soapWsdl, $Entry, $EntryID)
    {
        /* print '<pre>';
         print_r($Entry); exit;*/
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->Create([$EntryID => $Entry]);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    
    public function updateEntry($credentials, $soapWsdl, $Entry, $EntryID)
    {
        $client = $this->createClient($credentials, $soapWsdl);

        try {
            $result = $client->Update([$EntryID => $Entry]);
            //ini_set('soap.wsdl_cache_ttl', 1);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }
    }

    public function updateMultiple($credentials, $soapWsdl, $Entry, $EntryID)
    {
        $client = $this->createClient($credentials, $soapWsdl);

        try {
            $result = $client->UpdateMultiple([$EntryID => $Entry]);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }
    }

    public function deleteEntry($credentials, $soapWsdl, $key)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            //$result = $client->Delete($WhereArray);
            $result = $client->Delete(['Key' => $key]);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }
    }


    //RBSS Sale invoice code unit
    public function GenerateInvoice($credentials, $soapWsdl, $Entry){//Approve any requests
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->CreateInvoice($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }
    }

//RBSS Create customer

    public function CreateCustomer($credentials, $soapWsdl, $Entry){//Approve any requests
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->CreateCustomer($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }
    }



    //IANSOFT LEAVE MGT

    public function SendLeaveRequestApproval($credentials, $soapWsdl, $Entry)
    {
        //$result =  $client->Create([$EntryID => $Entry]);
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendLeaveApplicationForApproval($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    // Leave Recall
    public function RecallApproval($credentials, $soapWsdl, $Entry, $soapWsdlMethod)
    {
        //$result =  $client->Create([$EntryID => $Entry]);
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->$soapWsdlMethod($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    public function CancelLeaveRequestApproval($credentials, $soapWsdl, $Entry)
    {
        //$result =  $client->Create([$EntryID => $Entry]);
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanCancelLeaveApplicationForApproval($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    public function IanApproveLeaveApplication($credentials, $soapWsdl, $Entry)
    {
        //$result =  $client->Create([$EntryID => $Entry]);
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanApproveLeaveApplication($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    public function IanRejectLeaveApplication($credentials, $soapWsdl, $Entry)
    {
        //$result =  $client->Create([$EntryID => $Entry]);
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanRejectLeaveApplication($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Call Job Application CodeUnit

    public function IanCreateJobApplication($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanCreateJobApplication($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Payslip report

    public function IanGeneratePayslip($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanGeneratePayslip($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }


    //Generate P9 report

    public function IanGenerateP9($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanGenerateP9($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }
    /** PERFOMANCE MANAGEMENT FUNCTIONS ON APPRAISAL WORKFLOW CODEUNIT */
    //send Appraisal for approval

    public function IanSendGoalSettingForApproval($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendGoalSettingForApproval($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Approve set Goals

    public function IanApproveGoalSetting($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanApproveGoalSetting($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Reject Appraisal and send it back to appraisee


    public function IanSendGoalSettingBackToAppraisee($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendGoalSettingBackToAppraisee($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Send Mid Year Appraisal for Approval

    public function IanSendMYAppraisalForApproval($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendMYAppraisalForApproval($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Approve Mid Year Appraisal

    public function IanApproveMYAppraisal($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanApproveMYAppraisal($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Send Mid Year Appraisal back to Appraisee (A Rejection)

    public function IanSendMYAppraisaBackToAppraisee($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendMYAppraisaBackToAppraisee($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Send End Year Appraisal for Approval

    public function IanSendEYAppraisalForApproval($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendEYAppraisalForApproval($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }


    //Approve End Year Appraisal

    public function IanApproveEYAppraisal($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanApproveEYAppraisal($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Send End Year Appraisal back to Appraisee (Rejection)

    public function IanSendEYAppraisaBackToAppraisee($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendEYAppraisaBackToAppraisee($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //send End Year Appraisal to Peer1

    public function IanSendEYAppraisalToPeer1($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendEYAppraisalToPeer1($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //send End Year Appraisal to Peer2

    public function IanSendEYAppraisalToPeer2($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendEYAppraisalToPeer2($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //send appraisal to supervisor from peers

    public function IanSendEYAppraisaBackToSupervisorFromPeer($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendEYAppraisaBackToSupervisorFromPeer($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Send Appraisal to Agreement Level

    public function IanSendEYAppraisalToAgreementLevel($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanSendEYAppraisalToAgreementLevel($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    //Generate Appraisal Report

    public function IanGenerateAppraisalReport($credentials, $soapWsdl, $Entry)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->IanGenerateAppraisalReport($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    // Insert Invoice Line

    public function IanMobile($credentials, $soapWsdl, $Entry, $WsdlMethod)
    {
        $client = $this->createClient($credentials, $soapWsdl);
        try {
            $result = $client->$WsdlMethod($Entry);
            return $result;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }






    //Navision Critical Functions

    private function createClient($credentials, $soapWsdl)
    {

        if (!defined('USERPWD'))
            define('USERPWD', "$credentials->UserName:$credentials->PassWord");
        try {
            $opts = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                )
            );
            $oPtions = [
                'soap_version' => SOAP_1_2,
                'connection_timeout' => 180,
                'login' => $credentials->UserName,
                'password' => $credentials->PassWord,

                'trace' => 1,
                'stream_context' => stream_context_create($opts)
            ];

            $context = stream_context_create([
                'ssl' => [
                    // set some SSL/TLS specific options
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $options = array("login" => $credentials->UserName,
                "password" => $credentials->PassWord,
                "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
                "stream_context" => $context);

             //$client = new \SoapClient($soapWsdl, $options);
            // we unregister the current HTTP wrapper
            stream_wrapper_unregister('http');
            // we register the new HTTP wrapper //'\\common\\components\\NTLMStream'
            stream_wrapper_register('http', '\\app\\Library\\NTLMStream') or die("Failed to register protocol");


            $client = new NTLMSoapClient($soapWsdl, $options);


            return $client;
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(503, 'Service unavailable:'.$e);
            //return $e->getMessage();
        }
        return false;
    }

}

/**va
 * Extend the native soap class to support NTLM authentication
 **/
class NTLMSoapClient extends \SoapClient
{
    function __doRequest($request, $location, $action, $version, $one_way = NULL)
    {
        $headers = array(
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . $action . '"',
        );

        $this->__last_request_headers = $headers;
        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, USERPWD);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = @curl_exec($ch);

        return $response;

    }

    function __getLastRequestHeaders()
    {
        return implode("\n", $this->__last_request_headers) . "\n";
    }
}

/**
 * create new streamwrappers to overrride default http wrapper
 **/
class NTLMStream
{
    private $path;
    private $mode;
    private $options;
    private $opened_path;
    private $buffer;
    private $pos;

    /**
     * Open the stream
     *
     * @param unknown_type $path
     * @param unknown_type $mode
     * @param unknown_type $options
     * @param unknown_type $opened_path
     * @return unknown
     */
    public function stream_open($path, $mode, $options, $opened_path)
    {
        $this->path = $path;
        $this->mode = $mode;
        $this->options = $options;
        $this->opened_path = $opened_path;
        $this->createBuffer($path);
        return true;
    }

    /**
     * Close the stream
     *
     */
    public function stream_close()
    {
        curl_close($this->ch);
    }

    /**
     * Read the stream
     *
     * @param int $count number of bytes to read
     * @return content from pos to count
     */
    public function stream_read($count)
    {
        if (strlen($this->buffer) == 0) {
            return false;
        }
        $read = substr($this->buffer, $this->pos, $count);
        $this->pos += $count;
        return $read;
    }

    /**
     * write the stream
     *
     * @param int $count number of bytes to read
     * @return content from pos to count
     */
    public function stream_write($data)
    {
        if (strlen($this->buffer) == 0) {
            return false;
        }
        return true;
    }

    /**
     *
     * @return true if eof else false
     */
    public function stream_eof()
    {
        return ($this->pos > strlen($this->buffer));
    }

    /**
     * @return int the position of the current read pointer
     */
    public function stream_tell()
    {
        return $this->pos;
    }

    /**
     * Flush stream data
     */
    public function stream_flush()
    {
        $this->buffer = null;
        $this->pos = null;
    }

    /**
     * Stat the file, return only the size of the buffer
     *
     * @return array stat information
     */
    public function stream_stat()
    {
        $this->createBuffer($this->path);
        $stat = array(
            'size' => strlen($this->buffer),
        );
        return $stat;
    }

    /**
     * Stat the url, return only the size of the buffer
     *
     * @return array stat information
     */
    public function url_stat($path, $flags)
    {
        $this->createBuffer($path);
        $stat = array(
            'size' => strlen($this->buffer),
        );
        return $stat;
    }

    /**
     * Create the buffer by requesting the url through cURL
     *
     * @param unknown_type $path
     */
    private function createBuffer($path)
    {
        if ($this->buffer) {
            return;
        }
        $this->ch = curl_init($path);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($this->ch, CURLOPT_USERPWD, USERPWD);
        $this->buffer = curl_exec($this->ch);

        $this->pos = 0;
    }
}
