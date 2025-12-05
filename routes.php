<?php

require_once __DIR__.'/router.php';

get('/', 'views/index.php');
get('/home', 'views/home.php');
get('/contact-us', 'views/contact-us.php');


get('/person', 'views/person.php');
get('/proxy', 'views/proxy.php');
post('/save', 'save.php');


post('/bridge-login', 'connections/bridge-login.php');
post('/bridge-signup', 'connections/bridge-signup.php');
get('/bridge-logout', 'connections/bridge-logout.php');

//search
post('/bridge-search', 'connections/bridge-search.php');


// API
get('/api-follow', 'api/api-follow.php');
get('/api-unfollow', 'api/api-unfollow.php');

any('/404','views/404.php');
get('/verify-email', 'api/api-verify-email.php');