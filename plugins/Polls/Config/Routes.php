<?php

namespace Config;

$routes = Services::routes();

$polls_namespace = ['namespace' => 'Polls\Controllers'];

$routes->get('polls', 'Polls::index', $polls_namespace);
$routes->post('polls/(:any)', 'Polls::$1', $polls_namespace);
$routes->get('polls/(:any)', 'Polls::$1', $polls_namespace);

$routes->get('nps', 'Nps::index', $polls_namespace);

// public routes should be defined before catch-all to prevent overrides
$routes->get('nps/s/(:num)', 'Nps_public::view/$1', $polls_namespace);
$routes->get('nps/embed/(:num)', 'Nps_public::embed/$1', $polls_namespace);
$routes->post('nps/submit', 'Nps_public::submit', $polls_namespace);

// internal NPS routes
$routes->post('nps/(:any)', 'Nps::$1', $polls_namespace);
$routes->get('nps/(:any)', 'Nps::$1', $polls_namespace);

$routes->get('poll_settings', 'Poll_settings::index', $polls_namespace);
$routes->post('poll_settings/(:any)', 'Poll_settings::$1', $polls_namespace);
$routes->get('poll_settings/(:any)', 'Poll_settings::$1', $polls_namespace);


$routes->get('poll_updates', 'Poll_Updates::index', $polls_namespace);
$routes->get('poll_updates/(:any)', 'Poll_Updates::$1', $polls_namespace);
