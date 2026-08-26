# Contact Manager (COP 4331C Small Project)

Fall 2026, Dr. Aashish Yadavally, UCF.

Web-based contact manager. Users register or log in, then do full CRUD on their own contacts, plus search.

## Features required

- Login / register (landing page gives both options)
- Create a contact
- Read / list contacts
- Update a contact
- Delete a contact (this is the only place an alert/dialog is allowed)
- Search contacts

## Contact record fields (minimum)

| Field | Notes |
|---|---|
| Name | First and last name preferred |
| Email | |
| Phone | |
| Date created | Set by the system |

## Tech constraints

- LAMP stack only. No Python.
- Front end talks to the backend through an API. No direct DB access from the client.
- REST over HTTP. Ports 80/443 are the convention, not a hard rule.
- JSON for all request and response bodies.
- App has to be hosted on a real remote server, not a local machine. Suggested: GoDaddy, Heroku, DigitalOcean, AWS, Azure.

### JSON shape example

Request:
```json
{
  "search": "Jo"
}
```

Response:
```json
{
  "results": [
    { "firstName": "John", "lastName": "Doe" },
    { "firstName": "Jones", "lastName": "Smith" }
  ]
}
```

## Search behavior

- Partial match, case insensitive. Typing `Jo` returns John, Jones, Jobs.
- Has to work on first name and last name at minimum.

## Scalability

- Do not load all records into memory. Filter and page on the server side.
- Assume something like 10,000 contacts and make sure it still returns fast.

## UX rules

- Follow normal UX best practices.
- No alert boxes except the delete confirmation. Use them during testing only.
- Test on full-screen desktop and on phones.
- Bootstrap and jQuery are suggested.

## GitHub

- All team code lives in one GitHub repo.
- The repo must be public so the professor and TA can check progress.
- Add the repo link to the project spreadsheet.
- Commits are timestamped, so individual contribution is visible.

## API documentation

- Use SwaggerHub (swagger.io) to document and test the API.
- At least one endpoint has to be demoed live during the presentation.
- API devs keep it updated so the front end knows what changed.

## Presentation requirements

- Professional PowerPoint slides.
- Must include: Gantt chart, Use Case diagram, Entity Relationship Diagram.
- Working demo of login/register, CRUD, and search.
- At least one SwaggerHub endpoint demoed.
- Every member speaks, and not just to say their name.
- Every member submits the .pptx to Webcourses individually.
- Do not zip the file. Submit before the group presents.

## Team

5 to 7 members, same team through the end of the project.

| Role | Count | Responsibility |
|---|---|---|
| Project Manager | 1 | Keeps people on track, can also develop |
| Database Engineer | 1 | Maintains DB, owns the ERD |
| API Developer | 2 | Builds endpoints, keeps SwaggerHub current |
| Front-End Developer | 2 | Builds UI, works off the Swagger docs |

Split by skill, but overlap responsibilities wherever possible so one person going missing does not stall the project.

## Grading

- 75% team grade, same for everyone, with exceptions for people who do not pull their weight.
- 25% individual, based on GitHub activity and the Gantt chart.

## Timeline from the slides

| Week | Milestone |
|---|---|
| 2 - 3 | GitHub repo up, link submitted |
| 3 | SwaggerHub API docs started |
| 4 | User experience pass, responsive testing |
| 5 | Gantt chart, Use Case diagram, ERD done |

## Team expectations

- Weekly meetings, in person or on Discord.
- DWYSYWD: do what you say you will do.
- Do not ghost the team. Social loafing gets caught and it hits your grade.
- If you have a full-time job, plan the time. This is a 4000-level class that feeds into Senior Design.

## Learning curve

You get the basics in class, but the lectures are "getting started" only. Expect to fill gaps with YouTube, online courses, and office hours with the professor or TA.
