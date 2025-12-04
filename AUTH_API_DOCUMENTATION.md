# Authentication API Documentation

## Overview
This document describes the authentication API endpoints for the Lunar ecommerce application. The API uses Laravel Sanctum for token-based authentication.

## Base URL
```
http://your-domain.com/api
```

## Authentication
Protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your_access_token}
```

---

## Endpoints

### 1. Register a New User

**Endpoint:** `POST /api/register`

**Description:** Create a new user account and receive an authentication token.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Validation Rules:**
- `name`: required, string, max 255 characters
- `email`: required, valid email, max 255 characters, must be unique
- `password`: required, string, minimum 8 characters, must match confirmation
- `password_confirmation`: required, must match password

**Success Response (201 Created):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "access_token": "1|abc123xyz...",
  "token_type": "Bearer"
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

**Example cURL:**
```bash
curl -X POST http://your-domain.com/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

---

### 2. Login

**Endpoint:** `POST /api/login`

**Description:** Authenticate a user and receive an access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Validation Rules:**
- `email`: required, valid email
- `password`: required

**Success Response (200 OK):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "access_token": "2|xyz789abc...",
  "token_type": "Bearer"
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": [
      "The provided credentials are incorrect."
    ]
  }
}
```

**Example cURL:**
```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Example JavaScript (Fetch):**
```javascript
fetch('http://your-domain.com/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    email: 'john@example.com',
    password: 'password123'
  })
})
.then(response => response.json())
.then(data => {
  // Store the token
  localStorage.setItem('access_token', data.access_token);
  console.log('Login successful', data);
})
.catch(error => console.error('Error:', error));
```

---

### 3. Logout

**Endpoint:** `POST /api/logout`

**Description:** Revoke the current user's access token.

**Authentication Required:** Yes

**Headers:**
```
Authorization: Bearer {your_access_token}
Accept: application/json
```

**Success Response (200 OK):**
```json
{
  "message": "Logged out successfully"
}
```

**Error Response (401 Unauthorized):**
```json
{
  "message": "Unauthenticated."
}
```

**Example cURL:**
```bash
curl -X POST http://your-domain.com/api/logout \
  -H "Authorization: Bearer 2|xyz789abc..." \
  -H "Accept: application/json"
```

**Example JavaScript (Fetch):**
```javascript
const token = localStorage.getItem('access_token');

fetch('http://your-domain.com/api/logout', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  // Remove the token
  localStorage.removeItem('access_token');
  console.log('Logout successful', data);
})
.catch(error => console.error('Error:', error));
```

---

### 4. Get Authenticated User

**Endpoint:** `GET /api/me`

**Description:** Get the currently authenticated user's information.

**Authentication Required:** Yes

**Headers:**
```
Authorization: Bearer {your_access_token}
Accept: application/json
```

**Success Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2025-12-04T10:00:00.000000Z"
  }
}
```

**Error Response (401 Unauthorized):**
```json
{
  "message": "Unauthenticated."
}
```

**Example cURL:**
```bash
curl -X GET http://your-domain.com/api/me \
  -H "Authorization: Bearer 2|xyz789abc..." \
  -H "Accept: application/json"
```

**Example JavaScript (Fetch):**
```javascript
const token = localStorage.getItem('access_token');

fetch('http://your-domain.com/api/me', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  console.log('User info:', data.user);
})
.catch(error => console.error('Error:', error));
```

---

## Authentication Flow

### 1. Registration Flow
```
Client                                  Server
  |                                       |
  |-- POST /api/register                 |
  |   (name, email, password, confirm)   |
  |                                       |
  |                    201 Created, Token|
  |<--------------------------------------|
  |                                       |
  | Store token in localStorage/cookie   |
  |                                       |
```

### 2. Login Flow
```
Client                                  Server
  |                                       |
  |-- POST /api/login                    |
  |   (email, password)                  |
  |                                       |
  |                    200 OK, Token ----|
  |<--------------------------------------|
  |                                       |
  | Store token in localStorage/cookie   |
  |                                       |
```

### 3. Protected Request Flow
```
Client                                  Server
  |                                       |
  |-- GET /api/me                        |
  |   Authorization: Bearer {token}      |
  |                                       |
  |                    200 OK, User ------|
  |<--------------------------------------|
  |                                       |
```

### 4. Logout Flow
```
Client                                  Server
  |                                       |
  |-- POST /api/logout                   |
  |   Authorization: Bearer {token}      |
  |                                       |
  |                    200 OK ------------|
  |<--------------------------------------|
  |                                       |
  | Remove token from localStorage       |
  |                                       |
```

---

## Error Handling

All endpoints return appropriate HTTP status codes:

- **200 OK**: Successful request
- **201 Created**: Resource created successfully
- **401 Unauthorized**: Missing or invalid authentication token
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server-side error

Error responses include a message and may include validation errors:

```json
{
  "message": "Error description",
  "errors": {
    "field_name": [
      "Error message for this field"
    ]
  }
}
```

---

## Security Notes

1. **HTTPS**: Always use HTTPS in production to protect tokens and credentials in transit
2. **Token Storage**: Store tokens securely (httpOnly cookies for web, secure storage for mobile)
3. **Token Expiration**: Configure token expiration in `config/sanctum.php` if needed
4. **Password Requirements**: Minimum 8 characters (can be customized)
5. **Rate Limiting**: Consider adding rate limiting to prevent brute force attacks
6. **CORS**: Configure CORS properly in `config/cors.php` for cross-origin requests

---

## Testing

### Using Postman

1. **Register/Login**:
   - Set method to POST
   - URL: `http://your-domain.com/api/login`
   - Body → raw → JSON
   - Add credentials and send
   - Copy the `access_token` from response

2. **Protected Endpoints**:
   - Set method (GET/POST/etc.)
   - URL: `http://your-domain.com/api/me`
   - Headers → Add `Authorization: Bearer {your_token}`
   - Send request

### Using cURL

See example cURL commands in each endpoint section above.

---

## Integration Examples

### React Example

```javascript
// services/auth.js
const API_URL = 'http://your-domain.com/api';

export const authService = {
  async login(email, password) {
    const response = await fetch(`${API_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ email, password })
    });
    
    if (!response.ok) throw new Error('Login failed');
    
    const data = await response.json();
    localStorage.setItem('access_token', data.access_token);
    return data;
  },

  async logout() {
    const token = localStorage.getItem('access_token');
    await fetch(`${API_URL}/logout`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    localStorage.removeItem('access_token');
  },

  async getUser() {
    const token = localStorage.getItem('access_token');
    const response = await fetch(`${API_URL}/me`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    
    if (!response.ok) throw new Error('Failed to get user');
    
    return response.json();
  }
};
```

### Vue.js Example

```javascript
// services/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://your-domain.com/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add token to requests
api.interceptors.request.use(config => {
  const token = localStorage.getItem('access_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default {
  login(credentials) {
    return api.post('/login', credentials);
  },
  
  logout() {
    return api.post('/logout');
  },
  
  getUser() {
    return api.get('/me');
  }
};
```

---

## Troubleshooting

### "Unauthenticated" Error
- Verify token is being sent in Authorization header
- Check token format: `Bearer {token}` (note the space)
- Ensure token hasn't been revoked
- Verify Sanctum middleware is configured

### CORS Issues
- Configure `config/cors.php`
- Ensure frontend origin is allowed
- Check that credentials are included in requests

### Token Not Working
- Verify `HasApiTokens` trait is on User model
- Check Sanctum is installed and configured
- Ensure database has `personal_access_tokens` table

---

## Additional Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Lunar Documentation](https://docs.lunarphp.io/)
- [API Security Best Practices](https://owasp.org/www-project-api-security/)
