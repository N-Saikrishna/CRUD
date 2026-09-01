CREATE USER 'contactdb'@'localhost' IDENTIFIED BY 'secure_password_here';

GRANT SELECT, INSERT, UPDATE, DELETE ON contacts.* TO 'contactdb'@'localhost';
GRANT SELECT ON contacts.Users TO 'contactdb'@'localhost';
GRANT SELECT ON contacts.Contacts TO 'contactdb'@'localhost';
GRANT SELECT ON contacts.Sessions TO 'contactdb'@'localhost';

FLUSH PRIVILEGES;
