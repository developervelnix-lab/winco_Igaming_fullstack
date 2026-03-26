<?php
/*
 Don't edit this file without developer permission.
 For any help please contact developer here: abcd@gmail.com
*/
header("Access-Control-Allow-Origin: *");

define("ACCESS_SECURITY","true");
include '../security/headers-security.php';


// check for all request headers
$headerObj = new RequestHeaders();
$headerObj -> checkCorsPolicy("GET,POST");
$headerObj -> checkAllHeaders();


// including required files
include '../security/license.php';
include '../security/config.php';
include '../security/constants.php';
include 'route-paths.php';


// setting up empty array
$resArr = array();
$resArr['data'] = array();


// setting real date & time
date_default_timezone_set('Asia/Kolkata');
$curr_date = date("d-m-Y");
$curr_time = date("h:i a");
$curr_date_time = $curr_date.' '.$curr_time;


// validating license
// $licenseObj = new RequestLicense();
// if($licenseObj -> validateLicense()==="true"){

    // Get the requested URL
    $base_url = parse_url($_SERVER['REQUEST_URI'])['path'];
    $route_path = $headerObj->getRoute();
    // normalize route: remove surrounding slashes and whitespace
    if (!empty($route_path)) {
        $route_path = trim($route_path, "\t\n\r\0\x0B /\\");
    }

    // fallback to GET param if header Route not provided
    if (empty($route_path) && isset($_GET['route'])) {
      $route_path = trim($_GET['route'], "/ \t\n\r\0\x0B");
    }
    // special-case status check before route lookup
    if ($route_path === 'status') {
        echo "Routing is working..";
        return;
    }

    $request_uri = '/'.$route_path;
    
    // Check if the requested route exists
    if (array_key_exists($request_uri, $routes)) {
        $request_path = $routes[$request_uri];
    
        if($request_path=="default"){
          echo "invalid_route_request";
          return;
        }

        // Handle the route
        switch ($route_path) {
          case 'status':
            echo "Routing is working..";
            break;

          default:
            include '../'.$request_path;
            break;
        }
    } else {
      // Handle other routes or show a 404 page
      echo "invalid_route_request_1";
    }
// }else{
//     // Handle unauthorized access (e.g., return an error)
//     header("HTTP/1.1 401 Invalid License");
//     exit();
// }


// print_r(parse_url($_SERVER['REQUEST_URI']));
// print_r(apache_request_headers());
// return;
?>
