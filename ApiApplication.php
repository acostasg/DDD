<?php
require_once 'Application.php';

class Api_Application extends Application
{

    public function sendErrorResponse()
    {
        $errorName = null;
        if (is_int($this->_error['type'])) {
            $constants = get_defined_constants(true);
            foreach ($constants['Core'] as $cnst => $val) {
                if (substr($cnst, 0, 2) === 'E_' && $val == $this->_error['type']) {
                    $errorName = $cnst;
                    break;
                }
            }
        }
        header_remove();
        header('Content-type: application/json');
        header("HTTP/1.0 500 Internal server error");
        $response = array(
            'status' => 'failed',
            'response' => $this->_error['message'],
            'host' => 'http://' . $_SERVER['HTTP_HOST'],
            'file' => $this->_error['file'],
            'line' => $this->_error['line'],
        );
        echo serialize($response);
    }
}
