CREATE USER 'contactdb'@'localhost' IDENTIFIED BY 'secure_password_here';

GRANT SELECT, INSERT, UPDATE, DELETE ON COP4331.* TO 'contactdb'@'localhost';

FLUSH PRIVILEGES;
