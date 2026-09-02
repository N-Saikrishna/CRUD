USE COP4331;

INSERT INTO Users (FirstName, LastName, Login, Password) VALUES
  ('Demo', 'User', 'demo',   '$2y$10$djASruchNW4Qgk9DLqqbYeAa687RkldoITo7GvWMdsO7wy9B/bnlK'),
  ('Test', 'User', 'tester', '$2y$10$djASruchNW4Qgk9DLqqbYeAa687RkldoITo7GvWMdsO7wy9B/bnlK');

INSERT INTO Contacts (UserID, FirstName, LastName, Phone, Email) VALUES
  (1, 'Pranav',   'Jones',      '4075550101', 'pranav@example.com'),
  (1, 'Sai',      'Smith',      '4075550102', 'sai@example.com'),
  (1, 'Haren',    'Jobs',       '4075550103', 'haren@example.com'),
  (1, 'Kareem',   'Johnson',    '4075550104', 'kareem@example.com'),
  (1, 'Devam',    'Patel',      '4075550105', 'devam@example.com'),
  (1, 'Mohammed', 'Doe',        '4075550106', 'mohammed@example.com');

INSERT INTO Contacts (UserID, FirstName, LastName, Phone, Email) VALUES
  (2, 'Private', 'Contact', '4075550999', 'private@example.com');

INSERT INTO Sessions (Token, UserID, ExpiresAt) VALUES
  ('devtoken0000000000000000000000000000000000000000000000000000face', 1, '2030-01-01 00:00:00');
