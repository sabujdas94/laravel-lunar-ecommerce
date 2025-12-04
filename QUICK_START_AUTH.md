# Quick Start: Testing Authentication API

## Prerequisites
1. Ensure your Laravel application is running
2. Database is migrated (users table and personal_access_tokens table exist)

## Quick Test Commands

### 1. Register a New User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com"
  },
  "access_token": "1|abc123...",
  "token_type": "Bearer"
}
```

**Save the token for next requests!**

### 3. Get Current User (Protected)
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### 4. Logout (Protected)
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Using Postman

### Setup:
1. Create a new collection named "Lunar Auth"
2. Add an environment variable `base_url` = `http://localhost:8000/api`
3. Add an environment variable `token` (will be set after login)

### Register Request:
- Method: POST
- URL: `{{base_url}}/register`
- Headers: 
  - `Content-Type: application/json`
  - `Accept: application/json`
- Body (raw JSON):
```json
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
- Tests tab (to save token):
```javascript
if (pm.response.code === 201) {
    pm.environment.set("token", pm.response.json().access_token);
}
```

### Login Request:
- Method: POST
- URL: `{{base_url}}/login`
- Headers:
  - `Content-Type: application/json`
  - `Accept: application/json`
- Body (raw JSON):
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```
- Tests tab (to save token):
```javascript
if (pm.response.code === 200) {
    pm.environment.set("token", pm.response.json().access_token);
}
```

### Get User Request:
- Method: GET
- URL: `{{base_url}}/me`
- Headers:
  - `Authorization: Bearer {{token}}`
  - `Accept: application/json`

### Logout Request:
- Method: POST
- URL: `{{base_url}}/logout`
- Headers:
  - `Authorization: Bearer {{token}}`
  - `Accept: application/json`

## JavaScript Frontend Example

```javascript
// Login and store token
async function login(email, password) {
  const response = await fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  
  if (response.ok) {
    localStorage.setItem('token', data.access_token);
    console.log('Login successful:', data.user);
    return data;
  } else {
    console.error('Login failed:', data);
    throw new Error(data.message);
  }
}

// Make authenticated request
async function getUser() {
  const token = localStorage.getItem('token');
  
  const response = await fetch('http://localhost:8000/api/me', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  const data = await response.json();
  return data.user;
}

// Logout
async function logout() {
  const token = localStorage.getItem('token');
  
  await fetch('http://localhost:8000/api/logout', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  localStorage.removeItem('token');
  console.log('Logged out successfully');
}

// Usage
login('test@example.com', 'password123')
  .then(() => getUser())
  .then(user => console.log('Current user:', user))
  .catch(error => console.error('Error:', error));
```

## Common Issues & Solutions

### Issue: "Unauthenticated" error
**Solution:** Check that:
- Token is correctly formatted: `Bearer {token}` with a space
- Token hasn't been revoked (logout invalidates token)
- Authorization header is included in request

### Issue: "The email has already been taken"
**Solution:** Use a different email or login with existing credentials

### Issue: "The provided credentials are incorrect"
**Solution:** 
- Verify email and password are correct
- Check user exists in database
- Password is case-sensitive

### Issue: CORS errors in browser
**Solution:** Configure `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:3000'], // Your frontend URL
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

## API Endpoints Summary

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| POST | `/api/register` | No | Register new user |
| POST | `/api/login` | No | Login user |
| POST | `/api/logout` | Yes | Logout (revoke token) |
| GET | `/api/me` | Yes | Get current user |

## Next Steps

1. Test all endpoints using the commands above
2. Integrate with your frontend application
3. Add additional protected routes as needed
4. Consider adding email verification
5. Implement password reset functionality
6. Add rate limiting for security

For complete documentation, see **AUTH_API_DOCUMENTATION.md**
