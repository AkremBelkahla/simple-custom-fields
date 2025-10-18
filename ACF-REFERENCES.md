# Références à Advanced Custom Fields (ACF)

## Vue d'ensemble

**Simple Custom Fields** s'inspire fortement d'**Advanced Custom Fields (ACF)**, le plugin de référence pour la gestion de champs personnalisés dans WordPress. Ce document détaille toutes les références et inspirations tirées d'ACF.

## 🎨 Design et Interface

### Palette de couleurs ACF

Notre plugin utilise la palette de couleurs signature d'ACF :

| Élément | Couleur | Référence ACF |
|---------|---------|---------------|
| **Primary** | `#0783BE` | Bleu signature ACF - Utilisé pour les boutons principaux, liens et éléments interactifs |
| **Primary Hover** | `#066a9c` | État hover du bleu ACF |
| **Primary Light** | `#e7f5fb` | Background léger pour les états hover et focus |
| **Success** | `#46B450` | Vert WordPress/ACF pour les statuts actifs |
| **Danger** | `#DC3232` | Rouge WordPress/ACF pour les suppressions |
| **Warning** | `#FFB900` | Jaune WordPress/ACF pour les avertissements |

### Inspiration visuelle

```
ACF Field Groups Page          →    SCF Groups Page
┌─────────────────────────┐         ┌─────────────────────────┐
│ [+ Add Field Group]     │         │ [+ Ajouter un groupe]   │
├─────────────────────────┤         ├─────────────────────────┤
│ ┌─────────────────────┐ │         │ ┌─────────────────────┐ │
│ │ Product Fields      │ │         │ │ Champs Produit      │ │
│ │ 5 fields • Post     │ │   →     │ │ 5 champs • Article  │ │
│ │ [Edit] [Delete]     │ │         │ │ [Modifier] [×]      │ │
│ └─────────────────────┘ │         │ └─────────────────────┘ │
└─────────────────────────┘         └─────────────────────────┘
```

## 📄 Page de liste des groupes

### Inspirations d'ACF

#### 1. **Layout en Cards**
- **Référence ACF** : Page "Field Groups" d'ACF Pro
- **Notre implémentation** : Grille responsive avec cards modernes
- **Fichier** : `templates/groups-page.php`
- **CSS** : `assets/css/main.css` (section "Liste des groupes")

#### 2. **Métadonnées visuelles**
- **Référence ACF** : Affichage du nombre de champs et du type de contenu
- **Notre implémentation** : Icônes dashicons + texte descriptif
- **Exemple** : "⚙️ 5 champs  📄 Article"

#### 3. **Indicateurs de statut**
- **Référence ACF** : Badges "Active" / "Inactive"
- **Notre implémentation** : Badges avec animation pulse
- **CSS** : `.status-indicator` avec `@keyframes pulse`

#### 4. **Actions rapides**
- **Référence ACF** : Boutons "Edit" et "Duplicate"
- **Notre implémentation** : Boutons "Modifier" et "Supprimer"
- **Hover effect** : Élévation et changement de couleur

## ✏️ Page d'édition de groupe

### Inspirations d'ACF

#### 1. **Layout en colonnes**
- **Référence ACF** : Page "Edit Field Group" avec main content + sidebar
- **Notre implémentation** : Grid CSS avec `1fr 300px`
- **Fichier** : `assets/css/edit-group.css`

```css
/* Inspiré du layout ACF */
#post-body.columns-2 {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--scf-spacing-lg);
}
```

#### 2. **Postbox avec headers stylisés**
- **Référence ACF** : Headers avec gradient subtil
- **Notre implémentation** : `linear-gradient(to bottom, #fafafa, #fff)`
- **Effet** : Séparation visuelle claire entre sections

#### 3. **Drag & Drop visuel**
- **Référence ACF** : Réorganisation des champs par glisser-déposer
- **Notre implémentation** : jQuery UI Sortable avec placeholder animé
- **Feedback** : Ombre portée + bordure bleue sur l'élément déplacé

#### 4. **Focus states bleus**
- **Référence ACF** : Bordure bleue (#0783BE) sur les inputs actifs
- **Notre implémentation** : `box-shadow: 0 0 0 1px var(--scf-primary)`
- **Tous les inputs** : text, textarea, select

## 🪟 Modales

### Inspirations d'ACF

#### 1. **Animations d'entrée**
- **Référence ACF** : Fade in + slide up fluide
- **Notre implémentation** :
```css
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
```

#### 2. **Backdrop blur**
- **Référence ACF Pro** : Effet de flou sur l'arrière-plan
- **Notre implémentation** : `backdrop-filter: blur(2px)`

#### 3. **Scrollbar personnalisée**
- **Référence ACF** : Scrollbar discrète et moderne
- **Notre implémentation** : Styles webkit pour scrollbar

#### 4. **Boutons dans le footer**
- **Référence ACF** : Disposition avec gap entre les boutons
- **Notre implémentation** : Flexbox avec `gap: 12px`

## 🎬 Animations et transitions

### Inspirations d'ACF

#### 1. **Transitions fluides**
- **Référence ACF** : Durée standard de 200-300ms
- **Notre implémentation** : `transition: all 0.2s ease`
- **Éléments** : Tous les boutons, inputs, cards

#### 2. **Hover effects**
- **Référence ACF** : Élévation légère au survol
- **Notre implémentation** : `transform: translateY(-1px)`
- **Ombre** : `box-shadow: var(--scf-shadow)`

#### 3. **Feedback visuel**
- **Référence ACF** : Flash de couleur pour les actions
- **Notre implémentation** :
  - Vert (#e7f5ec) pour succès
  - Rouge (#fef2f2) pour suppression

## 🎨 Système de boutons

### Inspirations d'ACF

#### 1. **Bouton primaire**
- **Référence ACF** : Bleu #0783BE avec hover #066a9c
- **Notre implémentation** : `.scf-btn-primary`
- **États** : Default, Hover, Active, Disabled

#### 2. **Variantes de boutons**
- **Référence ACF** : Primary, Secondary, Danger
- **Notre implémentation** :
  - `.scf-btn-primary` - Bleu ACF
  - `.scf-btn-secondary` - Gris
  - `.scf-btn-danger` - Rouge
  - `.scf-btn-success` - Vert

#### 3. **Icônes intégrées**
- **Référence ACF** : Dashicons avec gap
- **Notre implémentation** : `gap: 6px` entre icône et texte

## 📱 Responsive Design

### Inspirations d'ACF

#### 1. **Breakpoints**
- **Référence ACF** : Adaptation progressive
- **Notre implémentation** :
  - Desktop : >1024px
  - Tablet : 782px - 1024px
  - Mobile : <782px
  - Très petit : <480px

#### 2. **Modale plein écran (mobile)**
- **Référence ACF** : Modale en plein écran sur mobile
- **Notre implémentation** : `width: 100%; height: 100vh`

#### 3. **Grille adaptative**
- **Référence ACF** : Nombre de colonnes variable
- **Notre implémentation** :
  - Desktop : 3 colonnes
  - Tablet : 2 colonnes
  - Mobile : 1 colonne

## 🎯 Champs personnalisés

### Inspirations d'ACF

#### 1. **Labels en gras**
- **Référence ACF** : Labels avec `font-weight: 600`
- **Notre implémentation** : `.scf-field-label`

#### 2. **Hover sur les wrappers**
- **Référence ACF** : Background gris au survol
- **Notre implémentation** : `background: var(--scf-bg-secondary)`

#### 3. **Messages d'erreur**
- **Référence ACF** : Bordure gauche colorée
- **Notre implémentation** : `border-left: 3px solid var(--scf-danger)`

## 📊 Comparaison détaillée

### ACF vs SCF

| Fonctionnalité | ACF | Simple Custom Fields |
|----------------|-----|----------------------|
| **Couleur primaire** | #0783BE | ✅ #0783BE (identique) |
| **Layout cards** | ✅ Oui | ✅ Oui (inspiré) |
| **Drag & drop** | ✅ Oui | ✅ Oui (jQuery UI) |
| **Animations** | ✅ Fluides | ✅ Fluides (inspirées) |
| **Responsive** | ✅ Complet | ✅ Complet (inspiré) |
| **Focus states** | ✅ Bleu | ✅ Bleu (identique) |
| **Modales** | ✅ Blur | ✅ Blur (inspiré) |
| **Indicateurs** | ✅ Badges | ✅ Badges animés |

## 🔗 Liens et ressources

### Documentation ACF
- **Site officiel** : https://www.advancedcustomfields.com/
- **Documentation** : https://www.advancedcustomfields.com/resources/
- **GitHub** : https://github.com/AdvancedCustomFields

### Inspiration design
- Page "Field Groups" d'ACF Pro
- Page "Edit Field Group" d'ACF Pro
- Modales d'édition de champs ACF
- Système de couleurs ACF

## 💡 Différences et améliorations

### Ce que nous avons gardé d'ACF
✅ Palette de couleurs signature  
✅ Layout en cards moderne  
✅ Animations fluides  
✅ Focus states bleus  
✅ Responsive design  

### Ce que nous avons amélioré
🚀 **Animation pulse** sur les indicateurs de statut  
🚀 **Backdrop blur** sur les modales  
🚀 **Scrollbar personnalisée** dans les modales  
🚀 **Feedback visuel** avec flash de couleur  
🚀 **Grid CSS moderne** au lieu de flexbox  

### Ce que nous avons simplifié
📦 Interface plus épurée  
📦 Moins d'options (focus sur l'essentiel)  
📦 Code plus léger et maintenable  

## 🎓 Crédits

**Simple Custom Fields** est un plugin indépendant créé par **Akrem Belkahla** (Infinity Web).

Le design s'inspire d'**Advanced Custom Fields (ACF)** développé par **Delicious Brains** (anciennement Elliot Condon), avec respect et admiration pour leur travail exceptionnel.

**ACF** est une marque déposée de Delicious Brains. Simple Custom Fields n'est pas affilié, approuvé ou sponsorisé par Delicious Brains.

## 📝 Notes légales

Ce plugin utilise une palette de couleurs et des principes de design inspirés d'ACF, mais :
- ❌ N'utilise aucun code source d'ACF
- ❌ N'est pas une copie ou un fork d'ACF
- ❌ Ne prétend pas remplacer ACF
- ✅ Est un plugin indépendant avec son propre code
- ✅ Rend hommage au design exceptionnel d'ACF
- ✅ Vise à offrir une expérience familière aux utilisateurs d'ACF

---

**Auteur** : Akrem Belkahla  
**Agence** : Infinity Web  
**Email** : akrem.belkahla@infinityweb.tn  
**Version** : 1.4.0  
**Date** : Octobre 2025
