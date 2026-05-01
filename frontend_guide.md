# Easy Notes API - Frontend Development Guide for AI Agents

This guide provides everything an AI agent needs to know to build a frontend for the **Easy Notes API**. It covers authentication, mandatory profile setup, and all available endpoints.

## 🚀 Overview

Easy Notes is a social platform where users can share notes, follow others, like content, and engage through comments.

## 🔐 Authentication

The API uses **Laravel Sanctum** for authentication. All requests should include the `Accept: application/json` header. For authenticated requests, use the `Authorization: Bearer {token}` header.

### Auth Endpoints
- `POST /api/register`: Register a new user.
    - **Request**:
        - `email` (string, required, email, unique)
        - `password` (string, required, min:8)
        - `password_confirmation` (string, required)
    - **Response**: `204 No Content`
- `POST /api/login`: Log in and receive a Bearer token.
    - **Request**:
        - `email` (string, required)
        - `password` (string, required)
    - **Response**: `200 OK`
        - `user`: { `id`, `name`, `username`, `email`, `bio`, `gender` }
        - `token`: `string`
- `POST /api/logout`: Log out (requires authentication).
    - **Response**: `204 No Content`
- `POST /api/forgot-password`: Request password reset link.
    - **Request**: `email`
    - **Response**: `200 OK` { `status` }
- `POST /api/reset-password`: Reset password using token.
    - **Request**: `token`, `email`, `password`, `password_confirmation`
    - **Response**: `200 OK` { `status` }

---

## 👤 Mandatory Profile Setup

> [!IMPORTANT]
> **A user MUST complete their profile setup before they can create notes or post comments.**
> The system checks if a `username` is set. If not, the user will receive a `403 Forbidden` error when trying to post.
> **Note**: The `name` field is now set during this setup phase, not at registration.

### Profile Setup Endpoint
- `POST /api/profile/setup`: Used for one-time initialization.
    - **Request**:
        - `name` (required, max 20)
        - `username` (required, unique, 3-20 chars, alphanumeric/underscores)
        - `bio` (optional, max 200)
        - `gender` (required: `male` or `female`)
    - **Response**: `201 Created`
        - `message`: "Profile setup successfully."
        - `user`: { `id`, `name`, `username`, `bio`, `gender`, `email` }

### Profile Management
- `GET /api/profile`: Get current user's profile details.
    - **Response**: `200 OK` { `user`: { `id`, `name`, `username`, `bio`, `gender`, `email` } }
- `PUT /api/profile`: Update name and bio.
    - **Request**:
        - `name` (required, max 20)
        - `bio` (optional, max 200)
    - **Response**: `200 OK` { `message`, `user` }

---

## 📝 Notes & Interaction

Notes are the core content of the app. They can be public or private.

### Notes Endpoints
- `GET /api/notes`: List current user's notes (paginated).
    - **Query Params**: `q` (search), `tag` (slug)
    - **Response**: `200 OK` { `data`: [NoteResource], `links`, `meta` }
- `GET /api/notes/feed`: Get a feed of notes from followed users.
    - **Query Params**: `q` (search), `tag` (slug)
    - **Response**: `200 OK` { `data`: [NoteResource], `links`, `meta` }
- `POST /api/notes`: Create a new note (**Requires Profile Setup**).
    - **Request**:
        - `title` (required, max 50)
        - `content` (optional)
        - `is_public` (boolean, default: false)
        - `tags` (array of strings, optional)
    - **Response**: `201 Created` [NoteResource]
- `GET /api/notes/{id}`: View a specific note.
    - **Response**: `200 OK` [NoteResource]
- `PUT /api/notes/{id}`: Update a note.
    - **Request**: `title`, `content`, `is_public`, `tags`
    - **Response**: `200 OK` [NoteResource]
- `DELETE /api/notes/{id}`: Delete a note.
    - **Response**: `200 OK` { `message`: "Note deleted successfully" }
- `GET /api/public-notes`: View public notes without follow restrictions.
    - **Query Params**: `q`, `tag`
    - **Response**: `200 OK` { `data`: [NoteResource], `links`, `meta` }

**NoteResource Structure**:
- `id`, `title`, `content`, `is_public`, `likes_count`, `liked_by_me`, `created_at`
- `user`: { `id`, `name`, `username` }
- `tags`: [ { `id`, `name`, `slug` } ]

### Likes
- `POST /api/notes/{id}/like`: Like a note.
    - **Response**: `200 OK` { `message`: "Liked" }
- `DELETE /api/notes/{id}/like`: Unlike a note.
    - **Response**: `200 OK` { `message`: "Unliked" }
- `GET /api/notes/liked`: Get notes liked by the current user.
    - **Response**: `200 OK` { `data`: [NoteResource] }

### Comments
- `GET /api/notes/{id}/comments`: List comments for a note.
    - **Response**: `200 OK` { `data`: [CommentResource] }
- `POST /api/notes/{id}/comments`: Post a comment (**Requires Profile Setup**).
    - **Request**: `body` (required, max 100)
    - **Response**: `201 Created` [CommentResource]
- `DELETE /api/comments/{id}`: Delete a comment.
    - **Response**: `200 OK` { `message`: "Comment deleted" }

**CommentResource Structure**:
- `id`, `body`, `created_at`
- `user`: { `id`, `name` }

---

## 🏷️ Tags

- `GET /api/tags`: List all tags.
    - **Response**: `200 OK` { `data`: [TagResource] }
- `GET /api/tags/popular`: Get trending tags.
    - **Response**: `200 OK` { `data`: [TagResource] }
- `GET /api/tags/{id}`: View details for a specific tag.
    - **Response**: `200 OK` [TagResource]

**TagResource Structure**:
- `id`, `name`, `slug`, `notes_count`

---

## 👥 Following System

- `GET /api/follow`: List users the current user is following.
    - **Response**: `200 OK` { `data`: [FollowResource] }
- `POST /api/follow`: Follow a user.
    - **Request**: `user_id` (required)
    - **Response**: `201 Created` [FollowResource]
- `DELETE /api/follow/{id}`: Unfollow a user.
    - **Response**: `200 OK` { `message`: "Unfollowed successfully" }

---

## 🛠️ Typical Workflow for Frontend

1. **Register/Login**: Obtain the Sanctum token.
2. **Check Profile**: Call `GET /api/profile`.
3. **Onboarding**: If `username` is null, redirect the user to the Profile Setup screen.
4. **App Access**: Once profile is setup, allow the user to create notes and interact with others.
5. **Feed**: Load `GET /api/notes/feed` for the home screen.

