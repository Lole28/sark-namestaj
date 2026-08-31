<?php
/** @var Router $router */

/* --- Javne stranice -------------------------------------------------- */
$router->get('/',                 'HomeController@index');
$router->get('/o-nama',           'StraniceController@oNama');
$router->get('/usluge',           'StraniceController@usluge');
$router->get('/kontakt',          'StraniceController@kontakt');

$router->get('/radovi',           'RadoviController@lista');
$router->get('/radovi/{slug}',    'RadoviController@detalj');

/* --- Upit / recenzija (fallback bez JavaScript-a) ------------------- */
$router->post('/upit',            'UpitController@posalji');
$router->post('/recenzija',       'UpitController@recenzija');

/* --- Nalog / autentikacija ---------------------------------------- */
$router->get('/registracija',     'AuthController@registracijaForma');
$router->post('/registracija',    'AuthController@registracija');
$router->get('/prijava',          'AuthController@prijavaForma');
$router->post('/prijava',         'AuthController@prijava');
$router->post('/odjava',          'AuthController@odjava');

$router->get('/nalog/upiti',      'NalogController@upiti');

/* --- Administracija --------------------------------------------------- */
$router->get('/admin',                       'AdminController@pocetna');

$router->get('/admin/kategorije',             'AdminController@kategorije');
$router->get('/admin/kategorije/nova',        'AdminController@kategorijaForma');
$router->post('/admin/kategorije',            'AdminController@sacuvajKategoriju');
$router->get('/admin/kategorije/{id}/izmena', 'AdminController@kategorijaForma');
$router->post('/admin/kategorije/{id}',       'AdminController@azurirajKategoriju');
$router->post('/admin/kategorije/{id}/brisanje', 'AdminController@obrisiKategoriju');

$router->get('/admin/radovi',                 'AdminController@radovi');
$router->get('/admin/radovi/novi',            'AdminController@radForma');
$router->post('/admin/radovi',                'AdminController@sacuvajRad');
$router->get('/admin/radovi/{id}/izmena',     'AdminController@radForma');
$router->post('/admin/radovi/{id}',           'AdminController@azurirajRad');
$router->post('/admin/radovi/{id}/brisanje',  'AdminController@obrisiRad');

$router->get('/admin/upiti',                  'AdminController@upiti');
$router->get('/admin/recenzije',              'AdminController@recenzije');
$router->post('/admin/recenzije/{id}/odobri', 'AdminController@odobriRecenziju');
$router->post('/admin/recenzije/{id}/brisanje','AdminController@obrisiRecenziju');

/* --- Web servisi (JSON API) --------------------------------------- */
$router->get('/api/radovi',           'ApiController@radovi');
$router->get('/api/kurs',             'ApiController@kurs');
$router->post('/api/upiti',           'ApiController@napraviUpit');
$router->post('/api/recenzije',       'ApiController@napraviRecenziju');
$router->post('/api/slike',           'ApiController@otpremiSliku');
$router->post('/api/slike/{id}/brisanje', 'ApiController@obrisiSliku');
$router->patch('/api/upiti/{id}',     'ApiController@promeniStatusUpita');
