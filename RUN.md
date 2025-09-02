# FindMyAsso - Plateforme de Protection Animale

## Description

FindMyAsso est une plateforme complète dédiée à la protection animale qui connecte les animaux en détresse avec des familles aimantes, des associations engagées et des vétérinaires solidaires.

## Fonctionnalités Principales

### 🏠 Familles d'Accueil
- **Inscription multi-étapes** : Formulaire en 4 étapes pour créer un profil complet
- **Questionnaire détaillé** : Évaluation de l'environnement, expérience et disponibilité
- **Gestion de la capacité** : Définition du nombre d'animaux et espèces acceptées
- **Visibilité contrôlée** : Profils visibles aux associations selon les préférences
- **Localisation** : Géolocalisation pour faciliter les contacts locaux

### 🏢 Associations
- **Liste publique** : Affichage des associations approuvées avec filtres
- **Profils détaillés** : Informations complètes sur chaque association
- **Capacités d'accueil** : Gestion des places disponibles par espèce
- **Animaux à adopter** : Catalogue des animaux disponibles
- **Système de dons** : Collecte de fonds et dons en nature

### 🐾 Animaux
- **Catalogue d'adoption** : Recherche et filtrage avancés
- **Profils détaillés** : Photos, descriptions, caractéristiques
- **Demandes d'adoption** : Système de candidature pour les adoptants
- **Suivi médical** : Vaccination, stérilisation, identification

### 👨‍⚕️ Vétérinaires Solidaires
- **Réseau de soins** : Vétérinaires proposant des tarifs réduits
- **Services spécialisés** : Consultations, urgences, soins préventifs
- **Géolocalisation** : Recherche par proximité

## Architecture Technique

### Backend
- **Framework** : Symfony 6.4 LTS
- **PHP** : Version 8.2+
- **Base de données** : PostgreSQL 15+
- **ORM** : Doctrine avec UUID comme clés primaires
- **Sécurité** : Symfony Security avec rôles hiérarchiques

### Frontend
- **Template Engine** : Twig
- **CSS Framework** : Tailwind CSS
- **JavaScript** : Stimulus pour l'interactivité
- **Design** : Interface responsive et moderne

### Entités Principales

#### User
- Gestion des utilisateurs avec rôles multiples
- Authentification et autorisation
- Profils liés selon le rôle

#### FosterProfile
- Profils des familles d'accueil
- Questionnaire détaillé en JSON
- Gestion de la capacité et disponibilité
- Géolocalisation

#### Association
- Profils des associations de protection
- Capacités d'accueil par espèce
- Gestion des animaux et adoptions
- Système d'approbation

#### Animal
- Profils des animaux à adopter
- Photos multiples
- Suivi médical et comportemental
- Statuts d'adoption

## Installation et Configuration

### Prérequis
- PHP 8.2+
- PostgreSQL 15+
- Composer
- Node.js et npm
- Docker (optionnel)

### Installation

1. **Cloner le projet**
```bash
git clone <repository-url>
cd FindMyAsso
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Installer les dépendances JavaScript**
```bash
npm install
```

4. **Configuration de l'environnement**
```bash
cp .env .env.local
# Éditer .env.local avec vos paramètres de base de données
```

5. **Base de données**
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les données de test
php bin/console doctrine:fixtures:load
```

6. **Assets**
```bash
npm run build
```

7. **Démarrer le serveur**
```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

### Configuration Docker (Optionnel)

```bash
# Démarrer les services
docker-compose up -d

# Les services seront disponibles sur :
# - Application : http://localhost:8000
# - PostgreSQL : localhost:5432
```

## Utilisation

### Comptes de Test

Après avoir chargé les fixtures, vous pouvez utiliser ces comptes :

#### Administrateur
- **Email** : admin@findmyasso.fr
- **Mot de passe** : admin123
- **Rôle** : ROLE_ADMIN

#### Association
- **Email** : asso1@example.com
- **Mot de passe** : password1
- **Rôle** : ROLE_ASSOCIATION

#### Famille d'Accueil
- **Email** : foster1@example.com
- **Mot de passe** : password1
- **Rôle** : ROLE_FOSTER

### Parcours Utilisateur

#### 1. Famille d'Accueil
1. Aller sur `/foster/register`
2. Remplir le formulaire en 4 étapes :
   - Informations personnelles
   - Capacité et localisation
   - Questionnaire détaillé
   - Validation et visibilité
3. Se connecter et gérer son profil

#### 2. Association
1. S'inscrire via le formulaire d'association
2. Attendre l'approbation admin
3. Gérer les animaux et adoptions
4. Consulter les familles d'accueil disponibles

#### 3. Adoptant
1. Parcourir les animaux disponibles
2. Filtrer par critères
3. Faire une demande d'adoption
4. Suivre le processus

## API et Intégrations

### Stripe
- Intégration pour les dons en ligne
- Gestion des paiements sécurisés
- Webhooks pour le suivi des transactions

### Géolocalisation
- Calcul de distance entre utilisateurs
- Recherche par proximité
- Intégration avec les services de cartographie

## Sécurité

### Rôles et Permissions
- **ROLE_ADMIN** : Accès complet à l'administration
- **ROLE_ASSOCIATION** : Gestion des associations et animaux
- **ROLE_FOSTER** : Gestion des profils famille d'accueil
- **ROLE_VETERINARIAN** : Gestion des profils vétérinaires
- **ROLE_USER** : Accès de base aux fonctionnalités publiques

### Protection des Données
- Chiffrement des mots de passe
- Validation des données d'entrée
- Protection CSRF
- Gestion sécurisée des fichiers uploadés

## Développement

### Structure du Projet
```
src/
├── Controller/          # Contrôleurs Symfony
├── Entity/             # Entités Doctrine
├── Form/               # Formulaires Symfony
├── Repository/         # Repositories Doctrine
├── Service/            # Services métier
└── DataFixtures/       # Données de test

templates/
├── admin/              # Templates d'administration
├── foster/             # Templates familles d'accueil
├── public/             # Templates publics
└── security/           # Templates d'authentification

assets/
├── controllers/        # Contrôleurs Stimulus
├── styles/            # Fichiers CSS
└── vendor/            # Dépendances JavaScript
```

### Commandes Utiles

```bash
# Générer une migration
php bin/console make:migration

# Créer une entité
php bin/console make:entity

# Créer un contrôleur
php bin/console make:controller

# Vider le cache
php bin/console cache:clear

# Lancer les tests
php bin/phpunit
```

### Standards de Code
- PSR-12 pour le PHP
- ESLint pour le JavaScript
- Prettier pour le formatage
- PHPStan pour l'analyse statique

## Déploiement

### Production
1. Configurer les variables d'environnement
2. Optimiser l'autoloader : `composer install --no-dev --optimize-autoloader`
3. Compiler les assets : `npm run build`
4. Vider le cache : `php bin/console cache:clear --env=prod`
5. Configurer le serveur web (Apache/Nginx)

### Variables d'Environnement
```env
DATABASE_URL="postgresql://user:password@localhost:5432/findmyasso"
APP_ENV=prod
APP_SECRET=your-secret-key
STRIPE_PUBLIC_KEY=your-stripe-public-key
STRIPE_SECRET_KEY=your-stripe-secret-key
```

## Support et Contribution

### Documentation
- Documentation Symfony : https://symfony.com/doc
- Documentation Tailwind : https://tailwindcss.com/docs
- Documentation Stimulus : https://stimulus.hotwired.dev

### Issues et Bugs
- Utiliser le système d'issues du projet
- Fournir des logs détaillés
- Décrire les étapes de reproduction

### Contribution
1. Fork le projet
2. Créer une branche feature
3. Commiter les changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

## Contact

- **Email** : contact@findmyasso.fr
- **Site web** : https://findmyasso.fr
- **Support** : support@findmyasso.fr

---

*FindMyAsso - Connecter les animaux en détresse avec des familles aimantes* 🐾
