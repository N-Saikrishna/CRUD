# Contact Manager

Team 9. COP 4331C, Fall 2026, UCF.

A website where you make an account, log in, and keep a list of your own contacts.
You can add, edit, delete, and search them. Each person only sees their own contacts.

Every contact has a first name, last name, phone, email, and the date it was added.

## Built with

PHP and MySQL on an Ubuntu server running Apache. Plain HTML, CSS, and JavaScript on
the front end with Bootstrap. The browser never talks to MySQL. It sends JSON to our
PHP files, and they talk to the database.

## Who is doing what

| Person | Job |
|---|---|
| Haren | Project manager, plus the update and delete endpoints |
| Mohammed | Database and the ERD |
| Jeremy | Server, domain, and test data |
| Devam | API setup, register and login, SwaggerHub |
| Pranav | API for adding, listing, and searching contacts |
| Kareem | Contacts page |
| Saikrishna | Login and register page, accessibility |

## How to work on this repo

You cannot push to `main`. It needs a pull request and one approval.

Everyone has their own branch. Push to yours as often as you want.

- `feature/database-mohammed`
- `feature/database-jeremy`
- `feature/api-devam`
- `feature/api-pranav`
- `feature/ui-kareem`
- `feature/ui-sai`
- `feature/docs-haren`

There is also `feature/api` for shared API work like the database schema.

Start each session like this:

```bash
git checkout main
git pull
git checkout feature/your-branch
git pull
```

When something works, open a pull request into `main` and ask someone else to approve it.

When you review, click **Approve**, not Comment. Only Approve unblocks the merge.

## Rules from the assignment

- LAMP only. No Node, no Python.
- The front end always goes through the API, never straight to MySQL.
- Search runs on the server. Do not load every contact into the browser and filter there.
- Search has to match partial text and ignore capitals. Typing "jo" finds John and Jones.
- No popup alerts anywhere except the one asking if you really want to delete a contact.
- The site has to run on a real server and be reached by a domain name. An IP address
  is not allowed.
- Assume 10,000 contacts and make sure search is still fast.

## Things that are graded and easy to forget

These are worth points and nobody assigns them until it is too late.

- **Lighthouse accessibility report.** Chrome scores our live site. Open the site in
  Chrome, press F12, go to the Lighthouse tab, check Accessibility, and click Analyze.
  Save the report and screenshot the score for the slides. Run it early. It is much
  harder to fix at the end. The basics are: every page starts with `<!DOCTYPE html>`
  and `<html lang="en">`, has a charset and a viewport tag, every input has a real
  `<label>`, and text has enough contrast against its background.
- **Three diagrams, not one.** Use case, activity, and sequence. All three are needed.
- **The ERD** is its own separate item.
- **SwaggerHub demo.** Document everything, but only demo one or two endpoints live.
- **Code reviews and documentation count.** The individual part of the grade looks at
  how often you commit, whether you keep at it, the code you write, the reviews you
  leave on other people's pull requests, and the docs you help with. Small commits
  often beats one big one at the end.
- **Everyone submits the slides.** Not submitting is a zero for that person.

## Never commit

Passwords, `config.php`, or anything with a real database password in it. This repo is
public. Once a password is in the history it stays there.
