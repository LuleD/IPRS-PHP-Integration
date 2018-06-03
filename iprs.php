<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class IPRS
{
    /**
     * @var
     */
    private $log;

    /**
     * @var
     */
    private $pass;

    /**
     * @var string
     */
    private $soap_url;

    /**
     * IPRS constructor.
     */
    public function __construct()
    {
        $this->log = '<YOUR USERNAME>';

        $this->pass = '<YOUR PASSWORD>';

        $this->soap_url = "http://10.1.1.5:9004/IPRSServerwcf?wsdl";
    }

    public function getIPRSDatabyID($id_number = null, $serial_number = null)
    {
        try {
            $client = new SoapClient($this->soap_url, array(
                'trace' => true,
                'exceptions' => true,
                'connection_timeout' => 10,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                'soap_version' => SOAP_1_1, 'encoding' => 'ISO-8859-1'
            ));


            $response = $client->GetDataByIdCard(array('log' => $this->log, 'pass' => $this->pass, 'id_number' => $id_number, 'serial_number' => $serial_number));
            $success = $this->stdObjectToArray($response);
            $retval = $this->stdObjectToArray($success['GetDataByIdCardResult']);
            $othernames = str_replace(" ", "|", ltrim(rtrim($retval['Other_Name'])));

            $returndata['accountName'] = $retval['Surname'] . "|" . $othernames . "|" . $retval['First_Name'];
            $returndata['Surname'] = $retval['Surname'];
            $returndata['First_Name'] = $retval['First_Name'];
            $returndata['Other_Name'] = $retval['Other_Name'];
            $returndata['photo'] = $retval['Photo'];
            $returndata['dob'] = $retval['Date_of_Birth'];
            $returndata['docidno'] = $id_number;
            $returndata['gender'] = $retval['Gender'];
            $returndata['Serial_Number'] = $retval['Serial_Number'];
            $returndata['Place_of_Birth'] = $retval['Place_of_Birth'];

        } catch (Exception $e) {

            //$this->log("IPRSERROR", "IPRSERR", $e->getMessage());
            $returndata['ErrorCode'] = "EXP-101";
            $returndata['ErrorMessage'] = $e->getMessage();
            $returndata['ErrorOcurred'] = true;
        }

        return $returndata;
    }


    public function GetDataByPassport($id_number = null, $passport_number = null)
    {
        try {
            $client = new SoapClient($this->soap_url, array(
                'trace' => true,
                'exceptions' => true,
                'connection_timeout' => 10,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                'soap_version' => SOAP_1_1, 'encoding' => 'ISO-8859-1'
            ));


            $response = $client->GetDataByIdCard(array(
                'log' => $this->log, 'pass' => $this->pass,
                'id_number' => $id_number, 'passport_number' => $passport_number
            ));

            $success = $this->stdObjectToArray($response);

            $retval = $this->stdObjectToArray($success['GetDataByPassport']);

            print_r($retval);

            $othernames = str_replace(" ", "|", ltrim(rtrim($retval['Other_Name'])));

            $returndata['accountName'] = $retval['Surname'] . "|" . $othernames . "|" . $retval['First_Name'];
            $returndata['Surname'] = $retval['Surname'];
            $returndata['First_Name'] = $retval['First_Name'];
            $returndata['Other_Name'] = $retval['Other_Name'];
            $returndata['photo'] = $retval['Photo'];
            $returndata['dob'] = $retval['Date_of_Birth'];
            $returndata['docidno'] = $id_number;
            $returndata['gender'] = $retval['Gender'];
            $returndata['Serial_Number'] = $retval['Serial_Number'];
            $returndata['Place_of_Birth'] = $retval['Place_of_Birth'];

        } catch (Exception $e) {

            //$this->log("IPRSERROR", "IPRSERR", $e->getMessage());

            $returndata['ErrorCode'] = "EXP-101";
            $returndata['ErrorMessage'] = $e->getMessage();
            $returndata['ErrorOcurred'] = true;
        }

        return $returndata;
    }


    private function stdObjectToArray($d)
    {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        } else {
            if (is_array($d)) {

                /*
                 * Return array converted to object
                 * Using __FUNCTION__ (Magic constant)
                 * for recursive call
                 */
                return array_map(__FUNCTION__, $d);
            }
        }
        return $d;
    }
}
header('Content-Type: application/json');

$obj = new IPRS();

if ($_GET['id_type'] == 'ID') {
    echo json_encode($obj->getIPRSDatabyID($_GET['ID_NO']));
    exit();
} elseif ($_GET['id_type'] == 'PASSPORT_NO') {
    echo json_encode($obj->GetDataByPassport($_GET['PASSPORT_NO']));
    exit();
} else {
    echo json_encode(array('status' => 'err', 'message' => 'Invalid Request!'));
    exit();
}