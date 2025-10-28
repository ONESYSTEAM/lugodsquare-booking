<?php

namespace app;

use app\Controllers\MembershipController;
use app\Controllers\BookingController;
use app\Controllers\EmailController;



class Router
{
    public static $routes = [];

    public static function init()
    {
        // Define routes
        Router::add('/', fn() => (new BookingController())->index());

        Router::add('/membership-registration', fn() => Router::render('Membership-registration'));
        Router::add('/membership-pin', fn() => (new MembershipController())->add(), 'POST');
        Router::add('/member', fn() => (new MembershipController())->setPin(), 'POST');
        Router::add('/confirmation', fn() => Router::render('Confirmation-page'));
        Router::add('/remove-session', fn() => (new MembershipController())->logout());


        Router::add('/check-membership', fn() => (new MembershipController())->checkMembership(), 'POST');
        Router::add('/verify-pin', fn() => (new MembershipController())->checkMembershipPin(), 'POST');
        Router::add('/verify-email', fn() => (new EmailController())->verifyEmail(), 'POST');
        Router::add('/confirm-code', fn() => (new EmailController())->confirmCode(), 'POST');
        Router::add('/get-court-details', fn() => (new BookingController())->getCourtDetails());
        Router::add('/calculate-total', fn() => (new BookingController())->calculateTotal());
        Router::add('/get-booked-slots', fn() => (new BookingController())->getBookedSlots(), 'POST');

        Router::add('/booking', fn() => (new BookingController())->booking(), 'POST');

        // Run the router
        Router::run();
    }

    public static function add($path, $callback)
    {
        $path = str_replace(['{', '}'], ['(?P<', '>[^/]+)'], $path);
        Router::$routes[$path] = $callback;
    }

    public static function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach (self::$routes as $route => $callback) {
            if (preg_match("#^$route$#", $requestUri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                echo call_user_func($callback, $params);
                return;
            }
        }

        echo template()->render('Errors/404');
    }

    public static function render($view, $data = [])
    {
        $viewPath = __DIR__ . "/Views/{$view}.php";
        if (file_exists($viewPath)) {
            $templates = new \League\Plates\Engine(__DIR__ . '/Views');
            echo $templates->render($view, $data);
        } else {
            echo template()->render('Errors/404');
        }
    }
}
