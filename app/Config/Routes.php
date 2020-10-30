<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->addPlaceholder('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

$routes->get('/', 'Home::index');
$routes->add('posts/account/(:uuid)', 'Home::PostsAccount/$1');

$routes->add('manage', 'Manage::Index');
$routes->add('manage/add-account', 'Manage::AddAccount');
$routes->post('manage/save-account', 'Manage::SaveAccount');
$routes->add('manage/set-tags-account/(:uuid)/(:num)', 'Manage::SetTagsAccount/$1/$2');
$routes->add('manage/get-posts', 'Manage::GetPosts');
$routes->add('manage/get-posts-account/(:uuid)', 'Manage::GetPostsAccount/$1');
$routes->add('manage/select-posts', 'Manage::SelectPosts');
$routes->add('manage/remove-temp-media/(:uuid)', 'Manage::RemoveTempMedia/$1');
$routes->add('manage/remove-post/(:uuid)', 'Manage::RemovePost/$1');
$routes->add('manage/upload-post/(:uuid)', 'Manage::UploadPost/$1');


/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
