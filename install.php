<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$pdo = get_db();

$statements = [
"CREATE TABLE IF NOT EXISTS sites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL
)",
"CREATE TABLE IF NOT EXISTS groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    schedule VARCHAR(255),
    price_per_season REAL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    roles TEXT DEFAULT '[\"patineur\"]',
    is_active INTEGER DEFAULT 1,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    phone VARCHAR(20),
    address TEXT,
    emergency_contacts TEXT DEFAULT '[]',
    license_number VARCHAR(50) UNIQUE,
    group_id INTEGER REFERENCES groups(id) ON DELETE SET NULL,
    status VARCHAR(20) DEFAULT 'active',
    joined_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    registration_year INTEGER DEFAULT (CAST(strftime('%Y','now') AS INTEGER))
)",
"CREATE TABLE IF NOT EXISTS user_sites (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    site_id INTEGER REFERENCES sites(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, site_id)
)",
"CREATE TABLE IF NOT EXISTS parent_child (
    parent_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    child_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (parent_id, child_id)
)",
"CREATE TABLE IF NOT EXISTS training_sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    date DATETIME NOT NULL,
    duration_minutes INTEGER DEFAULT 60,
    coach_notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS attendances (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    session_id INTEGER REFERENCES training_sessions(id),
    session_date DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'present',
    notes TEXT,
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    recorded_by_id INTEGER REFERENCES users(id),
    site_id INTEGER REFERENCES sites(id)
)",
"CREATE TABLE IF NOT EXISTS payment_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    amount REAL NOT NULL,
    payment_type VARCHAR(50),
    payment_method VARCHAR(50),
    payment_date DATETIME NOT NULL,
    due_date DATETIME,
    status VARCHAR(20) DEFAULT 'paid',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS season_payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    season_year INTEGER NOT NULL,
    licence_due REAL DEFAULT 0,
    saison_tournai_due REAL DEFAULT 0,
    wasquehal_p1_due REAL DEFAULT 0,
    wasquehal_p2_due REAL DEFAULT 0,
    competitions_due REAL DEFAULT 0,
    licence_paid REAL DEFAULT 0,
    saison_tournai_paid REAL DEFAULT 0,
    wasquehal_p1_paid REAL DEFAULT 0,
    wasquehal_p2_paid REAL DEFAULT 0,
    competitions_paid REAL DEFAULT 0,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, season_year)
)",
"CREATE TABLE IF NOT EXISTS reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    report_type VARCHAR(50),
    data TEXT,
    generated_by_id INTEGER REFERENCES users(id),
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)",
];

foreach ($statements as $sql) {
    $pdo->exec($sql);
}

$count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($count === 0) {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO users (username,email,password_hash,first_name,last_name,roles) VALUES (?,?,?,?,?,?)")
        ->execute(['admin', 'admin@axelclub.be', $hash, 'Admin', 'Club', '["admin"]']);
    echo "<p>✅ Compte admin créé : <strong>admin@axelclub.be</strong> / <strong>admin123</strong> — <em>Changez ce mot de passe immédiatement !</em></p>";
}

echo "<p>✅ Base de données initialisée avec succès.</p>";
echo "<p><strong>Supprimez ce fichier install.php maintenant !</strong></p>";
