# API Platform — Book & User Management

Projet personnel d'apprentissage autour de **API Platform 3.x**, **Symfony**, **VichUploaderBundle** et du pattern **DTO / State Provider / State Processor**.

## Stack technique

- PHP 8.3
- Symfony 8.1
- API Platform 4.x
- Doctrine ORM
- VichUploaderBundle
- Mysql

## Objectifs du projet

Explorer les patterns recommandés par API Platform 3 :

- Séparation entre entité Doctrine et ressource API (via une classe `ApiResource` dédiée)
- Upload de fichiers en `multipart/form-data` (couverture de livre, photo utilisateur)
- State Providers et State Processors pour découpler la logique métier
- DTOs d'entrée (`UserCreateInput`) pour la validation et la désérialisation

## Architecture

```
src/
├── ApiResource/
│   └── UserResource.php          # Classe API Platform (pas l'entité Doctrine)
├── Dto/
│   └── Input/
│       └── UserCreateInput.php   # DTO de création utilisateur (multipart)
├── Entity/
│   ├── Book.php                  # Entité avec upload VichUploader
│   └── User.php                  # Entité Doctrine pure
├── Repository/
│   └── BookRepository.php
└── State/
    ├── Processor/
    │   └── UserCreateProcessor.php
    └── Provider/
        └── UserProvider.php
```

### Deux approches d'exposition API

Le projet illustre volontairement deux approches :

**`Book`** — L'entité Doctrine est directement la ressource API Platform. Approche simple, adaptée aux petits modèles sans logique complexe.

**`User`** — L'entité Doctrine (`User`) est découplée de la ressource API (`UserResource`). Un DTO d'entrée (`UserCreateInput`) gère la désérialisation, un `UserCreateProcessor` gère la persistance, et un `UserProvider` gère la lecture. Approche recommandée pour les cas réels.

## Upload de fichiers

### Book — couverture

L'upload est géré par VichUploaderBundle directement sur l'entité :

- `imageFile` : champ `File` (non persisté, géré par Vich)
- `imageName` : nom du fichier stocké en base (`writable: false` côté API)
- `updatedAt` : mis à jour à chaque upload pour invalider le cache HTTP

Le champ `imageName` est marqué `#[ApiProperty(writable: false)]` pour qu'il ne soit pas exposé en écriture. C'est Vich qui le renseigne automatiquement.

### User — photo de profil

Géré via le DTO `UserCreateInput` et le `UserCreateProcessor`. Le mapping entre le fichier uploadé et l'entité est à la charge du processor.

## Endpoints

| Méthode | URI | Description |
|---------|-----|-------------|
| `GET` | `/api/books` | Liste des livres |
| `POST` | `/api/books` | Créer un livre (multipart/form-data) |
| `PUT` | `/api/books/{id}` | Modifier un livre (multipart/form-data) |
| `GET` | `/api/users` | Liste des utilisateurs |
| `GET` | `/api/users/{id}` | Détail d'un utilisateur |
| `POST` | `/api/users` | Créer un utilisateur (multipart/form-data) |

## Installation

```bash
git clone <repo>
cd <repo>
composer install
```

Configurer `.env.local` :

```dotenv
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/api_platform_demo"
```

Créer la base et appliquer les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Configurer VichUploader dans `config/packages/vich_uploader.yaml` :

```yaml
vich_uploader:
    db_driver: orm
    mappings:
        book_images:
            uri_prefix: /uploads/book_images
            upload_destination: '%kernel.project_dir%/public/uploads/book_images'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
```

Démarrer le serveur :

```bash
symfony server:start
```

La documentation interactive est disponible sur `https://localhost:8000/api`.

## Points notables

**`#[ApiProperty(writable: false)]` sur `imageName`** — Sans cela, API Platform tente de désérialiser `imageName` depuis la requête, ce qui crée un conflit avec Vich.

**`updatedAt` obligatoire pour Vich** — VichUploader ne re-persiste pas le fichier si aucun champ de l'entité n'a changé. Mettre `updatedAt` à jour dans `setImageFile()` est indispensable.

**`PUT` avec multipart** — API Platform ne supporte pas nativement le `PUT` en `multipart/form-data` sans configuration explicite (`inputFormats`). C'est déclaré sur l'opération.

**Découplage `UserResource` / `User`** — `UserResource` expose un champ `photoUrl` (URL publique) alors que `User` stocke `photo` (nom de fichier). La transformation est à la charge du Provider.
