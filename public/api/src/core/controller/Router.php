<?php
namespace core\controller;

class Router
{

    public static function proc()
    {
        // Explode the URI
        $requestURI = explode('/', strtolower(strtok($_SERVER['REQUEST_URI'],'?')));
        $scriptName = explode('/', strtolower($_SERVER['SCRIPT_NAME']));
        $commandArray = array_diff_assoc($requestURI, $scriptName);

        $commandArray = array_values($commandArray);

        if (count($commandArray) != 0) {
            return self::api($commandArray);
        } else {
            return array();
        }
    }

    private static function api($commandArray)
    {
        $ret = array();
        $ret['moduleName'] = '';
        $ret['controllerName'] = '';
        $ret['actionName'] = '';
        $ret['parameters'] = array();
        $ret['needAuthen'] = true;
        $ret['uploadFile'] = false;


        /*****************************
         * ROUTE for all user
         * special first command array
         ****************************/
        // Login
        // api/auth POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST'
            && $commandArray[0] == 'auth'
        ) {
            $ret['moduleName'] = 'account';
            $ret['controllerName'] = 'AccountController';
            $ret['actionName'] = 'login';
            $ret['needAuthen'] = false;
        }

        // Load auth
        // api/load-auth GET
        elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
            && $commandArray[0] == 'load-auth'
        ) {
            $ret['moduleName'] = 'account';
            $ret['controllerName'] = 'AccountController';
            $ret['actionName'] = 'loadAuthAccount';
        }

        // Logout
        // api/logout DELETE
        elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
            && $commandArray[0] == 'logout'
        ) {
            $ret['moduleName'] = 'account';
            $ret['controllerName'] = 'AccountController';
            $ret['actionName'] = 'logout';
            $ret['needAuthen'] = false;
        }

        // Change password
        // api/change-password POST
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
            && $commandArray[0] == 'change-password'
        ) {
            $ret['moduleName'] = 'account';
            $ret['controllerName'] = 'AccountController';
            $ret['actionName'] = 'changePassword';
        }

        // recover password
        // api/forgot-password POST
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
            && $commandArray[0] == 'forgot-password'
        ) {
            $ret['moduleName'] = 'account';
            $ret['controllerName'] = 'AccountController';
            $ret['actionName'] = 'forgotPassword';
            $ret['needAuthen'] = false;
        }

        // Set password
        // api/set-password POST
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
            && $commandArray[0] == 'set-password'
        ) {
            $ret['moduleName'] = 'account';
            $ret['controllerName'] = 'AccountController';
            $ret['actionName'] = 'setPassword';
            $ret['needAuthen'] = false;
        }


        /***************************
         * ROUTE for superuser
         * start with /api/superuser
         **************************/
        elseif ($commandArray[0] == 'superuser') {
            // Create faculty
            // api/superuser/faculty POST
            if ($_SERVER['REQUEST_METHOD'] == 'POST'
                && $commandArray[1] == 'faculty'
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'AccountManagerController';
                $ret['actionName'] = 'createFaculty';
            }

            // Delete faculty
            // api/superuser/faculty/<id> DELETE
            elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                && $commandArray[1] == 'faculty'
                && count($commandArray) == 3
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'AccountManagerController';
                $ret['actionName'] = 'deleteFacultyById';
                $ret['parameters']['id'] = $commandArray[2];
            }

            // Get all faculty
            // api/superuser/faculty[?page=<int>[&limit=<int>][&order=<field>[&DESC=true]]] GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && $commandArray[1] == 'faculty'
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyController';
                $ret['actionName'] = 'getFaculty';
            }


            /**********************************
             * ROUTE for superuser edit degree
             * start with /api/superuser/degree
             *********************************/
            elseif ($commandArray[1] == 'degree') {

                // Add a degree
                // api/superuser/degree POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST'
                    && count($commandArray) == 2
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'addDegree';
                }

                // Update degree information
                // api/superuser/degree/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'updateDegreeById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Delete degree
                // api/superuser/degree/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'deleteDegreeById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /**********************************
             * ROUTE for superuser edit degree
             * start with /api/superuser/training-type
             *********************************/
            elseif ($commandArray[1] == 'training-type') {

                // Add a training-type
                // api/superuser/training-type POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST'
                    && count($commandArray) == 2
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'addTrainingType';
                }

                // Update training-type information
                // api/superuser/training-type/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'updateTrainingTypeById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Delete training-type
                // api/superuser/training-type/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'deleteTrainingTypeById';
                    $ret['parameters']['id'] = $commandArray[2];
                }


                // not match
                else {
                    return array();
                }
            }

            /**********************************
             * ROUTE for superuser edit training level
             * start with /api/superuser/training-level
             *********************************/
            elseif ($commandArray[1] == 'training-level') {

                // Add a training-level
                // api/superuser/training-level POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST'
                    && count($commandArray) == 2
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'addTrainingLevel';
                }

                // Update training-level information
                // api/superuser/training-level/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'updateTrainingLevelById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Delete training-level
                // api/superuser/training-level/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'superuser';
                    $ret['controllerName'] = 'DictManagerController';
                    $ret['actionName'] = 'deleteTrainingLevelById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
             * ROUTE for admin edit quota
             * start with /api/superuser/quota
             ************************/
            elseif($commandArray[1] == 'quota') {
                //Create new version of quotas
                // api/superuser/quota POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'QuotaController';
                    $ret['actionName'] = 'createQuotaVersion';
                }

                // update quotas by version
                // api/superuser/quota/<version> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'QuotaController';
                    $ret['actionName'] = 'updateQuotaVersion';
                    $ret['parameters']['version'] = $commandArray[2];
                }

                // active/deactive quotas by version
                // api/superuser/quota/<version> PATCH
                elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'QuotaController';
                    $ret['actionName'] = 'activeQuotaVersion';
                    $ret['parameters']['version'] = $commandArray[2];
                }

                // Delete quotas by version
                // api/superuser/quota/<version> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'QuotaController';
                    $ret['actionName'] = 'deleteQuotasByVersion';
                    $ret['parameters']['version'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            // not match
            else {
                return array();
            }
        }

        /*************************************
         * ROUTE for common api of degree
         * start with /api/degree
         ************************************/
        elseif ($commandArray[0] == 'degree') {
            // Get all degree
            // api/degree GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'DictManagerController';
                $ret['actionName'] = 'getDegree';
            }

            // Get a degree
            // api/degree/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'DictManagerController';
                $ret['actionName'] = 'getDegreeById';
                $ret['parameters']['id'] = $commandArray[1];
            }
        }

        /*************************************
         * ROUTE for common api of training-type
         * start with /api/training-type
         ************************************/
        elseif ($commandArray[0] == 'training-type') {
            // Get all training-type
            // api/training-type GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'DictManagerController';
                $ret['actionName'] = 'getTrainingType';
            }

            // Get a training-type
            // api/training-type/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'DictManagerController';
                $ret['actionName'] = 'getTrainingTypeById';
                $ret['parameters']['id'] = $commandArray[1];
            }
        }

        /*************************************
         * ROUTE for common api of training-level
         * start with /api/training-level
         ************************************/
        elseif ($commandArray[0] == 'training-level') {
            // Get all training-level
            // api/training-level GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'DictManagerController';
                $ret['actionName'] = 'getTrainingLevel';
            }

            // Get a training-level
            // api/training-level/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'superuser';
                $ret['controllerName'] = 'DictManagerController';
                $ret['actionName'] = 'getTrainingLevelById';
                $ret['parameters']['id'] = $commandArray[1];
            }
        }

        /***************************
         * ROUTE for faculty
         * start with /api/faculty
         **************************/
        elseif ($commandArray[0] == 'faculty') {
            // Get a faculty
            // api/faculty/<id> GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyController';
                $ret['actionName'] = 'getFacultyById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // Update faculty information
            // api/faculty/<id> PUT
            elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyController';
                $ret['actionName'] = 'updateFacultyById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // not match
            else {
                return array();
            }
        }

        /*******************************
         * ROUTE for faculty admin
         * start with /api/admin
         ******************************/
        elseif ($commandArray[0] == 'admin') {

            /*********************************
            * ROUTE for admin edit officer/lecture
            * start with /api/admin/officer
            ********************************/
            // Add a officer as faculty admin
            // api/faculty-admin/add-officer-admin POST
            if ($_SERVER['REQUEST_METHOD'] == 'POST'
                && $commandArray[1] == 'add-officer-admin') {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyAdminController';
                $ret['actionName'] = 'addOfficerAdmin';
            }

            // Import a list of officers in faculty
            // api/admin/import-officer POST
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
                && $commandArray[1] == 'import-officer') {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyAdminController';
                $ret['actionName'] = 'importOfficer';
            }

            // Set a lecture as an admin of faculty
            // api/admin/set-officer-admin POST
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
                && $commandArray[1] == 'set-officer-admin'
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyAdminController';
                $ret['actionName'] = 'setOfficerAdmin';
            }

            // Remove admin right of a lecture
            // api/admin/remove-officer-admin POST
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
                && $commandArray[1] == 'remove-officer-admin'
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyAdminController';
                $ret['actionName'] = 'removeOfficerAdmin';
            }

            // Set number of topic
            // api/admin/lecture/<id>/set-norm PUT
            elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                && $commandArray[1] == 'lecture'
                && count($commandArray) == 4
                && $commandArray[3] == 'set-norm'
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyAdminController';
                $ret['actionName'] = 'setNormLecture';
                $ret['parameters']['id'] = $commandArray[2];
            }

            /*************************************
             * ROUTE for admin edit officer
             * start with /api/admin/officer
             ************************************/
            elseif($commandArray[1] == 'officer'){
                // Get officers's public information in faculty
                // api/admin/officer GET
                if ($_SERVER['REQUEST_METHOD'] == 'GET'
                    && count($commandArray) == 2) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminGetOfficers';
                }

                // Get an officer's public information in faculty by Id
                // api/admin/officer/<id> GET
                elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                    && count($commandArray) == 3) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminGetOfficerById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Update an officer's public information in faculty by Id
                // api/admin/officer/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                    && count($commandArray) == 3) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminUpdateOfficerById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Update an officer's public information in faculty by Id
                // api/admin/officer/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                    && count($commandArray) == 3) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminRemoveOfficer';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                //Not match
                else {
                    return array();
                }
            }

            /*************************************
             * ROUTE for admin edit officer
             * start with /api/admin/out-officer
             ************************************/
            elseif ($commandArray[1] == 'out-officer') {
                // Learner import new out officer
                // api/admin/out-officer POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminAddOutOfficer';
                }

                // Delete out officer(s) by id
                // api/admin/out-officer DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminRemoveOutOfficer';
                }
            }

            /*************************************
            * ROUTE for admin edit learner
            * start with /api/admin/learner
            ************************************/

            // Add learner(s) to faculty
            // api/admin/import-learner POST
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
                && $commandArray[1] == 'import-learner') {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'FacultyAdminController';
                $ret['actionName'] = 'importLearner';
            }

            elseif ($commandArray[1] == 'learner'){
                // Get learners's public information in faculty
                // api/admin/learner GET
                if ($_SERVER['REQUEST_METHOD'] == 'GET'
                    && count($commandArray) == 2) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminGetLearners';
                }

                // Get an learner's public information in faculty by Id
                // api/admin/learner/<id> GET
                elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                    && count($commandArray) == 3) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminGetLearnerById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Update an learner's public information in faculty by Id
                // api/admin/learner/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                    && count($commandArray) == 3) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminUpdateLearnerById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Update an learner's public information in faculty by Id
                // api/admin/learner/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                    && count($commandArray) == 3) {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'FacultyAdminController';
                    $ret['actionName'] = 'adminRemoveLearner';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                //not match
                else {
                    return array();
                }
            }

            /*********************************
            * ROUTE for admin edit department
            * start with /api/admin/department
            ********************************/

            elseif($commandArray[1] == 'department'){
                // Add a department
                // api/admin/department POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'DepartmentController';
                    $ret['actionName'] = 'addDepartment';
                }

                // Update department information
                // api/admin/department/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'DepartmentController';
                    $ret['actionName'] = 'updateDepartmentById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Delete department
                // api/admin/department/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'DepartmentController';
                    $ret['actionName'] = 'deleteDepartmentById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                 // not match
                else {
                    return array();
                }
            }

            /*********************************
            * ROUTE for admin edit training-program
            * start with /api/admin/training-program
            ********************************/
            elseif ($commandArray[1] == 'training-program'){
                // Add a training-program
                // api/admin/training-program POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingProgramController';
                    $ret['actionName'] = 'addTrainingProgram';
                }

                // Update program information
                // api/admin/training-program/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingProgramController';
                    $ret['actionName'] = 'updateTrainingProgramById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Delete program
                // api/admin/training-program/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingProgramController';
                    $ret['actionName'] = 'deleteTrainingProgramById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /*********************************
            * ROUTE for admin edit training-area
            * start with /api/admin/training-area
            ********************************/
            elseif ($commandArray[1] == 'training-area'){
                // Add a training-area
                // api/admin/training-area POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingAreaController';
                    $ret['actionName'] = 'addTrainingArea';
                }

                // Update training-area information
                // api/admin/training-area/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingAreaController';
                    $ret['actionName'] = 'updateTrainingAreaById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Remove training-area
                // api/admin/training-area/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingAreaController';
                    $ret['actionName'] = 'deleteTrainingAreaById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /*********************************
            * ROUTE for admin edit training-course
            * start with /api/admin/training-course
            ********************************/
            elseif ($commandArray[1] == 'training-course'){
                // Add a training-course
                // api/admin/training-course POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingCourseController';
                    $ret['actionName'] = 'addTrainingCourse';
                }

                // Update training-course information
                // api/admin/training-course/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingCourseController';
                    $ret['actionName'] = 'updateTrainingCourseById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Delete training-course
                // api/admin/training-course/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'TrainingCourseController';
                    $ret['actionName'] = 'deleteTrainingCourseById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /*********************************
            * ROUTE for admin edit knowledge-area
            * start with /api/admin/knowledge-area
            ********************************/
            elseif ($commandArray[1] == 'knowledge-area'){
                // Add a knowledge-area
                // api/knowledge-area POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'KnowledgeAreaController';
                    $ret['actionName'] = 'addKnowledgeArea';
                }

                // Update knowledge-area information
                // api/admin/knowledge-area/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'KnowledgeAreaController';
                    $ret['actionName'] = 'updateKnowledgeAreaById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Disable department
                // api/admin/knowledge-area/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'faculty';
                    $ret['controllerName'] = 'KnowledgeAreaController';
                    $ret['actionName'] = 'deleteKnowledgeAreaById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
            * ROUTE for admin edit topic
            * start with /api/admin/topic/import-lc
            ************************/
            elseif ($commandArray[1] == 'topic') {
                // Initialize a topic
                // api/admin/topic/import-lc POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
                    $commandArray[2] == 'import-lc')
                {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'adminImportLearnerCodeTopic';
                }

                // Delete a topic Actually cancel this topic (status -1)
                // api/admin/topic/<id> DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'adminDeleteTopic';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // Admin update topic
                // api/admin/topic/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'adminUpdateTopic';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
            * ROUTE for admin edit announcement
            * start with /api/admin/announcement
            ************************/
            elseif($commandArray[1] == 'announcement'){
                // Get all announcement
                // api/admin/announcement GET
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'AnnouncementController';
                    $ret['actionName'] = 'adminGetAnnouncement';
                }

                // create a announcement
                // api/admin/announcement POST
                elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'AnnouncementController';
                    $ret['actionName'] = 'adminCreateAnnouncement';
                }

                // update a announcement
                // api/admin/announcement/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'AnnouncementController';
                    $ret['actionName'] = 'adminUpdateAnnouncementById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // delete a announcement
                // api/admin/announcement DELETE
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'AnnouncementController';
                    $ret['actionName'] = 'adminDeleteAnnouncement';
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
            * ROUTE for admin edit document
            * start with /api/admin/document
            ************************/
            elseif($commandArray[1] == 'document'){
                // Get all document
                // api/admin/document GET
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'DocumentController';
                    $ret['actionName'] = 'adminGetDocument';
                }

                // update a document
                // api/admin/document/<id> PUT
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'common';
                    $ret['controllerName'] = 'DocumentController';
                    $ret['actionName'] = 'adminUpdateDocumentById';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
             * ROUTE for admin edit request-change
             * start with /api/admin/request-change
             ************************/
            elseif($commandArray[1] == 'request-change'){
                // Get all request-change
                // api/admin/request-change PUT
                if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'adminManageRequestChangeSession';
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
             * ROUTE for admin edit request-protect
             * start with /api/admin/request-protect
             ************************/
            elseif($commandArray[1] == 'request-protect'){
                // Get all request-protect
                // api/admin/request-protect PUT
                if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'adminManageRequestProtectSession';
                }

                // not match
                else {
                    return array();
                }
            }

            // not match
            else {
                return array();
            }
        }

        /*********************************
         * ROUTE for admin edit department
         * start with /api/department
         ********************************/
        elseif ($commandArray[0] == 'department') {
            // Get all department
            // api/department[?page=<int>[&limit=<int>][&order=<field>[&DESC=true]]] GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'DepartmentController';
                $ret['actionName'] = 'getDepartment';
            }

            // Get a department
            // api/department/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'DepartmentController';
                $ret['actionName'] = 'getDepartmentById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            /*********************************
            * ROUTE for HEAD of deaprtment with topic
            ********************************/
            elseif(count($commandArray) == 3
                && $commandArray[1] == 'review') {
                    // Delete a review by id
                    // api/department/review/<id> DELETE
                    if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                        $ret['moduleName'] = 'department';
                        $ret['controllerName'] = 'ReviewController';
                        $ret['actionName'] = 'deleteReviewById';
                         $ret['parameters']['id'] = $commandArray[2];
                    }

                    // not match
                    else {
                        return array();
                    }
                }

            // not match
            else {
                return array();
            }
        }


        /******************************
         * ROUTE for admin edit program
         * start with /api/training-program
         *****************************/
        elseif ($commandArray[0] == 'training-program') {
            // Get all training-program
            // api/training-program GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'TrainingProgramController';
                $ret['actionName'] = 'getTrainingProgram';
            }

            // Get a training-program
            // api/training-program/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'TrainingProgramController';
                $ret['actionName'] = 'getTrainingProgramById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // not match
            else {
                return array();
            }
        }


        /**************************************
         * ROUTE for admin edit training-area
         * start with /api/training-area
         *************************************/
        elseif ($commandArray[0] == 'training-area') {
            // Get all training-course
            // api/training-area GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'TrainingAreaController';
                $ret['actionName'] = 'getTrainingArea';
            }

            // Get a training-area
            // api/training-area/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'TrainingAreaController';
                $ret['actionName'] = 'getTrainingAreaById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // not match
            else {
                return array();
            }
        }

        /******************************
         * ROUTE for admin edit training-course
         * start with /api/training-course
         *****************************/
        elseif ($commandArray[0] == 'training-course') {
            // Get all training-course
            // api/training-course[?page=<int>[&limit=<int>][&order=<field>[&DESC=true]]] GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'TrainingCourseController';
                $ret['actionName'] = 'getTrainingCourse';
            }

            // Get a training-course
            // api/training-course/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'TrainingCourseController';
                $ret['actionName'] = 'getTrainingCourseById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // not match
            else {
                return array();
            }
        }

        /*************************************
         * ROUTE for admin edit knowledge area
         * start with /api/knowledge-area
         ************************************/
        elseif ($commandArray[0] == 'knowledge-area') {
            // Get all knowledge-area
            // api/knowledge-area GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'KnowledgeAreaController';
                $ret['actionName'] = 'getKnowledgeArea';
            }

            // Get a knowledge-area
            // api/knowledge-area/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'KnowledgeAreaController';
                $ret['actionName'] = 'getKnowledgeAreaById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // get all knowledge area of officer
            // api/knowledge-area//<id>/officer GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 3
                && $commandArray[2] == 'officer'
            ) {
                $ret['moduleName'] = 'faculty';
                $ret['controllerName'] = 'KnowledgeAreaController';
                $ret['actionName'] = 'getAreaOfficers';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // not match
            else {
                return array();
            }
        }

        /*************************
         * ROUTE for officer
         * start with /api/officer
         ************************/
        elseif ($commandArray[0] == 'officer') {
            // Get all officers's public information
            // api/officer GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'getOfficer';
            }
            // Get a officer
            // api/officer/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'getOfficerById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // Update a officer information
            // api/officer/<id> PUT
            elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                && count($commandArray) == 2
                && $commandArray[1] != 'remove-avatar'
                && $commandArray[1] != 'review'
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'updateOfficerById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // upload new avatar of officer
            // api/officer/upload-avatar POST
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
                && count($commandArray) == 2
                && $commandArray[1] == 'upload-avatar'
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'uploadOfficerAvatar';
                $ret['uploadFile'] = true;
            }

            // remove old avatar of officer
            // api/officer/remove-avatar PUT
            elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                && count($commandArray) == 2
                && $commandArray[1] == 'remove-avatar'
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'removeOfficerAvatar';
            }

            // update knowledge area of officer
            // api/officer/knowledge-area POST
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
                && count($commandArray) == 2
                && $commandArray[1] == 'knowledge-area'
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'updateOfficerAreas';
            }

            // get all knowledge area of officer
            // api/officer/<id>/knowledge-area GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 3
                && $commandArray[2] == 'knowledge-area'
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'getOfficerAreas';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // update knowledge area of officer
            // api/officer/knowledge-area POST
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST'
                && count($commandArray) == 2
                && $commandArray[1] == 'knowledge-area'
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'updateOfficerAreas';
            }

            // Remove knowledge area of officer
            // api/officer/knowledge-area/<id> DELETE
            elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                && count($commandArray) == 3
                && $commandArray[1] == 'knowledge-area'
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'deleteOfficerArea';
                $ret['parameters']['areaId'] = $commandArray[2];
            }

            // get all topics of officer by id
            // api/officer/<id>/topic GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 3
                && $commandArray[2] == 'topic'
            ) {
                $ret['moduleName'] = 'topic';
                $ret['controllerName'] = 'TopicController';
                $ret['actionName'] = 'getOfficerTopics';
                $ret['parameters']['id'] = $commandArray[1];
            }

            /*************************
            * ROUTE for officer
            * start with /api/officer/review
            ************************/
            elseif(count($commandArray) == 2
                && $commandArray[1] == 'review'
            ) {
                if($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'officer';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'officerReviewTopic';
                }
            }

            // not match
            else {
                return array();
            }
        }

        /*************************
         * ROUTE for attachment
         * start with /api/out-officer
         ************************/
        elseif ($commandArray[0] == 'out-officer') {
            // Learner import new out officer
            // api/out-officer POST
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'addOutOfficer';
            }
            // Get all out officer
            // api/out-officer GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'getOutOfficer';
            }
            // Get out officer by id
            // api/out-officer/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'officer';
                $ret['controllerName'] = 'OfficerController';
                $ret['actionName'] = 'getOutOfficerById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // not match
            else {
                return array();
            }
        }

        /*************************
         * ROUTE for learner
         * start with /api/learner
         ************************/
        elseif ($commandArray[0] == 'learner') {
            // Get a learner
            // api/learner/<id> GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'learner';
                $ret['controllerName'] = 'LearnerController';
                $ret['actionName'] = 'getLearnerById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // Update learner information
            // api/learner/<id> PUT
            elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'learner';
                $ret['controllerName'] = 'LearnerController';
                $ret['actionName'] = 'updateLearnerById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // Leaner register a topic
            // api/learner/topic/<id> PUT

            elseif($commandArray[1] == 'topic'){
                if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'learnerRegisterTopic';
                    $ret['parameters']['id'] = $commandArray[2];
                }

                else {
                    return array();
                }
            }

            // get all topic of learner
            // api/learner/<id>/topic GET
            elseif($commandArray[2] == 'topic'
                    && count($commandArray) == 3){
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'getLearnerTopics';
                    $ret['parameters']['id'] = $commandArray[1];
                }

                else {
                    return array();
                }
            }


             // Learner upload register attachment
            // api/learner/attachment-register POST
            else if( $commandArray[1] == 'attachment-register' &&
                    $_SERVER['REQUEST_METHOD'] == 'POST') {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'DocumentController';
                $ret['actionName'] = 'learnerUploadRegisterAttachment';
                $ret['uploadFile'] = true;
            }

            // not match
            else {
                return array();
            }
        }

        /******************************
         * ROUTE for common announcement api
         * start with /api/announcement
         *****************************/
        elseif ($commandArray[0] == 'announcement') {
            // Get all announcement
            // api/announcement GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'AnnouncementController';
                $ret['actionName'] = 'getAnnouncement';
                $ret['needAuthen'] = false;
            }

            // Get a announcement by id
            // api/announcement/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'AnnouncementController';
                $ret['actionName'] = 'getAnnouncementById';
                $ret['parameters']['id'] = $commandArray[1];
                $ret['needAuthen'] = false;
            }

            // not match
            else {
                return array();
            }
        }

        /*************************
         * ROUTE for step
         * start with /api/step
         ************************/
        elseif ($commandArray[0] == 'step') {
            // Get all step
            // api/step GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'StepController';
                $ret['actionName'] = 'getStep';
            }

            // Get a step
            // api/step/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'StepController';
                $ret['actionName'] = 'getStepById';
                $ret['parameters']['id'] = $commandArray[1];
            }
        }

        /*************************
         * ROUTE for topic
         * start with /api/topic
         ************************/
        elseif ($commandArray[0] == 'topic') {
            // Get all topic
            // api/topic[?page=<int>[&limit=<int>][&order=<field>[&DESC=true]]] GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 1
            ) {
                $ret['moduleName'] = 'topic';
                $ret['controllerName'] = 'TopicController';
                $ret['actionName'] = 'getTopic';
            }

            // Get a topic
            // api/topic/<id> GET
            elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                && count($commandArray) == 2
            ) {
                $ret['moduleName'] = 'topic';
                $ret['controllerName'] = 'TopicController';
                $ret['actionName'] = 'getTopicById';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // Create a activity
            // api/topic/activity POST
            elseif ($commandArray[1] == 'activity' &&
                    $_SERVER['REQUEST_METHOD'] == 'POST'
                    && count($commandArray) == 2
                ){
                $ret['moduleName'] = 'topic';
                $ret['controllerName'] = 'ActivityController';
                $ret['actionName'] = 'createActivity';
            }

            /*************************
            * ROUTE for topic - activity
            * start with /api/topic/<id>/activity
            ************************/
            elseif ($commandArray[2] == 'activity') {
                // Get all activity
                // api/topic/<id>/activity GET
                if ($_SERVER['REQUEST_METHOD'] == 'GET'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'ActivityController';
                    $ret['actionName'] = 'getTopicActivities';
                    $ret['parameters']['topicId'] = $commandArray[1];
                }

                // Get a activity by id
                // api/topic/<id>/activity/<id> GET
                elseif ($_SERVER['REQUEST_METHOD'] == 'GET'
                    && count($commandArray) == 4
                ) {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'ActivityController';
                    $ret['actionName'] = 'getTopicActivityById';
                    $ret['parameters']['topicId'] = $commandArray[1];
                    $ret['parameters']['actionId'] = $commandArray[3];
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
             * ROUTE for topic - activity
             * start with /api/topic/<id>/request-change
             ************************/
            elseif ($commandArray[2] == 'request-change') {
                // Get a request by id
                // api/topic/<id>/request-change GET
                if ($_SERVER['REQUEST_METHOD'] == 'GET'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'getRequestChangeById';
                    $ret['parameters']['id'] = $commandArray[1];
                }

                // Update a request by id
                // api/topic/<id>/request-change GET
                elseif ($_SERVER['REQUEST_METHOD'] == 'PUT'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'learnerUpdateRequestChangeById';
                    $ret['parameters']['id'] = $commandArray[1];
                }

                // Delete a request by id
                // api/topic/<id>/request-change GET
                elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE'
                    && count($commandArray) == 3
                ) {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'deleteRequestChangeById';
                    $ret['parameters']['id'] = $commandArray[1];
                }

                // not match
                else {
                    return array();
                }
            }

            /*************************
             * ROUTE for topic - request-pause
             * start with /api/topic/request-pause
             ************************/
            elseif($commandArray[1] == 'request-pause'){
                if($_SERVER['REQUEST_METHOD'] == 'POST'
                    && count($commandArray) == 2)
                {
                    $ret['moduleName'] = 'topic';
                    $ret['controllerName'] = 'TopicController';
                    $ret['actionName'] = 'changeTopicStatusRequestPause';
                }

                // not match
                else {
                    return array();
                }
            }

            // not match
            else {
                return array();
            }
        }

        /*************************
         * ROUTE for document
         * start with /api/document
         ************************/
        elseif ($commandArray[0] == 'document') {
            // Get all documents
            // api/document GET
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'DocumentController';
                $ret['actionName'] = 'addDocument';
            }

            // Get all topic related to document by id
            // api/document/<id>/topic
            else if ( count($commandArray) == 3 &&
                $commandArray[2] == 'topic' &&
                $_SERVER['REQUEST_METHOD'] == 'GET') 
            {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'DocumentController';
                $ret['actionName'] = 'getTopicsByDocumentId';
                $ret['parameters']['id'] = $commandArray[1];
            }

            // not match
            else {
                return array();
            }
        }

        /*************************
         * ROUTE for attachment
         * start with /api/attachment
         ************************/
        elseif ($commandArray[0] == 'attachment') {
            // Upload a attachment
            // api/attachment POST
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'DocumentController';
                $ret['actionName'] = 'uploadAttachment';
                $ret['uploadFile'] = true;
            }

            // not match
            else {
                return array();
            }
        }

        elseif ($commandArray[0] == 'quota') {
            // Get a quota
            // api/quota GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $ret['moduleName'] = 'common';
                $ret['controllerName'] = 'QuotaController';
                $ret['actionName'] = 'getQuota';
            }

            else {
                return array();
            }
        }

        // not match
        else {
            return array();
        }

        return $ret;
    }
}
