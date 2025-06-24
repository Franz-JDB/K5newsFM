<?php
/**
 * Enable Database Extensions Script
 * This script automatically enables mysqli and pdo_mysql extensions
 */

echo "=== Enable Database Extensions ===\n\n";

// Get the php.ini file path
$php_ini_path = php_ini_loaded_file();

if (!$php_ini_path) {
    die("Error: Could not find php.ini file\n");
}

echo "PHP INI file: $php_ini_path\n\n";

// Check if file is writable
if (!is_writable($php_ini_path)) {
    echo "⚠ Warning: php.ini file is not writable\n";
    echo "You may need to run this script as administrator\n\n";
    
    echo "Manual steps to enable extensions:\n";
    echo "1. Open the file: $php_ini_path\n";
    echo "2. Find these lines:\n";
    echo "   ;extension=mysqli\n";
    echo "   ;extension=pdo_mysql\n";
    echo "3. Remove the semicolons to make them:\n";
    echo "   extension=mysqli\n";
    echo "   extension=pdo_mysql\n";
    echo "4. Save the file and restart Apache\n\n";
    
    exit(1);
}

// Read the current php.ini content
$content = file_get_contents($php_ini_path);
if ($content === false) {
    die("Error: Could not read php.ini file\n");
}

echo "Reading php.ini file...\n";

// Check current status
$mysqli_enabled = strpos($content, 'extension=mysqli') !== false && strpos($content, ';extension=mysqli') === false;
$pdo_mysql_enabled = strpos($content, 'extension=pdo_mysql') !== false && strpos($content, ';extension=pdo_mysql') === false;

echo "Current status:\n";
echo "  mysqli: " . ($mysqli_enabled ? "✓ Enabled" : "✗ Disabled") . "\n";
echo "  pdo_mysql: " . ($pdo_mysql_enabled ? "✓ Enabled" : "✗ Disabled") . "\n\n";

if ($mysqli_enabled && $pdo_mysql_enabled) {
    echo "✓ Both extensions are already enabled!\n";
    echo "Restart Apache to apply changes.\n";
    exit(0);
}

// Enable extensions
$changes_made = false;

// Enable mysqli
if (!$mysqli_enabled) {
    $content = preg_replace('/;extension=mysqli/', 'extension=mysqli', $content);
    echo "✓ Enabled mysqli extension\n";
    $changes_made = true;
}

// Enable pdo_mysql
if (!$pdo_mysql_enabled) {
    $content = preg_replace('/;extension=pdo_mysql/', 'extension=pdo_mysql', $content);
    echo "✓ Enabled pdo_mysql extension\n";
    $changes_made = true;
}

if ($changes_made) {
    // Write the modified content back
    if (file_put_contents($php_ini_path, $content)) {
        echo "\n✓ Successfully updated php.ini file\n";
        echo "⚠ IMPORTANT: You must restart Apache for changes to take effect!\n\n";
        
        echo "To restart Apache:\n";
        echo "1. Open XAMPP Control Panel\n";
        echo "2. Click 'Stop' next to Apache\n";
        echo "3. Wait 5 seconds\n";
        echo "4. Click 'Start' next to Apache\n\n";
        
        echo "After restarting Apache, run:\n";
        echo "php test_setup.php\n";
        
    } else {
        echo "\n✗ Failed to write php.ini file\n";
        echo "You may need to run this script as administrator\n";
    }
} else {
    echo "No changes needed.\n";
}

echo "\n=== Script Complete ===\n";
?> 