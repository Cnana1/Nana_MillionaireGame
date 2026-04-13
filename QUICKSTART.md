# Millionaire Game - Quick Start Guide

## Project Structure

```
Nana_MillionaireGame/
├── index.php                 # Entry point (redirects to login)
├── login.php                 # Login page
├── register.php              # Registration page
├── game.php                  # (To be built) Main game page
├── result.php                # (To be built) Result/feedback page
├── leaderboard.php           # (To be built) Top scores display
├── logout.php                # (To be built) Logout handler
├── includes/
│   ├── session_check.php     # Session security & initialization
│   ├── db.php                # User storage functions
│   └── functions.php         # Utility functions
├── data/
│   ├── users.json            # User credentials storage
│   └── leaderboard.json      # Top scores storage
└── css/
    └── styles.css            # Stylesheet
```

## Key Features Implemented

### Security
- **Password Hashing**: Uses `password_hash(PASSWORD_DEFAULT)` for secure storage
- **File Locking**: Prevents data corruption with `flock()` when writing
- **Session Protection**: `httponly` cookies, strict mode enabled
- **XSS Prevention**: All output sanitized with `htmlspecialchars()`
- **Input Validation**: Usernames and passwords validated before processing

### User Registration (register.php)
- Username: 3-20 characters
- Password: Minimum 6 characters
- Password confirmation required
- Duplicate username detection
- Success redirect to login after 3 seconds
- Sticky forms (retain username on error)

### User Login (login.php)
- Credential verification with `password_verify()`
- Session initialization with game variables
- Error messages (generic for security)
- Sticky username field
- Auto-redirect if already logged in

### Session Management
- Game session initialized on login with:
  - `$_SESSION['user']` → Username
  - `$_SESSION['score']` → Current score
  - `$_SESSION['level']` → Question number
  - `$_SESSION['lifelines']` → Available lifelines
  - `$_SESSION['seen_ids']` → Previously used questions
  - `$_SESSION['recent_answers']` → Last 3 answers (for AI difficulty)

## Testing Instructions

### 1. Start Your Local Server
```bash
# If using PHP built-in server (PHP 5.4+)
php -S localhost:8000

# Then visit: http://localhost:8000
```

### 2. Test Registration
- Go to register page (or click "Register here" link)
- Try invalid inputs:
  - Username < 3 characters
  - Password < 6 characters
  - Mismatched passwords
  - Duplicate username
- Create a valid account (e.g., username: `testuser`, password: `test123`)
- Verify redirect to login after 3 seconds

### 3. Test Login
- Try wrong password → Should show generic error message
- Try non-existent username → Should show generic error message
- Login with correct credentials → Should redirect to game.php (which doesn't exist yet)
- Check browser: Session cookie should be created

### 4. Inspect User Data
Open `data/users.json` to verify:
- User is stored with hashed password
- Data is properly formatted JSON
- Password is not readable (hashed)

## Next Steps (For Sprint 2-3)

### Create game.php
- Check session (require login)
- Display current question
- Show answer buttons
- Submit answer via POST
- Update score and level in session

### Create result.php
- Show correct/incorrect feedback
- Update score based on answer
- Display new level/milestone
- Check for safe havens
- Redirect to next question or leaderboard

### Create leaderboard.php
- Display top 5-10 scores
- Sort by score (descending)
- Show rank, username, score
- Link back to game or logout
- Accessible without login (optional)

### Create logout.php
- Destroy session securely
- Redirect to login.php

### Create game_logic.php (in includes/)
- Question bank (30+ questions)
- Question randomization
- Answer validation
- Score calculation
- Level progression logic

## Database Structure

### users.json Format
```json
{
  "username1": {
    "username": "username1",
    "password": "$2y$10$...hashed...",
    "created": "2026-04-12 10:30:00",
    "score": 0
  }
}
```

### leaderboard.json Format
```json
[
  {
    "username": "user1",
    "score": 1000000,
    "date": "2026-04-12 15:45:00"
  }
]
```

## Common Issues & Solutions

**Issue**: "Cannot access variables" from includes/
- **Solution**: Use absolute paths with `__DIR__` (already done in db.php)

**Issue**: Users.json file is corrupted or empty
- **Solution**: Delete the file and recreate it with `[]` content

**Issue**: Sessions not persisting
- **Solution**: Check `session_start()` is called before any output

**Issue**: Password verification failing
- **Solution**: Ensure password_hash() uses `PASSWORD_DEFAULT` for maximum compatibility

## Notes
- All form data is processed via POST (no GET for credentials)
- Generic error messages for failed login (don't reveal if username exists)
- Error messages are sanitized before display
- Passwords are never logged or displayed
