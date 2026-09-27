USE COP4331;

-- Add DateCreated column to Users if not present
SET @dbname = DATABASE();
SET @tablename = 'Users';
SET @columnname = 'DateCreated';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE Users ADD COLUMN DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
));
PREPARE alterUsersIfNotExists FROM @preparedStatement;
EXECUTE alterUsersIfNotExists;
DEALLOCATE PREPARE alterUsersIfNotExists;

-- Add DateCreated column to Contacts if not present
SET @tablename = 'Contacts';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE Contacts ADD COLUMN DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
));
PREPARE alterContactsIfNotExists FROM @preparedStatement;
EXECUTE alterContactsIfNotExists;
DEALLOCATE PREPARE alterContactsIfNotExists;

-- Update existing records to current timestamp
UPDATE Users SET DateCreated = NOW() WHERE DateCreated IS NULL OR DateCreated = '0000-00-00 00:00:00';
UPDATE Contacts SET DateCreated = NOW() WHERE DateCreated IS NULL OR DateCreated = '0000-00-00 00:00:00';
