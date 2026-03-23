-- SQLite
UPDATE users SET roles = json_array('patineur', 'admin') WHERE username = 'SarahTordeur';
