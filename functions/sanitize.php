<?php
// Sanitize data when going in and out of database.
// Make system as secure as possible when it comes to escaping.
function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8'); // escape single & double quotes
}