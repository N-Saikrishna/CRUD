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

### POST /LAMPAPI/Register.php

Request:
```json
{ "firstName": "Demo", "lastName": "User", "login": "demo", "password": "Password123" }
```

Response (201):
```json
{ "id": 1, "firstName": "Demo", "lastName": "User", "error": "" }
```
```json
{ "error": "Login must be 50 characters or fewer" }
```
```json
{ "error": "Password must be at least 6 characters" }
```
```json
{ "error": "A user with that login already exists" }
```

### POST /LAMPAPI/Login.php

Request:
```json
{ "login": "demo", "password": "Password123" }
```

Response (200):
```json
{ "id": 1, "firstName": "Demo", "lastName": "User", "token": "<64-char hex>", "expiresAt": "2026-09-11 21:22:00", "error": "" }
```
```json
{ "error": "Invalid login credentials" }
```

### POST /LAMPAPI/Logout.php

Request:
```json
{ "token": "<64-char hex>" }
```

Response (200):
```json
{ "error": "" }
```
```json
{ "error": "Invalid session token" }
```
```json
{ "error": "Session token has expired" }
```

### POST /LAMPAPI/AddColor.php

Request:
```json
{ "token": "<64-char hex>", "color": "blue" }
```

Response:
```json
{ "error": "" }
```

### POST /LAMPAPI/SearchColors.php

Request:
```json
{ "token": "<64-char hex>", "search": "bl" }
```

Response:
```json
{ "results": ["blue", "black"], "error": "" }
```
```json
{ "id": 0, "firstName": "", "lastName": "", "error": "No Records Found" }
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
