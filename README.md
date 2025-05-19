# 📚 PAF — Plateforme d’Apprentissage et de Formation

![Symfony](https://img.shields.io/badge/Symfony-7.2.6-black?logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.4.7-blue?logo=php)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?logo=mysql&logoColor=white)
![Made with ❤️ by Marceau](https://img.shields.io/badge/Made%20with-%E2%9D%A4%EF%B8%8F%20by%20Marceau-violet)

**PAF** est une plateforme pédagogique développée pour être déployée dans divers centres de formation. Conçue pour s’adapter à tous les secteurs d’activité, elle permet aux stagiaires d’accéder à leurs cours, exercices, documents de suivi et ressources pédagogiques en ligne.

---

## 🚀 Objectifs du projet

- Fournir une solution centralisée pour le suivi des apprenants.
- Offrir un accès simplifié et structuré aux contenus de formation.
- Intégrer des fonctionnalités adaptées aux réalités de terrain (gestion des stages, mots-clés, regroupement d’informations…).

---

## ⚙️ Technologies utilisées

- **PHP** 8.4.7
- **Symfony** 7.2.6
- **MySQL**
- Gestion des dépendances via **Composer** et **npm**

---

## 🧩 Fonctionnalités principales

- ✅ Gestion de cohortes et des groupes de formation  
- ✅ Système d’accès aux cours via des mots-clés thématiques  
- ✅ Gestion des stages et des périodes en entreprise  
- ✅ Regroupement de documents et informations pour les stagiaires  
- ✅ Interface claire et intuitive adaptée aux besoins pédagogiques

---

## 🔧 Installation locale

```bash
# Cloner le dépôt
git clone https://github.com/marceau07/autoformation.git
cd paf

# Installer les dépendances
composer install
npm install

```

```bash
# Configurer les variables essentielles pour le bon fonctionnement
touch .env
```
- APP_ENV=prod
- APP_SECRET=TOKEN `date | md5`
- GITHUB_PERSONAL_ACCESS_TOKEN=VOTRE_CLE_GITHUB (pour les feedback)
- GITHUB_PERSONAL_REPOSITORY=VOTRE_PROJET_GITHUB (pour les feedback)
- AI_URL=IP/DNS vers l'IA
- DATABASE_URL="mysql://UTILISATEUR:MDP@127.0.0.1:3306/BDD?serverVersion=VERSION&charset=utf8mb4"
- MAILER_DSN=smtp://EMAIL:PASSWORD@PROVIDER_DSN:PORT

```bash
# Créer le fichier .env.local si nécessaire
cp .env .env.local

# Configurer la base de données (exemple avec MySQL)
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Lancer le serveur local
php bin/console server:start
```