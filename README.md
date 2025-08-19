# FindMyAsso - Plateforme de Protection Animale

FindMyAsso est une plateforme web complète dédiée à la protection animale, permettant de mettre en relation les associations, les familles d'accueil, les vétérinaires et les adoptants potentiels.

## 🚀 Fonctionnalités

### Pour le Public
- **Découverte d'animaux** : Parcourir les animaux disponibles à l'adoption
- **Recherche d'associations** : Trouver des associations de protection animale
- **Demandes d'adoption** : Formulaires complets pour demander l'adoption d'un animal
- **Familles d'accueil** : Inscription et recherche de familles d'accueil
- **Vétérinaires solidaires** : Annuaire de vétérinaires partenaires
- **Donations** : Système de dons pour soutenir les associations
- **Carte interactive** : Localisation géographique des acteurs

### Pour les Associations
- **Gestion des animaux** : Ajout, modification et suivi des animaux
- **Gestion des demandes** : Traitement des demandes d'adoption
- **Communication** : Contact avec les adoptants potentiels
- **Statistiques** : Suivi des activités et performances

### Pour les Utilisateurs
- **Compte personnel** : Gestion des demandes d'adoption
- **Suivi** : État des demandes et communications
- **Préférences** : Sauvegarde des recherches et favoris

## 🛠️ Technologies Utilisées

- **Backend** : Symfony 6.4
- **Frontend** : Twig + Tailwind CSS
- **Base de données** : MySQL/PostgreSQL
- **Assets** : Symfony Asset Mapper + PostCSS
- **Sécurité** : Symfony Security Bundle
- **Formulaires** : Symfony Form Component
- **Validation** : Symfony Validator Component

## 📋 Prérequis

- PHP 8.1+
- Composer
- Symfony CLI
- Base de données (MySQL/PostgreSQL)
- Node.js 18+ (pour les assets)

## 🚀 Installation

### 1. Cloner le projet
```bash
git clone [URL_DU_REPO]
cd FindMyAsso
```

### 2. Installer les dépendances PHP
```bash
composer install
```

### 3. Installer les dépendances Node.js
```bash
npm install
```

### 4. Configuration de l'environnement
```bash
# Copier le fichier d'environnement
cp .env .env.local

# Éditer .env.local avec vos paramètres de base de données
DATABASE_URL="mysql://user:password@127.0.0.1:3306/findmyasso"
```

### 5. Créer la base de données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures (optionnel)
php bin/console doctrine:fixtures:load
```

### 6. Compiler les assets
```bash
# Compiler les assets CSS/JS
npm run build
```

### 7. Créer les dossiers d'upload
```bash
# Créer les dossiers pour les uploads
mkdir -p public/uploads/animals
mkdir -p public/uploads/associations
mkdir -p public/uploads/users
```

### 8. Démarrer le serveur
```bash
# Démarrer le serveur Symfony
symfony server:start

# Ou avec PHP
php -S localhost:8000 -t public/
```

## 📁 Structure du Projet

```
FindMyAsso/
├── assets/                 # Assets frontend (CSS, JS)
├── bin/                    # Exécutables Symfony
├── config/                 # Configuration de l'application
├── migrations/             # Migrations de base de données
├── public/                 # Dossier public (point d'entrée)
├── src/                    # Code source de l'application
│   ├── Controller/         # Contrôleurs
│   ├── Entity/            # Entités Doctrine
│   ├── Form/              # Formulaires
│   ├── Repository/        # Repositories
│   └── Service/           # Services métier
├── templates/              # Templates Twig
│   ├── adoption/          # Templates d'adoption
│   ├── association/       # Templates d'association
│   ├── donation/          # Templates de donation
│   ├── public/            # Templates publics
│   └── security/          # Templates de sécurité
├── tests/                  # Tests unitaires et fonctionnels
└── var/                    # Fichiers temporaires et cache
```

## 🔧 Configuration

### Variables d'environnement importantes

```env
# Base de données
DATABASE_URL="mysql://user:password@127.0.0.1:3306/findmyasso"

# Sécurité
APP_SECRET="votre_secret_ici"

# Uploads
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=10M

# Email (optionnel)
MAILER_DSN=smtp://localhost:1025
```

### Configuration des uploads

Les fichiers sont stockés dans `public/uploads/` avec la structure suivante :
- `animals/` : Photos des animaux
- `associations/` : Logos et bannières des associations
- `users/` : Photos de profil des utilisateurs

## 📱 Utilisation

### Navigation principale
- **Accueil** (`/`) : Page d'accueil avec présentation
- **Associations** (`/associations`) : Liste des associations
- **Adoptions** (`/adoptions`) : Animaux disponibles
- **Familles d'accueil** (`/familles-accueil`) : Inscription et recherche
- **Vétérinaires** (`/veterinaires-solidaires`) : Annuaire vétérinaire
- **Donations** (`/donations`) : Faire un don
- **Carte** (`/carte`) : Carte interactive
- **À propos** (`/a-propos`) : Informations sur la plateforme
- **Contact** (`/contact`) : Formulaire de contact

### Processus d'adoption
1. **Découverte** : Parcourir les animaux disponibles
2. **Sélection** : Choisir un animal et consulter ses détails
3. **Demande** : Remplir le formulaire de demande d'adoption
4. **Suivi** : Suivre l'état de la demande
5. **Contact** : Communication avec l'association

## 🧪 Tests

```bash
# Lancer tous les tests
php bin/phpunit

# Lancer les tests avec couverture
php bin/phpunit --coverage-html var/coverage
```

## 📊 Base de données

### Entités principales
- **User** : Utilisateurs de la plateforme
- **Association** : Associations de protection animale
- **Animal** : Animaux disponibles à l'adoption
- **AdoptionRequest** : Demandes d'adoption
- **FosterProfile** : Profils de familles d'accueil
- **VetProfile** : Profils de vétérinaires
- **Donation** : Dons et contributions
- **AnimalPhoto** : Photos des animaux

## 🔒 Sécurité

- Authentification et autorisation avec Symfony Security
- Validation des formulaires
- Protection CSRF
- Gestion des rôles utilisateur
- Validation des uploads de fichiers

## 🚀 Déploiement

### Production
```bash
# Optimiser l'environnement
composer install --no-dev --optimize-autoloader
npm run build

# Vider le cache
php bin/console cache:clear --env=prod

# Vérifier la configuration
php bin/console doctrine:schema:validate
```

### Docker (optionnel)
```bash
# Démarrer avec Docker Compose
docker-compose up -d

# Arrêter
docker-compose down
```

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📞 Support

Pour toute question ou problème :
- Ouvrir une issue sur GitHub
- Contacter l'équipe de développement
- Consulter la documentation Symfony

## 🔄 Mises à jour

```bash
# Mettre à jour les dépendances
composer update
npm update

# Mettre à jour la base de données
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear
```

---

**FindMyAsso** - Ensemble, protégeons et sauvons les animaux ! 🐾
