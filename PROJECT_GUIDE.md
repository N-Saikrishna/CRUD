# Team 9 Project Guide

Contact Manager | COP 4331C | Fall 2026

**Find your name. Read your section. Skim the rest.**

> **Source of truth:** roles and dates come from `Team9_Gantt_Slides.pptx`. If this doc and
> the Gantt ever disagree, the Gantt wins and this doc gets fixed.

---

## How this is graded — 100 points

| Item | Pts | Owner |
|---|---|---|
| PowerPoint submitted on time (all 7, individually) | 5 | everyone |
| Professional slides | 5 | Haren |
| All members participate in the talk | 5 | everyone |
| Gantt chart | 5 | Haren |
| ERD | 5 | Mohammed |
| Use case **and** Activity **and** Sequence diagrams | 5 | Haren |
| SwaggerHub demo (1-2 endpoints, no more) | 5 | Devam |
| Effective server-side search, partial match | 5 | Pranav |
| **Lighthouse accessibility report** | 5 | Kareem |
| Working project demonstration | 20 | everyone |
| Adherence to current standards | 5 | everyone |
| Instructor discretionary excellence | 5 | — |
| **GitHub contribution** — commits, consistency, code, **code reviews**, **documentation** | 25 | individual |

Two of these are easy to lose by forgetting they exist: the **Lighthouse accessibility
report**, and the fact that the diagram line is **three diagrams, not one**.

The 25-point contribution score counts **code reviews** and **documentation**, not just
commits. See "Rules for everyone" at the bottom — that is why we use pull requests.

---

## What we are building

A website where you make an account, log in, and manage your contacts. Add one, see your list, edit one, delete one, and search by name.

Each contact stores: first name, last name, phone, email, and the date it was created.

## Rules from the assignment

- LAMP only. PHP on the server, MySQL for the database. No Node, no Python.
- The front end talks to the backend through the API. Never straight to MySQL.
- Search matches partial text and ignores capitalization. "jo" finds John, Jones, Jobs.
- No pop-up alerts anywhere except the delete confirmation.
- The site runs on a real remote server. Local demos are not allowed.
- **The app must be reached by a domain name. A raw IP address is not acceptable.**
- All client-server traffic is JSON, and the client is AJAX — asynchronous calls only.
- Every endpoint documented on SwaggerHub. **Demo at least one and no more than two.**
- The live site needs a passing Lighthouse accessibility report.

---

# Order of work

Some work blocks other work. This is the chain:

**Day 1, in parallel:**
- Jeremy stands up the server **and buys the domain** — DNS takes hours, so start it first
- Devam writes `errors.php` and `db.php`, then agrees the response shape with Pranav
- **Devam publishes the endpoint contract to Discord.** Highest priority item on the team —
  Kareem and Saikrishna cannot start wiring anything until it exists
- Haren picks his technical role and claims two endpoints

**Day 2:**
- Mohammed builds the schema once Jeremy says MySQL is up
- Front end starts building pages against the contract, no working API needed yet
- Kareem sets up the HTML baseline (doctype, lang, viewport, real labels) so the
  accessibility score is built in rather than retrofitted

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

### 5b. Buy the domain and point it at the droplet

This is a hard requirement. `http://159.203.x.x` scores zero on the demo.

1. Buy a cheap `.com` or `.xyz` (Namecheap, Porkbun, or free through the Student Pack)
2. In DigitalOcean: **Networking → Domains**, add the domain, point it at the droplet
3. At the registrar, set nameservers to `ns1/ns2/ns3.digitalocean.com`
4. DNS takes 15 minutes to a few hours. Start early.
5. When `http://ourdomain.com` loads the Apache page, post it in Discord

Everyone hardcodes this domain in `urlBase` and in the Swagger `servers:` block. The IP is
used for `ssh` and FileZilla only.

### 5c. UCF network check — put this on your calendar now

UCF IT sometimes blocks outside domains. **Two days before the presentation, and again one
day before, open the site while on campus wifi.** If it is blocked, IT can unblock it but it
takes a day or two. Finding out on presentation morning is a zero on the 20-point demo.

### 6. Hand off

Post in Discord: the IP, that `/LAMPAPI` exists, and that MySQL is running so Mohammed can start.

Give SFTP access to the API and front end people. FileZilla: host is the IP, user `root`, port 22, SFTP.

### 7. Later

- Write and load `seed.sql`, **10,000 contacts**, once Mohammed's schema is up. The
  assignment says assume 10k records, so we test at 10k. Generate it with a script and
  commit the script too. Include plenty of names starting with "Jo" for the demo search.
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
- The indexes keep search fast once Jeremy's 10,000 seed rows are loaded. At 10k rows an
  unindexed search is visibly slow, and slow search costs us the 5-point search item.

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

# Devam — API, foundation and auth

Matches your Gantt row: contract locked week 1, register and login week 2, Swagger current
throughout, live endpoint demo week 4.

- `errors.php` — shared error constants
- `db.php` — connection, request/response helpers, validation
- `config.example.php` — committed with fake values. The real `config.php` never is.
- **The endpoint contract, published in Discord** (the table in Pranav's section)
- `Register.php` — login uniqueness check, hashing
- `Login.php`
- SwaggerHub spec covering every endpoint
- Validation helpers: email format, phone normalization, required fields

### Do this first, before any endpoint

Three things, in this order, all on day one:

1. **`errors.php` and `db.php`.** Every other PHP file imports these, including Pranav's.
   Until they exist and are pushed, nobody can write an endpoint.
2. **Publish the contract in Discord.** Kareem and Saikrishna are fully blocked until they
   know what each endpoint takes and returns. It is already written below — you do not have
   to design it, just post it and own it from there.
3. **Agree the response shape with Pranav** before either of you writes an endpoint.
   Otherwise you write six files in two styles and redo half of them.

If the contract changes later, announce it. A silent change breaks the front end with no
error message pointing anywhere near the cause.

**On `db.php`:** the Gantt has Jeremy down for "PHP to MySQL connection layer and config."
That means he installs and verifies the PHP MySQL extension on the server and hands you the
credentials. The actual `db.php` is yours. Do not both wait on each other.

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

# Pranav — API, contacts and search

Matches your Gantt row: spec review week 1, contact CRUD week 2, search week 3, paging and
error handling week 4.

- `AddContact.php`
- `SearchContacts.php` — LIKE matching, case insensitivity, LIMIT/OFFSET paging
- `UpdateContact.php`
- `DeleteContact.php`
- Server-side paging and consistent JSON error handling
- Postman collection, one saved request per endpoint
- Short README in `/LAMPAPI/` on how to run and test

### Do this first

Review the contract below with Devam and agree the response shape before writing anything.
Then wait for his `db.php` and `errors.php` to land — every file you write imports them.

`SearchContacts.php` is worth 5 points on its own and it is the endpoint most likely to be
demoed live. Test it against all 10,000 seed rows, not three.

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

### seed.sql — Jeremy owns this, you depend on it

Per the Gantt, Jeremy generates **10,000 contacts** in week 2. You need them loaded before
you can honestly claim search and paging work, so chase him if they are late.

Do not hand-type 10k rows. Generate them:

```sql
INSERT INTO Contacts (FirstName,LastName,Phone,Email,UserID) VALUES
('John','Doe','4075550100','jdoe@example.com',1),
('Jones','Smith','4075550101','jsmith@example.com',1),
-- ...9,998 more, generated by script and committed alongside the .sql
```

Plenty of names starting with "Jo" so the live demo search returns something good.

Once loaded, time the search endpoint. If it is slow, the indexes in Mohammed's schema are
missing or the query is not using them — check with `EXPLAIN`.

### SwaggerHub

Account at swagger.io, new API named `Team9-ContactManager`.

```yaml
openapi: 3.0.0
info:
  title: Team 9 Contact Manager
  version: 1.0.0
servers:
  - url: http://ourdomain.com/LAMPAPI
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

# Kareem — Front end, auth, accessibility and testing

Matches your Gantt row: wireframes week 1, static Bootstrap pages week 2, auth wired week 3,
responsive and accessibility testing week 4.

You own `index.html` (login and register), wiring auth to the API, responsive testing, and
the Lighthouse accessibility report.

You do not need working endpoints to start. You need Devam's contract. Build the pages now, wire them up when the API lands.

Pull Bootstrap from a CDN. Nothing to install.

### Start every page with this

The professor's demo files skip all of this, and it costs us points on two separate rubric
lines. Every `.html` file we ship starts here:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Manager</title>
</head>
```

Missing `<!DOCTYPE>` or `lang` is an accessibility failure. Missing the viewport tag breaks
mobile layout *and* fails Lighthouse, so it costs us twice.

### Accessibility — 5 points, and it has to be built in

There is a rubric line for a **Lighthouse accessibility report**. You cannot bolt this on in
week 4; it is a score Chrome generates against the live site. Build to it from the start:

- **Every input needs a real `<label>`.** A `placeholder` is not a label. This is the most
  common automatic failure, and the demo files do it wrong on every single field.
  ```html
  <label for="loginName">Username</label>
  <input type="text" id="loginName" placeholder="Username">
  ```
- **Contrast at least 4.5:1** for normal text. The demo stylesheet's red `#95060a` on grey
  `#b2b2b2` computes to 4.29:1 and fails. Bootstrap's defaults pass — use them.
- Every button has real text, every image has `alt`, headings run `h1` then `h2` in order.
- Form errors go in a `<div>` tied to the field with `aria-describedby`.

**How to run it:** live site in Chrome → F12 → Lighthouse tab → check Accessibility →
Analyze. Export the report, commit it, screenshot the score for the slides. Run it once in
week 2 while the pages are still small and easy to fix.

### js/code.js

```js
const urlBase = 'http://ourdomain.com/LAMPAPI';

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

# Saikrishna — Front end, contacts and search

Matches your Gantt row: contact list and table UI week 2, CRUD forms and search UI week 3,
delete confirm and UX polish week 4.

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

### Technical role — required

The assignment says a PM must "select the technical role they have the strongest affinity
for and actively contribute in that area." Coordination alone does not satisfy it, and the
25-point contribution score is measured on code. **Pair with Devam and Pranav on the API and
own two endpoints outright.** Put it on the Gantt with your name on it.

### Coordination

- Server cost covered, whether Student Pack credit or split seven ways
- Repo public, all seven as collaborators
- Weekly check-in, keep the Gantt current
- Enforce the pull request workflow — code reviews are part of the 25 points
- Build the deck, run one full dry run against a stopwatch
- Make sure all seven submit the `.pptx` individually. A miss is a zero for that person.

### Three diagrams, not one

The rubric line reads "Use case, **Activity**, and **Sequence** diagrams." All three:

- **Use case** — actors and what they can do: register, log in, add/edit/delete/search
- **Activity** — flowchart of one process. Add-a-contact is the clean one.
- **Sequence** — messages over time. Login is the obvious one: browser → `Login.php` →
  MySQL → back, including the failed-password path.

draw.io does all three. Export PNG.

### Before the presentation, in the signup spreadsheet

Three things, not just the repo link:

- Project title
- GitHub repository URL
- **Live application URL — the domain, not an IP**

### Deck sections the rubric asks for

Title / Team members and what each person did / Technology used / Things that went well /
**Things that did not go well** / Gantt / ERD / API demonstration / App demonstration / Q&A.

### Rehearsal

**12 minutes.** Over 13 costs 5 points, then 5 more per minute after that. Seven speakers
plus a live Swagger demo plus an app demo is about 90 seconds each — that only works
rehearsed with a stopwatch. Everyone explains a meaningful piece; saying your name does not
count.

Slides go on a **USB drive**. There is no time to pull them from Drive or Dropbox.

---

# Rules for everyone

- **Commit under your own account.** Several small commits, not one big one at the end.
  This is 25 of the 100 points.
- **Work on a branch and open a pull request.** The contribution score explicitly counts
  **code reviews** and **documentation**, not just commits. Nobody pushes straight to
  `main`. Every PR gets a real review comment from someone else before it merges — "looks
  good" is not a review. This is worth real points and it cannot be faked at the end.
- **The app is reached by our domain, never the IP.** `urlBase`, the Swagger `servers:`
  block, and anything you demo all use the domain.
- **Push to GitHub first, then upload to the server.** Files edited only on the droplet never appear in a commit and get overwritten by the next upload.
- **Never commit passwords or `config.php`.** The repo is public. A password committed once stays in the history forever.
- **Linux is case sensitive.** `Login.php` and `login.php` are different files.
- **Blank page instead of JSON** means a PHP fatal error. Ask Jeremy for `/var/log/apache2/error.log`.
- If you are going to miss a date, say so at the check-in, not after.
