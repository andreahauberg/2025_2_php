<?php
// toast component 

$message = '';
$type = 'ok';
$ttl = 5000;
// Tjek om der er en toast-besked i sessionen
if (!empty($_SESSION['toast'])) {
	$t = $_SESSION['toast'];
	$message = $t['message'] ?? '';
	$type = (isset($t['type']) && $t['type'] === 'error') ? 'error' : 'ok';
	$ttl = isset($t['ttl']) ? (int)$t['ttl'] : 5000;
	unset($_SESSION['toast']);
}
// Render kun toast, hvis der er en besked
if ($message) {
	$safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
	$safeType = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
// viser toast som HTML
	echo "<div id=\"toast\" class=\"toast-container\">";
	echo "<div class=\"toast toast-{$safeType}\" data-ttl=\"{$ttl}\">{$safe}</div>";
	echo "</div>";
}
