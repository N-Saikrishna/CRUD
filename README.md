# Contact Manager

Team 9. COP 4331C, Fall 2026, UCF.

A website where you make an account, log in, and keep a list of your own contacts.
You can add, edit, delete, and search them. Each person only sees their own contacts.

Every contact has a first name, last name, phone, email, and the date it was added.

Live at **cruddylit.com**, hosted on DigitalOcean.

## Built with

PHP and MySQL on an Ubuntu server running Apache, behind HTTPS. Plain HTML, CSS and
JavaScript on the front end with Bootstrap. The browser never talks to MySQL. It sends
JSON to our PHP files and they talk to the database.

## What is in here

```
LAMPAPI/     the PHP endpoints and the shared helpers
sql/         schema.sql, seed.sql, create_db_user.sql, the DateCreated migration
ERD/         the entity relationship diagram
docs/        API_CONTRACT.md, lighthouse-report.md
openapi.yaml the SwaggerHub spec
css/  js/    front end
```

## Where things stand

Everything the assignment asks for is built and running on the live site.

- **Server.** Ubuntu droplet, Apache, MySQL, PHP. Domain bought and pointed at it.
  HTTPS with a Let's Encrypt certificate that renews itself. Directory listing turned off.
- **Database.** Users, Contacts and Sessions tables. Foreign keys with cascade delete,
  indexes on the columns search uses, and passwords sized for a bcrypt hash.
- **ERD.** In `ERD/database-ERD.drawio.png`.
- **All seven endpoints.** Register, Login, Logout, AddContact, SearchContacts,
  UpdateContact, DeleteContact. Passwords are hashed with `password_hash` and checked
  with `password_verify`. Logging in returns a session token, and every contact query
  is scoped with `AND UserID = ?` so nobody can reach someone else's contacts.
- **Search.** Partial match on first and last name, case insensitive, with paging done
  in SQL so the browser never receives every row.
- **Front end.** `index.html` for log in and register, `contacts.html` for the list.
  `auth.js` saves the session token. `contacts.js` loads, searches, adds, edits and
  deletes, with paging controls and a result count.
- **Use case, activity and sequence diagrams.** All three, in the slide deck.
- **10,006 test contacts** on the demo account. Searching "jo" returns 269 matches in
  0.18 seconds, and page 200 comes back just as fast.
- **Lighthouse accessibility report.** 92 on the live contacts page, written up in
  `docs/lighthouse-report.md`. The one failure left is a Bootstrap grey at 4.45:1
  against the page background, just under the 4.5:1 minimum.
- **SwaggerHub.** `openapi.yaml` imported, all seven endpoints documented.
- **Gantt chart and slide deck.**

## Who is doing what

| Person | Job |
|---|---|
| Haren | Project manager. Add, Search, Update and Delete contact endpoints, the login and register page, paging on the contacts page, the three UML diagrams, the Gantt chart and the deck |
| Devam | Database schema, the shared helper layer every endpoint calls, the OpenAPI spec and SwaggerHub |
| Pranav | MySQL connection layer, session token helpers, and the register, login and logout endpoints |
| Jeremy | Server, domain, HTTPS, every deployment and migration, and the test data |
| Saikrishna | Login and register wiring, contacts page behaviour, accessibility and the Lighthouse report |
| Kareem | Contacts page markup, Bootstrap styling and the log out button |
| Mohammed | The entity relationship diagram |

## Use of AI

We used Claude on this project and want to be upfront about where.

Most of it was the LAMP side. It helped write and structure the PHP endpoints, work out
the session token and prepared statement patterns, and reorganise the code as it grew,
which is how all seven endpoints ended up going through one shared helper layer and one
connection function instead of each doing its own thing. We also used it for debugging,
for the documentation in this repo, and for the UML diagrams and the Gantt chart.

It did not replace review or testing. Nothing reached `dev` without a pull request and an
approving review from another person, everything was tested against the live server, and
each of us can walk through and explain the code committed under our name. AI output was
wrong more than once and those were caught in review or in testing and fixed.

## How to work on this repo

Two shared branches:

- **`dev`** is where everything comes together. All work goes here first.
- **`main`** only gets code we know works. It gets merged once at the end.

Both are protected. You cannot push to either directly, everything goes through a pull
request with one approval.

Make a branch off `dev` for whatever you are working on:

```bash
git checkout dev
git pull origin dev
git checkout -b feature/your-branch
```

Commit and push to your branch as often as you want, nobody reviews that.

When it is ready, open a pull request into **`dev`**. The base dropdown defaults to
`main`, change it. Add someone as a reviewer, and when they approve, merge and delete
your branch.

Reviewing: Files changed tab, then **Review changes**, then pick **Approve**, then
**Submit review**. The popup defaults to Comment and Comment does not unblock the merge.

Leave an actual sentence when you review. Say what you checked. Code reviews are part
of the individual grade and a one word approval is no evidence you did one.

## Deploying

Only Jeremy does this, but everyone should know why it is fiddly:

```bash
cd /var/www/html
cp LAMPAPI/config.php ~/config.backup
git pull origin dev
cp ~/config.backup LAMPAPI/config.php
```

`config.php` holds the real database password and is not tracked in git, so a plain
pull deletes it and the site goes down. Back it up first, put it back after.

## Rules from the assignment

- LAMP only. No Node, no Python.
- The front end always goes through the API, never straight to MySQL.
- Search runs on the server. Do not load every contact into the browser and filter there.
- Search has to match partial text and ignore capitals. Typing "jo" finds John and Jones.
- No popup alerts anywhere except the one asking if you really want to delete a contact.
- The site has to run on a real server and be reached by a domain name, not an IP.
- Assume 10,000 contacts and make sure search is still fast.

## Things that are graded and easy to forget

- **Lighthouse accessibility report.** Chrome scores our live site. Open it in Chrome,
  press F12, go to the Lighthouse tab, check Accessibility, click Analyze. Save the
  report and screenshot the score for the slides. Run it early, it is much harder to fix
  at the end. The basics: every page starts with `<!DOCTYPE html>` and `<html lang="en">`,
  has a charset and a viewport tag, every input has a real `<label>`, and text has enough
  contrast against its background.
- **Three diagrams, not one.** Use case, activity and sequence. All three are needed.
- **The ERD** is its own separate item.
- **SwaggerHub demo.** Document everything, but only demo one or two endpoints live.
- **Code reviews and documentation count** toward the individual grade, not just commits.
- **Everyone submits the slides.** Not submitting is a zero for that person.

## Never commit

Passwords, `config.php`, or anything with a real database password in it. This repo is
public. Once a password is in the history it stays there.
