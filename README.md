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
    - [ ] Friend
    - [ ] Friend Request
    - [ ] Messages
    - [ ] Groups
    - [ ] Notifications
- [ ] Write API methods for `users`
    - [x] GET: User information
    - [x] GET: Fetch all users
    - [x] UPDATE: Update user credentials
    - [x] UPDATE: Update profile information
    - [x] UPDATE: Set user visibility
    - [x] POST: Create user
    - [x] POST: Login User
    - [ ] POST: Logout User
    - [x] DELETE: Remove user 
- [ ] Write methods for `messages`
    - [ ] GET: Fetch all messages between UserA and UserB
    - [ ] POST: Send message from UserA to UserB
    - [ ] DELETE: Delete message from UserA to UserB 
- [ ] Write methods for `notifications`
- [ ] Write methods for `friends`
    - [ ] GET: Show friend requests by ID
    - [ ] GET: Show friend list by ID  
    - [ ] POST: Send friend request by ID
    - [ ] POST: Add friend by ID
    - [ ] DELETE: Remove friend request by ID
    - [ ] DELETE: Remove friend by ID
- [ ] Write methods for `groups`