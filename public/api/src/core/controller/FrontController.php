<?php
namespace core\controller;

use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/controller/Router.php';

/**
 * FrontController
 * FrontController is used for execute result of routing
 */
class FrontController
{
    public static function proc()
    {
        session_start();

        // Routing
        $ret = Router::proc();

        if (count($ret) !== 0) {

            if($ret['uploadFile']){
                header('Content-type: multipart/form-data');
            } else {
                header('Content-type: application/json;charset=utf-8');
            }

            if ($ret['needAuthen']
                && (!isset($_SESSION['username'])
                    || !isset($_SESSION['uid'])
                    || !isset($_SESSION['role'])
                    || !isset($_SESSION['vnuMail']))
            ) {
                // in case of not authenticated
                http_response_code(401);
                echo json_encode(array(
                    'message' => Constant::notAuthenticationText
                ));
            } else {
                // Execute the result of rerouting
                $filename = 'src/' . $ret['moduleName'] . '/controller/' . $ret['controllerName'] . '.php';

                if (file_exists($filename)) {
                    require_once $filename;

                    $controllerName = '\\' . $ret['moduleName'] . '\\controller\\' . $ret['controllerName'];
                    $controller = new $controllerName();
                    if (method_exists($controller, $ret['actionName'])) {
                        $action = $ret['actionName'];
                        $controller->$action($ret['parameters']);
                    } else {
                        // in case not found the action
                        http_response_code(404);
                        echo json_encode(array(
                            'message' => $ret['actionName'] . Constant::notDefinedText
                        ));
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        'message' => Constant::missingText . 'API này'
                    ));
                }
            }
        } else {
            // in case not found the API route
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . 'API'
            ));
        }
    }
}

?>
