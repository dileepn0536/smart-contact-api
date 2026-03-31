# Smart Contact API

A RESTful API built with Symfony 8 and JWT Authentication.

## Features
- JWT Authentication
- Contact CRUD Operations
- PostgreSQL Database
- Input Validation
- Proper HTTP Status Codes

## Tech Stack
- PHP 8.4
- Symfony 8
- PostgreSQL
- LexikJWTAuthenticationBundle
- Doctrine ORM

## API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/login_check | Login and get JWT token |
| GET | /api/contacts | Get all contacts |
| GET | /api/contacts/{id} | Get single contact |
| POST | /api/contact | Create contact |
| PUT | /api/contacts/{id} | Update contact |
| PATCH | /api/contacts/{id} | Partial update |
| DELETE | /api/contacts/{id} | Delete contact |
