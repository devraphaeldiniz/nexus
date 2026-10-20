USE nexus;
ALTER TABLE usuarios ADD COLUMN qr_version INT NOT NULL DEFAULT 1, ADD COLUMN qr_valid_until DATE NULL, ADD COLUMN primeiro_acesso TINYINT(1) NOT NULL DEFAULT 1;
UPDATE usuarios SET qr_valid_until = DATE_ADD(CURDATE(), INTERVAL 365 DAY) WHERE qr_valid_until IS NULL;
