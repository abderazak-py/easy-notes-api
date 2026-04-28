# Easy Notes API - Frontend Development Guide for AI Agents

This guide provides everything an AI agent needs to know to build a frontend for the **Easy Notes API**. It covers authentication, mandatory profile setup, and all available endpoints.

## 🚀 Overview

Easy Notes is a social platform where users can share notes, follow others, like content, and engage through comments.

## 🔐 Authentication

The API uses **Laravel Sanctum** for authentication. All requests should include the `Accept: application/json` header. For authenticated requests, use the `Authorization: Bearer {token}` header.

### Auth Endpoints
- `POST /api/register`: Register a new user.
- `POST /api/login`: Log in and receive a Bearer token.
- `POST /api/logout`: Log out (requires authentication).
- `POST /api/forgot-password`: Request password reset link.
- `POST /api/reset-password`: Reset password using token.

---

## 👤 Mandatory Profile Setup

> [!IMPORTANT]
> **A user MUST complete their profile setup before they can create notes or post comments.**
> The system checks if a `username` is set. If not, the user will receive a `403 Forbidden` error when trying to post.

### Profile Setup Endpoint
- `POST /api/profile/setup`: Used for one-time initialization.
    - **Fields**:
        - `name` (required, max 20)
        - `username` (required, unique, 3-20 chars, alphanumeric/underscores)
        - `bio` (optional, max 200)
        - `gender` (required: `male` or `female`)

### Profile Management
- `GET /api/profile`: Get current user's profile details.
- `PUT /api/profile`: Update name and bio (cannot change username after setup).

---

## 📝 Notes & Interaction

Notes are the core content of the app. They can be public or private.

### Notes Endpoints
- `GET /api/notes`: List notes (paginated).
- `GET /api/notes/my`: Get current user's notes.
- `GET /api/notes/feed`: Get a feed of notes from followed users.
- `POST /api/notes`: Create a new note (**Requires Profile Setup**).
- `GET /api/notes/{id}`: View a specific note.
- `PUT /api/notes/{id}`: Update a note.
- `DELETE /api/notes/{id}`: Delete a note.
- `GET /api/public-notes`: View public notes without follow restrictions.

### Likes
- `POST /api/notes/{id}/like`: Like a note.
- `DELETE /api/notes/{id}/like`: Unlike a note.
- `GET /api/notes/liked`: Get notes liked by the current user.

### Comments
- `GET /api/notes/{id}/comments`: List comments for a note.
- `POST /api/notes/{id}/comments`: Post a comment (**Requires Profile Setup**).
- `DELETE /api/comments/{id}`: Delete a comment.

---

## 🏷️ Tags

Notes can be tagged to organize content.

- `GET /api/tags`: List all tags.
- `GET /api/tags/popular`: Get trending tags.
- `GET /api/tags/{id}`: View notes for a specific tag.
- `POST /api/tags`: Create a tag.
- `PUT /api/tags/{id}`: Update a tag.
- `DELETE /api/tags/{id}`: Delete a tag.

---

## 👥 Following System

Users can follow each other to build their feed.

- `GET /api/follow`: List followers/following.
- `POST /api/follow`: Follow a user (provide `user_id`).
- `DELETE /api/follow/{id}`: Unfollow a user.

---

## 🛠️ Typical Workflow for Frontend

1. **Register/Login**: Obtain the Sanctum token.
2. **Check Profile**: Call `GET /api/profile`.
3. **Onboarding**: If `username` is null, redirect the user to the Profile Setup screen.
4. **App Access**: Once profile is setup, allow the user to create notes and interact with others.
5. **Feed**: Load `GET /api/notes/feed` for the home screen.
