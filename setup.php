<?php
/**
 * EQ Test Platform Setup Script
 * This script helps initialize the database and verify the installation
 */

// Database configuration
$host = 'localhost';
$dbname = 'db5xpbtbmv9ulr';
$username = 'ukrfhh29eellf';
$password = 'jua2ursxz7gb';

echo "<h1>EQ Test Platform Setup</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:40px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

try {
    // Test database connection
    echo "<h2>1. Testing Database Connection</h2>";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Check if tables exist
    echo "<h2>2. Checking Database Tables</h2>";
    $tables = ['questions', 'answers', 'test_sessions', 'user_responses'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<p class='success'>✅ Table '$table' exists</p>";
                $existing_tables[] = $table;
            } else {
                echo "<p class='error'>❌ Table '$table' does not exist</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Error checking table '$table': " . $e->getMessage() . "</p>";
        }
    }
    
    // Check if questions exist
    echo "<h2>3. Checking Sample Data</h2>";
    if (in_array('questions', $existing_tables)) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
            $count = $stmt->fetch()['count'];
            if ($count > 0) {
                echo "<p class='success'>✅ Found $count questions in database</p>";
            } else {
                echo "<p class='error'>❌ No questions found in database</p>";
                echo "<p class='info'>💡 Please import the database_schema.sql file to add sample questions</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Error checking questions: " . $e->getMessage() . "</p>";
        }
    }
    
    // Test file permissions
    echo "<h2>4. Checking File Permissions</h2>";
    $files_to_check = [
        'index.php' => 'Homepage',
        'quiz.php' => 'Quiz page',
        'results.php' => 'Results page',
        'config/database.php' => 'Database config',
        'assets/css/style.css' => 'Stylesheet'
    ];
    
    foreach ($files_to_check as $file => $description) {
        if (file_exists($file)) {
            if (is_readable($file)) {
                echo "<p class='success'>✅ $description ($file) is readable</p>";
            } else {
                echo "<p class='error'>❌ $description ($file) is not readable</p>";
            }
        } else {
            echo "<p class='error'>❌ $description ($file) not found</p>";
        }
    }
    
    // PHP version check
    echo "<h2>5. PHP Configuration</h2>";
    echo "<p class='info'>PHP Version: " . phpversion() . "</p>";
    
    if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
        echo "<p class='success'>✅ PHP version is compatible (7.4+)</p>";
    } else {
        echo "<p class='error'>❌ PHP version is too old. Please upgrade to 7.4 or higher</p>";
    }
    
    // Check required extensions
    $required_extensions = ['pdo', 'pdo_mysql', 'session'];
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext)) {
            echo "<p class='success'>✅ $ext extension is loaded</p>";
        } else {
            echo "<p class='error'>❌ $ext extension is not loaded</p>";
        }
    }
    
    echo "<h2>6. Setup Summary</h2>";
    if (count($existing_tables) === count($tables) && $count > 0) {
        echo "<p class='success'><strong>🎉 Setup Complete! Your EQ Test Platform is ready to use.</strong></p>";
        echo "<p><a href='index.php' style='background:#667eea;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Go to EQ Test</a></p>";
    } else {
        echo "<p class='error'><strong>⚠️ Setup Incomplete. Please address the issues above.</strong></p>";
        echo "<p class='info'>💡 <strong>Quick Fix:</strong> Import the database_schema.sql file into your database to complete the setup.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<h2>Troubleshooting</h2>";
    echo "<ul>";
    echo "<li>Check your database credentials in config/database.php</li>";
    echo "<li>Ensure MySQL server is running</li>";
    echo "<li>Verify the database exists</li>";
    echo "<li>Check user permissions</li>";
    echo "</ul>";
}

echo "<h2>Alternative: Single-File Version</h2>";
echo "<p class='info'>If you're having trouble with the database setup, you can use the single-file version:</p>";
echo "<p><a href='eq_test_single_file.php' style='background:#38b2ac;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Use Single-File Version</a></p>";

echo "<hr>";
echo "<p><small>EQ Test Platform Setup Script - " . date('Y-m-d H:i:s') . "</small></p>";
?>
