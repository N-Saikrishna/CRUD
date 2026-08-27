# Team 9 Project Guide

Contact Manager | COP 4331C | Fall 2026

**Find your name. Read your section. Skim the rest.**

---

## What we are building

A website where you make an account, log in, and manage your contacts. Add one, see your list, edit one, delete one, and search by name.

Each contact stores: first name, last name, phone, email, and the date it was created.

## Rules from the assignment

- LAMP only. PHP on the server, MySQL for the database. No Node, no Python.
- The front end talks to the backend through the API. Never straight to MySQL.
- Search matches partial text and ignores capitalization. "jo" finds John, Jones, Jobs.
- No pop-up alerts anywhere except the delete confirmation.
- The site runs on a real remote server.
- Every endpoint documented on SwaggerHub. One demoed live.

---

# Order of work

Some work blocks other work. This is the chain:

**Day 1, in parallel:**
- Jeremy stands up the server
- API A writes `db.php`, API B writes `errors.php`, then they agree on the response shape together
- API B publishes the endpoint contract to Discord

**Day 2:**
- Mohammed builds the schema once Jeremy says MySQL is up
- Front end starts building pages against the contract, no working API needed yet

**Then everyone builds in parallel.**

If you are blocked, say so in Discord. Do not sit quietly waiting.

---

# Jeremy — Server

You are first. Five people are waiting on you.

### 0. Try to get it free first

Before paying for anything, go to `education.github.com/pack` and verify with your knights.ucf.edu email. The GitHub Student Developer Pack includes DigitalOcean credit that covers this project several times over. Verification can take a day or two, so start it now.

If that comes through, the server costs nothing. If it does not, the droplet is about $7/month for two months. Say so in Discord and we will split it seven ways.

### 1. Create the droplet

1. Sign up at digitalocean.com
2. Go to `marketplace.digitalocean.com/apps/lamp`
3. Click **Create LAMP Droplet**
4. Region: New York or Atlanta. Ubuntu. Basic plan, cheapest tier, about $7/mo
5. Authentication: **Password**, not SSH key. Seven people need access.
6. Name it `team9-contactmanager`, create, wait a few minutes

Copy the IP address. Send it plus the root password to Haren by DM. Never in the group channel, never in the repo.

If you ended up paying, post the amount in Discord so everyone can send you their share.

### 2. Get in

```bash
ssh root@YOUR_DROPLET_IP
```

Windows without Git Bash: use PuTTY, port 22.

### 3. Confirm the stack

```bash
apache2 -v
php -v
mysql --version
```

All three print a version. If any is missing, tell Haren before going further.

Open `http://YOUR_DROPLET_IP` in a browser. You should see a default page.

### 4. Build the folders

```bash
cd /var/www/html
mkdir css images js LAMPAPI
```

```
/var/www/html
  css/  images/  js/
  LAMPAPI/        <- every PHP file
  index.html      <- login and register
  contacts.html   <- the app
```

### 5. Fix permissions

```bash
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
```

Skip this and everyone's FileZilla uploads fail.

### 6. Hand off

Post in Discord: the IP, that `/LAMPAPI` exists, and that MySQL is running so Mohammed can start.

Give SFTP access to the API and front end people. FileZilla: host is the IP, user `root`, port 22, SFTP.

### 7. Later

- Load Person B's `seed.sql` once Mohammed's schema is up
- Do the production deploy and click through every feature before the demo

### Debugging for the team

When someone gets a blank page instead of JSON, that is a PHP fatal error:

```bash
tail -50 /var/log/apache2/error.log
```

---

# Mohammed — Database

Start when Jeremy confirms MySQL is running. Both API devs are waiting on you.

### 1. Create the database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE COP4331;
USE COP4331;
```

### 2. Tables

```sql
CREATE TABLE `COP4331`.`Users` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `FirstName` VARCHAR(50) NOT NULL DEFAULT '',
  `LastName` VARCHAR(50) NOT NULL DEFAULT '',
  `Login` VARCHAR(50) NOT NULL DEFAULT '',
  `Password` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uniq_login` (`Login`)
) ENGINE = InnoDB;
```

```sql
CREATE TABLE `COP4331`.`Contacts` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `FirstName` VARCHAR(50) NOT NULL DEFAULT '',
  `LastName` VARCHAR(50) NOT NULL DEFAULT '',
  `Phone` VARCHAR(50) NOT NULL DEFAULT '',
  `Email` VARCHAR(100) NOT NULL DEFAULT '',
  `UserID` INT NOT NULL DEFAULT 0,
  `DateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `idx_user_last` (`UserID`, `LastName`),
  KEY `idx_user_first` (`UserID`, `FirstName`)
) ENGINE = InnoDB;
```

Three deliberate differences from the professor's handout. Do not change them back:

- `Password VARCHAR(255)`, not 50. `password_hash()` returns 60 characters. At 50 it truncates silently and login breaks with no error message anywhere.
- `DateCreated` is required by the assignment and missing from his example.
- The indexes keep search fast once Person B's 200 seed rows are loaded.

No `Colors` table. That was his demo app.

### 3. App database user

```sql
CREATE USER 'TheBeast'@'localhost' IDENTIFIED BY 'PICK_OUR_OWN_PASSWORD';
GRANT ALL PRIVILEGES ON COP4331.* TO 'TheBeast'@'localhost';
FLUSH PRIVILEGES;
```

Our own password, not the handout's. DM it to both API devs.

### 4. Verify

```sql
INSERT INTO Contacts (FirstName,LastName,Phone,Email,UserID)
VALUES ('John','Doe','407-555-0100','jdoe@example.com',1),
       ('Jones','Smith','407-555-0101','jsmith@example.com',1),
       ('Steve','Jobs','407-555-0102','sjobs@example.com',1);

SELECT * FROM Contacts
WHERE UserID=1 AND (FirstName LIKE '%Jo%' OR LastName LIKE '%Jo%');
```

All three rows come back. That is the search requirement proven at the database level before any PHP exists.

### 5. Commit it

Save the above as `database/schema.sql` and push. Now anyone can rebuild from scratch, and it is a real commit with your name on it.

### 6. The ERD

Required slide. draw.io or Lucidchart.

- Two boxes, Users and Contacts, every column with its type
- One line: one user has many contacts, `Contacts.UserID` points at `Users.ID`
- Mark the primary keys and the foreign key

Export PNG, send to Haren.

### 7. Tell the team

Post in Discord when the schema is live.

---

# API Developer A — Auth and write path

**Claim A or B in Discord today and tell Haren so this doc gets your name on it.**

Your split, as you two planned it:

- `db.php` — connection, prepared statement helper, standard JSON response
- `Register.php` — login uniqueness check, hashing
- `Login.php`
- `UpdateContact.php`
- `DeleteContact.php`
- Postman collection, one saved request per endpoint
- Validation helpers: email format, phone normalization, required fields

### Do this first, before any endpoint

`db.php` is what every other file imports, including B's. Write it, push it, and tell B it is ready. Then the two of you agree on the response shape together before either of you writes an endpoint. Otherwise you will write six files in two styles and redo half of them.

### config.php

On the server only. **Never commit this.** Commit `config.example.php` with fake values.

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'TheBeast');
define('DB_PASS', 'the_real_password');
define('DB_NAME', 'COP4331');
```

### db.php

```php
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/errors.php';

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        sendError(ERR_DB_CONNECT);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function getRequestInfo() {
    return json_decode(file_get_contents('php://input'), true);
}

function sendJson($payload) {
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit();
}

function sendError($message) {
    sendJson(array('error' => $message));
}

function requireFields($data, $fields) {
    foreach ($fields as $f) {
        if (!isset($data[$f]) || trim((string)$data[$f]) === '') {
            sendError(ERR_MISSING_FIELD . ': ' . $f);
        }
    }
}

function validEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function normalizePhone($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}
```

### Login.php

```php
<?php
require_once __DIR__ . '/db.php';

$in = getRequestInfo();
requireFields($in, array('login', 'password'));

$conn = getConnection();
$stmt = $conn->prepare(
    "SELECT ID, FirstName, LastName, Password FROM Users WHERE Login=?"
);
$stmt->bind_param("s", $in["login"]);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($in["password"], $row["Password"])) {
        sendJson(array(
            "id"        => (int)$row["ID"],
            "firstName" => $row["FirstName"],
            "lastName"  => $row["LastName"],
            "error"     => ERR_NONE
        ));
    }
}

sendError(ERR_INVALID_CREDENTIALS);
```

Return the same error for "no such user" and "wrong password". Different messages tell an attacker which usernames exist.

### Register.php

```php
$hash = password_hash($in["password"], PASSWORD_DEFAULT);
$stmt = $conn->prepare(
    "INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?,?,?,?)"
);
$stmt->bind_param("ssss", $in["firstName"], $in["lastName"], $in["login"], $hash);
```

Check the login is not taken first, return `ERR_LOGIN_TAKEN` if it is.

### UpdateContact.php and DeleteContact.php

```php
$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=? AND UserID=?");
$stmt->bind_param("ii", $in["id"], $in["userId"]);
```

**Always include `AND UserID=?`.** Without it, any logged-in user who guesses a contact ID can delete or edit someone else's records. This is the most likely security hole in the project and it is one clause to prevent.

### Postman collection

One saved request per endpoint, POST, raw JSON body. Export it as JSON and commit it to `LAMPAPI/postman/` so the front end devs can hit endpoints without writing code first.

---

# API Developer B — Read path and contract

Your split, as you two planned it:

- `AddContact.php`
- `SearchContacts.php` — LIKE matching, case insensitivity, LIMIT/OFFSET paging
- SwaggerHub spec, all six endpoints, request and both response shapes
- `seed.sql` with ~200 fake contacts
- Error code constants shared across all endpoints
- Short README in `/LAMPAPI/` on how to run and test

### Do this first

Two things, both before your endpoints:

1. **`errors.php`.** A imports it in `db.php`, so it blocks their work.
2. **Publish the endpoint contract in Discord.** Saikrishna and Kareem are completely blocked until they know what each endpoint takes and returns. This is the single highest priority item on the team right now. The full SwaggerHub spec can come later, but the contract goes out today.

### The contract

| File | Takes | Returns |
|---|---|---|
| `Register.php` | firstName, lastName, login, password | id, error |
| `Login.php` | login, password | id, firstName, lastName, error |
| `AddContact.php` | userId, firstName, lastName, phone, email | id, error |
| `SearchContacts.php` | userId, search, page | results array, error |
| `UpdateContact.php` | id, userId, firstName, lastName, phone, email | error |
| `DeleteContact.php` | id, userId | error |

All POST, all `application/json`, all in `/var/www/html/LAMPAPI/`.

### errors.php

```php
<?php
define('ERR_NONE', '');
define('ERR_DB_CONNECT', 'Database connection failed');
define('ERR_MISSING_FIELD', 'Missing required field');
define('ERR_INVALID_CREDENTIALS', 'Username or password is incorrect');
define('ERR_LOGIN_TAKEN', 'That username is already taken');
define('ERR_INVALID_EMAIL', 'Email address is not valid');
define('ERR_NOT_FOUND', 'Contact not found');
```

### SearchContacts.php

The one that matters most. It returns an array.

```php
<?php
require_once __DIR__ . '/db.php';

$in = getRequestInfo();
requireFields($in, array('userId'));

$search   = isset($in["search"]) ? "%" . $in["search"] . "%" : "%";
$page     = isset($in["page"]) ? max(1, (int)$in["page"]) : 1;
$pageSize = 50;
$offset   = ($page - 1) * $pageSize;

$conn = getConnection();
$stmt = $conn->prepare(
    "SELECT ID, FirstName, LastName, Phone, Email, DateCreated
     FROM Contacts
     WHERE UserID = ?
       AND (FirstName LIKE ? OR LastName LIKE ?)
     ORDER BY LastName
     LIMIT ? OFFSET ?"
);
$stmt->bind_param("issii", $in["userId"], $search, $search, $pageSize, $offset);
$stmt->execute();
$result = $stmt->get_result();

$contacts = array();
while ($row = $result->fetch_assoc()) {
    $contacts[] = $row;
}

sendJson(array("results" => $contacts, "page" => $page, "error" => ERR_NONE));
```

Why this satisfies the requirements:

- `%Jo%` on both sides matches partial text anywhere in the name
- MySQL's default collation is case insensitive, so `jo` finds `John`
- `LIMIT` and `OFFSET` cover "do not load all recordsets into memory"
- Empty search defaults to `%`, so the page has data on load

### AddContact.php

```php
$stmt = $conn->prepare(
    "INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID)
     VALUES (?,?,?,?,?)"
);
$stmt->bind_param("ssssi", $in["firstName"], $in["lastName"],
                  $phone, $in["email"], $in["userId"]);
```

`DateCreated` fills from the column default. Do not set it manually.

`"ssssi"` is the type of each parameter in order, four strings then an integer. Wrong types misbehave without erroring.

### seed.sql

200 rows, varied names so search and paging are actually testable. Include several starting with "Jo" so the demo search returns something.

```sql
INSERT INTO Contacts (FirstName,LastName,Phone,Email,UserID) VALUES
('John','Doe','4075550100','jdoe@example.com',1),
('Jones','Smith','4075550101','jsmith@example.com',1),
-- ...198 more
```

### SwaggerHub

Account at swagger.io, new API named `Team9-ContactManager`.

```yaml
openapi: 3.0.0
info:
  title: Team 9 Contact Manager
  version: 1.0.0
servers:
  - url: http://OUR_IP/LAMPAPI
paths:
  /Login.php:
    post:
      summary: Log a user in
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              properties:
                login:
                  type: string
                password:
                  type: string
      responses:
        '200':
          description: User record or an error string
          content:
            application/json:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                  firstName:
                    type: string
                  lastName:
                    type: string
                  error:
                    type: string
```

All six endpoints in that shape, with both the success and error response bodies. One of these gets demoed live in the presentation.

---

# Saikrishna — Front end, auth and testing

You own `index.html` (login and register), wiring auth to the API, and responsive testing.

You do not need working endpoints to start. You need B's contract. Build the pages now, wire them up when the API lands.

Pull Bootstrap from a CDN. Nothing to install.

### js/code.js

```js
const urlBase = 'http://OUR_IP/LAMPAPI';

let userId = 0;

function doLogin() {
    const login = document.getElementById("loginName").value;
    const password = document.getElementById("loginPassword").value;

    fetch(urlBase + '/Login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ login: login, password: password })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error !== "") {
            document.getElementById("loginResult").innerHTML = data.error;
            return;
        }
        userId = data.id;
        localStorage.setItem("userId", userId);
        localStorage.setItem("firstName", data.firstName);
        window.location.href = "contacts.html";
    })
    .catch(err => {
        document.getElementById("loginResult").innerHTML = err.message;
    });
}
```

Errors go into a `<div>`. Never an `alert()`.

### Responsive testing

Test at full desktop width and on an actual phone, not just a resized browser window. The contact table is what breaks. Bootstrap's `table-responsive` class handles most of it.

---

# Kareem — Front end, contacts and search

You own `contacts.html`: the list, the add and edit forms, search, and the delete confirmation.

### Rendering the table

```js
function searchContacts() {
    const search = document.getElementById("searchText").value;

    fetch(urlBase + '/SearchContacts.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId: userId, search: search, page: 1 })
    })
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById("contactTableBody");
        tbody.innerHTML = "";
        data.results.forEach(c => {
            const row = tbody.insertRow();
            row.insertCell().textContent = c.FirstName;
            row.insertCell().textContent = c.LastName;
            row.insertCell().textContent = c.Phone;
            row.insertCell().textContent = c.Email;
            row.insertCell().textContent = c.DateCreated;
        });
    });
}
```

Use `textContent`, not `innerHTML`, for contact data. If someone saves a contact named `<script>`, `innerHTML` runs it.

Call this on page load with an empty search so the table is not blank.

### The alert rule

The assignment allows exactly one dialog in the whole app:

```js
function confirmDelete(contactId) {
    if (confirm("Delete this contact?")) {
        deleteContact(contactId);
    }
}
```

Everything else — success messages, validation errors, save confirmations — goes into a `<div>`. Use `alert()` while debugging if you want, but strip every one before the demo. This is an easy place to lose points.

---

# Haren — Project manager

- Make sure the server cost is covered, whether that is Student Pack credit or split seven ways
- Repo public, all seven as collaborators
- Weekly check-in, keep the Gantt current
- Use case diagram
- Build the deck, run one full dry run
- Make sure all seven submit the `.pptx`

---

# Rules for everyone

- **Commit under your own account.** Several small commits, not one big one at the end. This is 25% of your grade.
- **Push to GitHub first, then upload to the server.** Files edited only on the droplet never appear in a commit and get overwritten by the next upload.
- **Never commit passwords or `config.php`.** The repo is public. A password committed once stays in the history forever.
- **Linux is case sensitive.** `Login.php` and `login.php` are different files.
- **Blank page instead of JSON** means a PHP fatal error. Ask Jeremy for `/var/log/apache2/error.log`.
- If you are going to miss a date, say so at the check-in, not after.
