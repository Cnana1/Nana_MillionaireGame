# Nana_MillionaireGame



## Project Description
The Nana Millionaire Game is a PHP-based quiz game inspired by *Who Wants to Be a Millionaire*. Players log in, answer progressively difficult questions, and earn points based on accuracy, streaks, and level progression. The game uses PHP sessions to manage state, lifelines such as 50/50 and Phone a Friend, and dynamically adjusts difficulty based on player performance. A persistent leaderboard stores and displays the top scores for all users.

---

## Setup Instructions

### Requirements
- PHP 7.4 or higher
- Web server (Apache recommended)
- Access to CODD server

### Folder Structure
Nana_MillionaireGame/
├── index.php                 # Entry point (redirects to login)
├── login.php                 # Login page
├── register.php              # Registration page
├── cashout.php               # Save progress and quit 
├── game.php                  # Main game page
├── result.php                # Result/feedback page
├── leaderboard.php           # Top scores display
├── logout.php                # Logout handler
├── includes/
│   ├── session_check.php     # Session security & initialization
│   ├── db.php                # User storage functions
│   └── functions.php         # Utility functions
├── data/
│   ├── users.json            # User credentials storage
│   └── leaderboard.json      # Top scores storage
└── css/
    └── styles.css            # Stylesheet

### Run the Project
- Open your browser and go to: http://codd.cs.gsu.edu/~cnana1/WP/PW/Nana_MillionaireGame/index.php


---

## Usage Guide

### 1. Register / Login
- New users register via `register.php`
- Existing users log in via `login.php`
- Successful login starts a PHP session

### 2. Gameplay (`game.php`)
- Users answer multiple-choice questions
- Score increases based on:
  - Level
  - Streak multiplier
- Difficulty adapts based on performance
- Lifelines:
  - **50/50** → removes two incorrect answers
  - **Phone a Friend** → reveals the correct answer hint
- Lifelines unlock after the first question

### 3. Game Flow
- Questions are randomly selected without repetition
- Game ends when:
  - Player runs out of lives OR
  - All questions are answered
- Player is redirected to the leaderboard

### 4. Leaderboard (`leaderboard.php`)
- Displays top scores sorted in descending order
- Ensures each user only appears once with their **highest score**
- Data is stored in `leaderboard.json`

### 5. Logout
- Users can log out using the logout button
- Session is destroyed and user is redirected to login

---

## Team Members
Caleb-Wesley Siyapze Nana (game.php, cashout.php, logout.php, leaderboard.php, questions.php, result.php)
Lance Santiago (login.php, session_check.php, functions.php, db.php, register.php, styles.css)