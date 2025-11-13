# Application de Gestion de Mariage

Application web complète pour la gestion d'un événement de mariage, développée avec Laravel 12. Cette application permet de gérer les invités, les tables de réception, les préférences alimentaires et l'envoi d'invitations via WhatsApp.

## 🎯 Fonctionnalités principales

### Gestion des invités
- Création, modification et suppression d'invités
- Gestion des couples (invités avec partenaire)
- Attribution de tables de réception
- Suivi du statut RSVP (confirmé, en attente)
- Envoi d'invitations personnalisées via WhatsApp
- Export PDF avec liste des invités et leurs tables associées
- Import/Export CSV pour gestion en masse
- Recherche et filtrage avancés

### Gestion des tables de réception
- Création et gestion des tables
- Attribution automatique ou manuelle des invités
- Export PDF des tables
- Import/Export CSV

### Gestion des préférences
- Enregistrement des préférences alimentaires des invités
- Gestion des boissons par catégorie
- Statistiques des préférences par boisson et par catégorie
- Export PDF des statistiques de préférences

### Invitations numériques
- Génération d'invitations personnalisées avec token unique
- Envoi automatique via WhatsApp
- Page publique pour confirmation RSVP
- Téléchargement des invitations en PDF
- Gestion des préférences directement depuis l'invitation

### Authentification et sécurité
- Système de connexion sécurisé
- Réinitialisation de mot de passe par email avec code de vérification
- Gestion du profil utilisateur
- Changement de mot de passe
- Gestion des utilisateurs administrateurs

### Dashboard
- Statistiques en temps réel (invités totaux, confirmés, en attente)
- Graphiques de confirmation hebdomadaires et mensuels
- Vue d'ensemble des tables de réception

## 🛠️ Technologies utilisées

- **Backend**: Laravel 12 (PHP 8.2+)
- **Base de données**: MySQL/PostgreSQL/SQLite
- **PDF**: DomPDF (barryvdh/laravel-dompdf)
- **WhatsApp**: UltraMsg WhatsApp PHP SDK
- **Frontend**: Bootstrap 5, JavaScript vanilla
- **Authentification**: Laravel Session Authentication

## 📋 Prérequis

- PHP 8.2 ou supérieur
- Composer
- Node.js et npm (pour les assets)
- Base de données (MySQL, PostgreSQL ou SQLite)
- Serveur web (Apache/Nginx) ou PHP built-in server
- Configuration WhatsApp API (UltraMsg) pour l'envoi de messages

## 🚀 Installation

1. **Cloner le dépôt**
```bash
git clone https://github.com/KevinKpekpe/maritalapp.git
cd appmariage
```

2. **Installer les dépendances**
```bash
composer install
npm install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**
Éditez le fichier `.env` et configurez vos paramètres de base de données :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_votre_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

5. **Exécuter les migrations**
```bash
php artisan migrate
```

6. **Optionnel : Charger des données de démonstration**
```bash
php artisan db:seed
```

7. **Lancer le serveur de développement**
```bash
php artisan serve
```

L'application sera accessible à l'adresse `http://localhost:8000`

## ⚙️ Configuration

### Configuration WhatsApp (UltraMsg)

Pour activer l'envoi d'invitations via WhatsApp, configurez les variables suivantes dans votre fichier `.env` :

```env
ULTRA_MSG_INSTANCE_ID=votre_instance_id
ULTRA_MSG_TOKEN=votre_token
ULTRA_MSG_API_URL=https://api.ultramsg.com
```

### Configuration Email

Pour la réinitialisation de mot de passe, configurez votre service d'email dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 📁 Structure du projet

```
appmariage/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Contrôleurs de l'application
│   │   └── Middleware/       # Middleware personnalisés
│   ├── Models/               # Modèles Eloquent
│   ├── Mail/                 # Classes Mailables
│   └── Services/
│       └── WhatsApp/          # Service d'envoi WhatsApp
├── database/
│   ├── migrations/            # Migrations de base de données
│   └── seeders/               # Seeders pour données de test
├── resources/
│   ├── views/                 # Vues Blade
│   ├── css/                   # Styles CSS
│   └── js/                    # Scripts JavaScript
├── routes/
│   └── web.php                # Routes web
└── public/                    # Fichiers publics
```

## 🔐 Comptes par défaut

Après avoir exécuté les seeders, vous pouvez vous connecter avec :
- **Email**: admin@example.com
- **Mot de passe**: password (à changer après la première connexion)

## 📊 Fonctionnalités détaillées

### Export/Import

- **Export PDF des invités** : Liste complète avec noms et tables associées
- **Export PDF des tables** : Liste de toutes les tables de réception
- **Export PDF des statistiques** : Préférences par boisson et catégorie
- **Import CSV** : Import en masse d'invités et de tables avec validation

### Formatage des numéros de téléphone

L'application gère automatiquement le formatage des numéros de téléphone internationaux, avec support des préfixes internationaux (1-3 chiffres) et application automatique du préfixe par défaut (243) si nécessaire.

## 🧪 Tests

```bash
php artisan test
```

## 📝 License

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Auteur

Développé par **SpectreCoding**

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 📞 Support

Pour toute question ou problème, veuillez ouvrir une issue sur le dépôt GitHub.

---

**Note**: Cette application est conçue pour la gestion d'événements de mariage. Assurez-vous de respecter les réglementations locales concernant l'envoi de messages WhatsApp et la collecte de données personnelles.
