# Easy Notes API

A modern, feature-rich API for managing personal notes with social capabilities. Built with Laravel, this API provides a robust foundation for note-taking applications with advanced social features.

## 🚀 Features

### ✅ Core Features (Implemented)
- **User Authentication**: Secure registration and login system
- **Note Management**: Create, read, update, and delete notes
- **Comments System**: Users can comment on notes
- **Likes System**: Users can like notes
- **Personal Access Tokens**: Secure API authentication via Laravel Sanctum
- **RESTful API Design**: Clean and consistent API endpoints

### 🔧 In Progress / Planned Features

#### Users & Social Features
- **User Profiles**: Public profile endpoint with username, bio, and stats (notes count, likes, comments)
- **Following System**: Allow users to follow other users with endpoints for follow/unfollow and listing followers/following
- **Personal Feed**: Display notes from users you follow

#### Notifications
- **Activity Alerts**: Receive notifications when someone likes your note
- **Comment Alerts**: Get notified when someone comments on your note

#### Discovery & Ranking
- **Trending Notes**: Identify and display trending/hot notes
- **Ranking Algorithm**: Rank notes based on likes and recency (e.g., many likes in last 24 hours)
- **Trending Endpoints**: `/notes/trending` and `/notes/recent` for discovery

#### Tagging & Categories
- **Tagging System**: Add tags table with many-to-many relationship to notes
- **Filtering**: Filter notes by tags
- **Popular Tags**: Show popular tags for discovery
- **Tag Management**: Endpoints to manage note tags

## 🛠️ Tech Stack

- **Backend Framework**: [Laravel](https://laravel.com) 12.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/PostgreSQL/SQLite support
- **API Specification**: RESTful API
- **Testing**: PHPUnit/Pest

## 📡 API Endpoints (Current)

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Authenticate user
- `POST /api/logout` - Log out user

### Notes
- `GET /api/notes` - Get all notes
- `POST /api/notes` - Create a new note
- `GET /api/notes/{id}` - Get a specific note
- `PUT /api/notes/{id}` - Update a note
- `DELETE /api/notes/{id}` - Delete a note

### Comments
- `POST /api/notes/{id}/comments` - Add a comment to a note
- `DELETE /api/comments/{id}` - Delete a comment

### Likes
- `POST /api/notes/{id}/like` - Like a note
- `POST /api/notes/{id}/unlike` - Unlike a note

## 🚧 Roadmap

### Phase 1: Social Features
- [ ] User profiles with customizable names and bios
- [ ] Following/unfollowing functionality
- [ ] Personalized feed of notes from followed users

### Phase 2: Enhanced Discovery
- [ ] Trending notes algorithm
- [ ] Note tagging system
- [ ] Advanced filtering and search

### Phase 3: Engagement
- [ ] Real-time notifications
- [ ] Rich text formatting for notes
- [ ] Media attachments for notes

## 🛡️ Security

- All API endpoints are protected with Laravel Sanctum token authentication
- Input validation for all user inputs
- Protection against common web vulnerabilities

## 🧪 Testing

The API includes comprehensive testing with:
- Feature tests for API endpoints
- Unit tests for individual components
- Continuous integration ready

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