# API Contract

### POST /LAMPAPI/Login.php

Request:
```json
{ "login": "demo", "password": "Password123" }
```

Response:
```json
{ "id": 1, "firstName": "Demo", "lastName": "User", "error": "" }
```
```json
{ "id": 0, "firstName": "", "lastName": "", "error": "No Records Found" }
```

### POST /LAMPAPI/AddColor.php

Request:
```json
{ "color": "blue", "userId": 1 }
```

Response:
```json
{ "error": "" }
```

### POST /LAMPAPI/SearchColors.php

Request:
```json
{ "search": "bl", "userId": 1 }
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
{ "id": 7, "userId": 1, "firstName": "John", "lastName": "Doe", "phone": "4075550101", "email": "john.doe@example.com" }
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
{ "id": 7, "userId": 1 }
```

Response:
```json
{ "error": "" }
```
```json
{ "error": "Contact not found" }
```
