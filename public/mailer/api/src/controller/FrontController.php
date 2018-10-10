<?php
namespace controller;

require_once 'Router.php';
require_once 'MailController.php';

/**
 * FrontController
 * FrontController is used for execute result of routing
 */
class FrontController
{
    public static function proc()
    {
        ignore_user_abort(true);    //prevent: a client disconnect should abort script execution
        set_time_limit(0);
        
        // Routing
        $ret = Router::proc();

        if (count($ret) !== 0) {
            header('Content-type: application/json;charset=utf-8');

            $controller = new MailController();
            if (method_exists($controller, $ret['actionName'])) {
                $action = $ret['actionName'];
                $controller->$action($ret['parameters']);
            } else {
                // in case not found the action
                http_response_code(404);
                echo json_encode(array(
                    'message' => $ret['actionName'] . ' chưa được định nghĩa'
                ));
            }
        } else {
            // in case not found the API route
            http_response_code(404);
            echo json_encode(array(
                'message' => 'Không tìm thấy API'
            ));
        }
    }
}

?>
