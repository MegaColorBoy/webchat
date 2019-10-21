# WebChat
A web-based instant messenger similar to Facebook Messenger and Whatsapp.

## Why built this?
Just trying to reverse engineer and have some fun coding it.

## Technologies
- PHP
- Javascript
- MySQL
- Slim Framework (for REST API)
- HTML, CSS (SASS)

## TODO

### Backend 

- [x] Create an API directory
- [x] Create database schema for backend
    - [x] User
    - [x] Friend
    - [x] Friend Request
    - [ ] Messages
    - [ ] Groups
    - [ ] Notifications
- [x] Write API methods for `users`
    - [x] GET: User information
    - [x] GET: Fetch all users
    - [x] UPDATE: Update user credentials
    - [x] UPDATE: Update profile information
    - [x] UPDATE: Set user visibility
    - [x] POST: Create user
    - [x] POST: Login User
    - [ ] POST: Logout User (This can be done later)
    - [x] DELETE: Remove user 
    - [x] GET: Show friend requests by ID
    - [x] GET: Show friend list by ID  
    - [x] POST: Send friend request by ID
    - [x] POST: Add friend by ID
    - [x] DELETE: Remove friend request by ID
    - [x] DELETE: Remove friend by ID
- [ ] Write methods for `messages`
    - [ ] GET: Fetch all messages between UserA and UserB
    - [ ] POST: Send message from UserA to UserB
    - [ ] DELETE: Delete message from UserA to UserB 
- [ ] Write methods for `notifications`
- [ ] Write methods for `groups`