# Site Vitrine Psychologue - Dr. Yoff

> Site web one-page élégant et professionnel pour un psychologue/thérapeute, conçu avec HTML/CSS/JavaScript vanilla et optimisé pour le référencement.

## 🌱 Aperçu

Site vitrine moderne avec une identité visuelle apaisante en **vert sage**, conçu pour inspirer confiance et sérénité. L'interface responsive et accessible respecte les meilleures pratiques web et les standards d'accessibilité.

### ✨ Caractéristiques principales

- **Design apaisant** : Palette de verts doux et couleurs neutres
- **SEO optimisé** : Structure sémantique, meta tags, Schema.org
- **Responsive mobile-first** : Adaptation parfaite sur tous écrans
- **Animations subtiles** : Effets parallax et transitions fluides
- **Accessibilité** : Respect des standards WCAG AA
- **Performance** : Code optimisé, sans dépendances externes
- **Formulaire de contact** sécurisé avec protection anti-spam

## 📁 Structure du projet

```
sityof/
├── index.html              # Page principale
├── css/
│   └── styles.css          # Styles CSS complets
├── js/
│   └── script.js           # JavaScript fonctionnel
├── php/
│   └── contact.php         # Script de contact sécurisé
├── images/
│   └── .gitkeep            # Guide pour les images
└── README.md               # Documentation
```

## 🚀 Installation

### Prérequis

- Serveur web avec support PHP 7.4+
- Configuration email (SMTP ou fonction mail() PHP)
- Certificat SSL recommandé (HTTPS)

### Étapes d'installation

1. **Cloner le repository**
   ```bash
   git clone https://github.com/LeoSprDev/sityof.git
   cd sityof
   ```

2. **Configurer le serveur web**
   - Pointer le document root vers le dossier du projet
   - S'assurer que PHP est activé
   - Vérifier les permissions d'écriture pour les logs

3. **Configurer l'envoi d'emails**
   - Éditer `php/contact.php`
   - Modifier les variables de configuration :
   ```php
   $config = [
       'to_email' => 'votre-email@domaine.com',
       'to_name' => 'Votre Nom',
       'from_email' => 'noreply@votre-domaine.com',
       // ... autres paramètres
   ];
   ```

4. **Ajouter les images**
   - Placer les photos dans le dossier `images/`
   - Respecter les noms de fichiers indiqués dans `images/.gitkeep`
   - Optimiser les images (format WebP recommandé)

5. **Personnaliser le contenu**
   - Modifier les informations dans `index.html`
   - Adapter les couleurs dans `css/styles.css` si nécessaire
   - Vérifier les liens de réseaux sociaux

## ⚙️ Configuration

### Configuration email

#### Option 1 : Fonction mail() PHP (recommandée pour la plupart des hébergeurs)
```php
$config['use_smtp'] = false;
```

#### Option 2 : SMTP personnalisé
```php
$config = [
    'use_smtp' => true,
    'smtp_host' => 'smtp.votre-fournisseur.com',
    'smtp_port' => 587,
    'smtp_username' => 'votre-username',
    'smtp_password' => 'votre-mot-de-passe'
];
```

### Variables d'environnement (recommandé)

Pour la sécurité, utilisez des variables d'environnement :

```php
$config = [
    'to_email' => $_ENV['CONTACT_EMAIL'] ?? 'default@domain.com',
    'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? 'default-password'
];
```

### Configuration des domaines autorisés (CORS)

Modifier dans `php/contact.php` :
```php
$allowed_origins = [
    'https://votre-domaine.com',
    'https://www.votre-domaine.com'
];
```

## 🎨 Personnalisation

### Couleurs de la palette

Les couleurs sont définies dans les variables CSS :

```css
:root {
    --primary-color: #68d391;      /* Vert sage principal */
    --primary-dark: #48bb78;       /* Vert sage foncé */
    --primary-light: #9ae6b4;      /* Vert sage clair */
    --secondary-color: #2f855a;    /* Vert forêt */
    --accent-color: #81c784;       /* Vert menthe pâle */
}
```

### Contenu personnalisable

1. **Informations personnelles** (`index.html`)
   - Nom et titre professionnel
   - Description et spécialités
   - Coordonnées de contact
   - Liens réseaux sociaux

2. **Parcours professionnel**
   - Timeline des expériences
   - Formations et certifications
   - Compétences spécifiques

3. **Centres d'intérêts**
   - Icônes et descriptions
   - Personnalisation des emojis

4. **Approche thérapeutique**
   - Méthodologie personnelle
   - Valeurs et philosophie

### Images recommandées

| Fichier | Dimensions | Description |
|---------|------------|-------------|
| `hero-1.jpg` | 800x600px | Cabinet de consultation |
| `hero-2.jpg` | 800x600px | Espace thérapeutique |
| `hero-3.jpg` | 800x600px | Salle d'attente |
| `og-image.jpg` | 1200x630px | Image sociale (Open Graph) |
| `favicon.ico` | 32x32px | Icône du site |

**Conseils photo :**
- Ambiance chaleureuse et professionnelle
- Éclairage naturel privilégié
- Couleurs harmonisées avec la palette verte
- Format WebP pour optimiser les performances

## 🔒 Sécurité

### Protections intégrées

- **Validation des données** : Filtrage et nettoyage complets
- **Protection anti-spam** : Détection de mots-clés et URLs suspectes
- **Rate limiting** : Limitation à 5 messages/heure par IP
- **Headers de sécurité** : XSS, CSRF, Clickjacking
- **Logging** : Traçabilité des tentatives d'envoi

### Recommandations supplémentaires

1. **HTTPS obligatoire** : Installer un certificat SSL
2. **Mise à jour PHP** : Utiliser PHP 8.0+ pour la sécurité
3. **Sauvegarde régulière** : Backup automatique du site
4. **Monitoring** : Surveiller les logs d'erreur

## 📊 Performance

### Optimisations incluses

- **CSS optimisé** : Variables CSS, organisation modulaire
- **JavaScript efficient** : Code natif sans framework
- **Images responsives** : Adaptation selon les écrans
- **Lazy loading** : Chargement différé des images
- **Minification recommandée** : Compression pour la production

### Score Lighthouse visé

- ✅ **Performance** : > 90/100
- ✅ **Accessibility** : > 95/100  
- ✅ **Best Practices** : > 95/100
- ✅ **SEO** : > 95/100

## ♿ Accessibilité

### Standards respectés

- **WCAG 2.1 AA** : Contrastes et navigation clavier
- **ARIA labels** : Étiquetage des éléments interactifs
- **Skip links** : Navigation rapide au contenu
- **prefers-reduced-motion** : Respect des préférences utilisateur
- **Focus management** : Indicateurs visuels clairs

## 🔍 SEO

### Opétimisations intégrées

- **Structure HTML sémantique** : Headers, nav, main, sections
- **Meta tags complets** : Title, description, Open Graph
- **Schema.org** : Balisage structuré LocalBusiness + Psychologist
- **URLs propres** : Ancres descriptives (#parcours, #contact)
- **Images optimisées** : Attributs alt descriptifs
- **Temps de chargement** : < 2 secondes visé

### Mots-clés ciblés

- psychologue, thérapeute, thérapie
- accompagnement psychologique
- consultation, bien-être mental
- [Ville/Région] + spécialités

## 🚀 Déploiement

### Checklist de déploiement

#### 📝 Contenu
- [ ] Personnaliser toutes les informations (nom, contact, etc.)
- [ ] Ajouter les photos professionnelles dans `/images/`
- [ ] Vérifier les liens de réseaux sociaux
- [ ] Tester le formulaire de contact
- [ ] Relire le contenu pour les fautes

#### ⚙️ Configuration technique
- [ ] Configurer l'envoi d'emails (`php/contact.php`)
- [ ] Définir les domaines autorisés (CORS)
- [ ] Vérifier les permissions de fichiers
- [ ] Tester sur mobile et desktop
- [ ] Vérifier la compatibilité navigateurs

#### 🔒 Sécurité
- [ ] Installer le certificat SSL (HTTPS)
- [ ] Configurer les headers de sécurité
- [ ] Tester la protection anti-spam
- [ ] Vérifier les logs d'erreur
- [ ] Sauvegarder la configuration

#### 📊 Performance
- [ ] Optimiser/compresser les images
- [ ] Minifier CSS/JS pour la production
- [ ] Tester les temps de chargement
- [ ] Vérifier le score Lighthouse
- [ ] Configurer la mise en cache serveur

#### 🔍 Référencement
- [ ] Soumettre le sitemap aux moteurs de recherche
- [ ] Configurer Google Analytics (optionnel)
- [ ] Vérifier l'indexation Google
- [ ] Tester les rich snippets
- [ ] Optimiser pour la recherche locale

### Plateformes de déploiement

**Hébergement web classique :**
- Upload via FTP/SFTP
- Configuration DNS
- Certificat SSL

**GitHub Pages (pour tester) :**
```bash
# Activer GitHub Pages dans les paramètres du repository
# URL : https://leosprdev.github.io/sityof/
```

**Services cloud :**
- Netlify, Vercel (JAMstack)
- OVH, Gandi, Ionos (hébergement traditionnel)

## 🌐 Nom de domaine

### Suggestions professionnelles

- `prenom-nom-psychologue.fr`
- `cabinet-psychologie-[ville].fr`
- `therapie-[specialite].com`
- `accompagnement-psychologique.fr`

### Configuration DNS

```
Type  | Nom  | Valeur
------|------|--------
A     | @    | [IP serveur]
A     | www  | [IP serveur]
AAAA  | @    | [IPv6 serveur] (optionnel)
```

## 🛠️ Maintenance

### Tâches régulières

- **Mensuelle** : Vérifier les logs de contact
- **Trimestrielle** : Mettre à jour PHP/serveur
- **Semestrielle** : Audit SEO et performance
- **Annuelle** : Renouvellement certificat SSL

### Monitoring recommandé

- **Uptime** : Surveillance de la disponibilité
- **Performance** : Temps de réponse
- **Sécurité** : Tentatives d'intrusion
- **SEO** : Positionnement sur les mots-clés

## 📞 Support

### Ressources utiles

- **Documentation PHP** : [php.net](https://php.net)
- **Validation HTML** : [W3C Validator](https://validator.w3.org)
- **Test performance** : [Google PageSpeed Insights](https://pagespeed.web.dev)
- **Test accessibilité** : [WAVE Web Accessibility Evaluator](https://wave.webaim.org)

### Problèmes courants

**Le formulaire ne fonctionne pas :**
1. Vérifier la configuration email dans `contact.php`
2. Tester la fonction `mail()` de PHP
3. Vérifier les logs d'erreur serveur
4. Contrôler les permissions de fichiers

**Images qui ne s'affichent pas :**
1. Vérifier les chemins dans `index.html`
2. S'assurer que les fichiers existent dans `/images/`
3. Contrôler les permissions de lecture

**Problèmes de style :**
1. Vérifier le chemin vers `css/styles.css`
2. Vider le cache navigateur
3. Vérifier la syntaxe CSS

---

## 🌟 Crédits

Site développé avec ❤️ par Léo Spr pour un accompagnement psychologique professionnel et bienveillant.

**Technologies utilisées :**
- HTML5 sémantique
- CSS3 avec variables et Grid/Flexbox
- JavaScript ES6+ vanilla
- PHP 7.4+ pour le backend

**Licence :** Libre d'utilisation et de modification pour usage professionnel.