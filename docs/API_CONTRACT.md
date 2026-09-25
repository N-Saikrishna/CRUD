# API Contract

## Authentication

Auth failures return HTTP 401 with one of:
```json
{ "error": "Missing required field: token" }
```
```json
{ "error": "Invalid session token" }
```
```json
{ "error": "Session token has expired" }
```

### POST /LAMPAPI/UpdateContact.php

Request:
```json
{ "token": "<64-char hex>", "id": 7, "userId": 1, "firstName": "John", "lastName": "Doe", "phone": "4075550101", "email": "john.doe@example.com" }
```

Response:
```json
{ "error": "" }
```
```json
{ "error": "Missing required field: email" }
```
```json
{ "error": "Contact not found" }
```

### POST /LAMPAPI/DeleteContact.php

Request:
```json
{ "token": "<64-char hex>", "id": 7, "userId": 1 }
```

Response:
```json
{ "error": "" }
```
```json
{ "error": "Contact not found" }
```
