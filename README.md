# Easy Notes API

A modern, feature-rich API for managing personal notes with social capabilities. Built with Laravel, this API provides a robust foundation for note-taking applications with advanced social features.

## 🚀 Features

### ✅ Core Features

#### Authentication & User Management
- **User Registration**: Register new users with email and password
- **User Login**: Authenticate users and issue personal access tokens
- **User Logout**: Revoke access tokens
- **Email Verification**: Verify user email addresses

#### User Profiles
- **Profile Setup**: First-time profile setup with username, name, bio, and gender
- **Profile Update**: Update name and bio after initial setup
- **Profile View**: View authenticated user's profile with stats

#### Note Management
- **Create Notes**: Create notes with title, content, and visibility settings
- **Update Notes**: Modify note content and visibility
- **Delete Notes**: Remove notes from the system
- **List Own Notes**: View all notes created by the authenticated user
- **Search Notes**: Search notes by title and content
- **Public Notes**: Browse all public notes sorted by popularity

#### Tagging System
- **Tag Management**: Create, update, and delete tags
- **Tag Notes**: Add multiple tags to notes during creation or update
- **Filter by Tag**: Filter notes by tag slug
- **Popular Tags**: Discover most-used tags ordered by note count
- **Auto Tag Creation**: Tags are automatically created when adding new tag names to notes
- **Tag Reuse**: Existing tags are matched by slug to prevent duplicates

#### Comments System
- **Add Comments**: Comment on public notes or own private notes
- **Delete Comments**: Remove own comments or comments on own notes
- **List Comments**: View all comments on a note

#### Likes System
- **Like Notes**: Like public notes or own private notes
- **Unlike Notes**: Remove likes from notes
- **Liked Notes List**: View all notes liked by the authenticated user

#### Following System
- **Follow Users**: Follow other users to see their notes in your feed
- **Unfollow Users**: Stop following users
- **List Following**: View list of users you follow

#### Personal Feed
- **Following Feed**: View public notes from users you follow
- **Feed Search**: Search within your personal feed
- **Feed Filtering**: Filter feed by tags

## 🛠️ Tech Stack

- **Backend Framework**: [Laravel](https://laravel.com) 12.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/PostgreSQL/SQLite support
- **API Specification**: RESTful API
- **Testing**: PHPUnit/Pest

## 📡 API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user |
| POST | `/api/login` | Authenticate user |
| POST | `/api/logout` | Log out user (requires auth) |
| POST | `/api/forgot-password` | Request password reset link |
| POST | `/api/reset-password` | Reset password with token |

### Profile
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/profile/setup` | Setup profile for first time |
| PUT | `/api/profile` | Update name and bio |
| GET | `/api/profile` | View authenticated user's profile |

### Notes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notes` | List own notes (supports `?q=search&tag=slug`) |
| GET | `/api/notes/my` | List own notes (alternative endpoint) |
| GET | `/api/notes/feed` | Get feed from followed users |
| GET | `/api/notes/liked` | Get liked notes |
| POST | `/api/notes` | Create a new note |
| GET | `/api/notes/{note}` | Get a specific note |
| PUT | `/api/notes/{note}` | Update a note |
| DELETE | `/api/notes/{note}` | Delete a note |

### Public Notes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/public-notes` | List all public notes (supports `?q=search&tag=slug`) |
| GET | `/api/public-notes/{note}` | View a specific public note |

### Tags
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tags` | List all tags with note counts |
| GET | `/api/tags/popular` | Get popular tags (top 20 by note count) |
| GET | `/api/tags/{tag}` | View a specific tag |
| POST | `/api/tags` | Create a new tag |
| PUT | `/api/tags/{tag}` | Update a tag |
| DELETE | `/api/tags/{tag}` | Delete a tag |

### Comments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notes/{note}/comments` | List comments for a note |
| POST | `/api/notes/{note}/comments` | Add a comment to a note |
| DELETE | `/api/comments/{comment}` | Delete a comment |

### Likes
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/notes/{note}/like` | Like a note |
| DELETE | `/api/notes/{note}/like` | Unlike a note |

### Following
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/follow` | List users you follow |
| POST | `/api/follow` | Follow a user |
| DELETE | `/api/follow/{user}` | Unfollow a user |

## 📝 Request/Response Examples

### Create Note with Tags
```json
// POST /api/notes
{
    "title": "My Laravel Tips",
    "content": "Here are some useful Laravel tips...",
    "is_public": true,
    "tags": ["Laravel", "PHP", "Tips"]
}
```

### Response with Tags
```json
{
    "data": {
        "id": 1,
        "title": "My Laravel Tips",
        "content": "Here are some useful Laravel tips...",
        "is_public": true,
        "likes_count": 0,
        "liked_by_me": false,
        "tags": [
            { "id": 1, "name": "Laravel", "slug": "laravel" },
            { "id": 2, "name": "PHP", "slug": "php" },
            { "id": 3, "name": "Tips", "slug": "tips" }
        ],
        "created_at": "2026-03-06T07:00:00.000000Z"
    }
}
```

### Filter Notes by Tag
```
GET /api/notes?tag=laravel
GET /api/public-notes?tag=php
```

### Popular Tags Response
```json
{
    "data": [
        { "id": 1, "name": "Laravel", "slug": "laravel", "notes_count": 42 },
        { "id": 2, "name": "PHP", "slug": "php", "notes_count": 35 },
        { "id": 3, "name": "Vue", "slug": "vue", "notes_count": 28 }
    ]
}
```

## 🛡️ Security

- All API endpoints are protected with Laravel Sanctum token authentication
- Input validation for all user inputs
- Protection against common web vulnerabilities
- Profile setup required before creating notes/comments

## 🧪 Testing

The API includes comprehensive testing with 82 tests and 187 assertions:
- Feature tests for all API endpoints
- Unit tests for individual components
- Continuous integration ready

Run tests with:
```bash
php artisan test
```

## 🚀 Getting Started

1. Clone the repository
2. Install dependencies: `composer install && npm install`
3. Set up environment: `cp .env.example .env && php artisan key:generate`
4. Run migrations: `php artisan migrate`
5. Start the server: `php artisan serve`

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for more details.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

If you have any questions or feedback, feel free to open an issue in the repository.