# Book API

A REST API built with **Symfony** and **API Platform** for managing books, including cover image upload support via VichUploaderBundle.

## Stack

- PHP 8.x
- Symfony 6.x
- API Platform 3.x
- Doctrine ORM
- VichUploaderBundle

## Features

- List books (`GET /api/books`)
- Create a book with cover image (`POST /api/books`)
- Update a book with cover image (`PUT /api/books/{id}`)

## Book resource

| Field | Type | Access | Description |
|-------|------|--------|-------------|
| `id` | int | read | Auto-generated identifier |
| `title` | string | read/write | Book title |
| `description` | string | read/write | Book description |
| `imageName` | string | read | Stored filename (set automatically) |
| `imageFile` | File | write | Cover image to upload (multipart/form-data) |
| `createdAt` | DateTimeImmutable | — | Creation date |
| `updatedAt` | DateTimeImmutable | — | Last update date |

## Installation

```bash
composer install
```

Configure your `.env.local`:

```dotenv
DATABASE_URL="mysql://user:password@127.0.0.1:3306/book_api"
```

Run migrations:

```bash
php bin/console doctrine:migrations:migrate
```

## Image upload configuration

Images are handled by VichUploaderBundle. Configure the `book_images` mapping in `config/packages/vich_uploader.yaml`:

```yaml
vich_uploader:
    db_driver: orm
    mappings:
        book_images:
            uri_prefix: /uploads/books
            upload_destination: '%kernel.project_dir%/public/uploads/books'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
```

## Usage

### Create a book

```http
POST /api/books
Content-Type: multipart/form-data

title=Clean Code
description=A book about writing clean code
imageFile=<binary>
```

### List books

```http
GET /api/books
Accept: application/json
```

## API documentation

Interactive docs available at `/api` (Swagger UI) once the app is running.
