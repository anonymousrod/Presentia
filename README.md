# Presentia - EBER Platform

[![Laravel CI](https://github.com/anonymousrod/Presentia/actions/workflows/ci.yml/badge.svg)](https://github.com/anonymousrod/Presentia/actions/workflows/ci.yml)

Presentia est une plateforme de gestion d'EBER (Entités de Base d'Engagement et de Responsabilité).

## TECH-001 : Initialisation du projet & intégration du template

Ce ticket couvre l'initialisation du projet Laravel 11 et l'intégration du template Velzon.

### Prérequis

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Installation

1. Cloner le dépôt
2. Installer les dépendances PHP : `composer install`
3. Installer les dépendances JS : `npm install`
4. Copier le fichier `.env.example` en `.env` et configurer la base de données
5. Générer la clé d'application : `php artisan key:generate`
6. Lancer les migrations : `php artisan migrate`
7. Lancer le serveur de développement : `php artisan serve` et `npm run dev`

### Qualité du code

- **Linting** : Laravel Pint est utilisé pour assurer la conformité PSR-12.
  ```bash
  ./vendor/bin/pint --config pint.json
  ```
- **Tests** : Pest/PHPUnit pour les tests unitaires et de fonctionnalité.
  ```bash
  php artisan test
  ```

### Architecture

- **Laravel 11**
- **Vite** pour le bundling des assets.
- **Blade** pour le moteur de templating.
