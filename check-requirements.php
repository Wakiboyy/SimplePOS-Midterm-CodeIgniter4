<?php
// Run in the project's root: php check-requirements.php
printf("SimplePOS requirements checker\nPHP version: %s\n", PHP_VERSION);
$required = ['intl', 'mbstring', 'mysqli', 'fileinfo', 'gd'];
$ok = version_compare(PHP_VERSION, '8.2', '>=');
foreach ($required as $extension) {
    $available = extension_loaded($extension);
    printf("%-10s %s\n", strtoupper($extension), $available ? 'ENABLED' : 'MISSING');
    $ok = $ok && $available;
}
echo $ok ? "Ready for CodeIgniter 4 and image uploads.\n" : "Enable missing extensions in XAMPP php.ini, then reopen PowerShell.\n";
exit($ok ? 0 : 1);
