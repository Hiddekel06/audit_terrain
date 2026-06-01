# Signature Design & Stack Technique

Ce document résume le style de design, les composants et la stack technique utilisés dans ce projet. Il sert de guide de référence pour maintenir la cohérence visuelle et structurelle dans d'autres projets.

## 🚀 Stack Technique
- **Backend :** Laravel 13 (PHP 8.3+)
- **Frontend :** Blade Templates + Alpine.js (pour l'interactivité légère)
- **Styling :** Tailwind CSS 3.4/4.0
- **Build Tool :** Vite
- **Authentification :** Laravel Breeze (Base personnalisée)

## 🎨 Identité Visuelle

### Palette de Couleurs
- **Couleur Primaire :** Émeraude (Emerald). Utilisée pour la structure principale (Sidebar, bordures de l'en-tête, accents).
- **Accents :** Indigo. Utilisé pour les états de focus des formulaires et certains éléments d'action.
- **Neutres :** Gris (Gray) pour les fonds de page (`gray-100`/`gray-900`) et le texte.

### Typographie & Éléments de Base
- **Police :** `Figtree` (Sans-serif moderne).
- **Arrondis (Bordures) :**
    - `rounded-md` pour les boutons et inputs classiques.
    - `rounded-xl` pour les items de navigation, barres de recherche et menus déroulants.
    - `rounded-full` pour les avatars et boutons d'action circulaires.
- **Ombres :** Utilisation de `shadow-sm` pour les cartes/inputs et `shadow-2xl` pour la sidebar fixe.

## 🏗️ Composants Clés

### 1. Sidebar (Le "Cœur" du Dashboard)
- **Style :** Dégradé vertical `from-emerald-800 to-emerald-900`.
- **Comportement :**
    - Rétractable (Collapsible) : Largeur `w-64` (étendu) vers `w-16` (réduit).
    - Géré par Alpine.js (`sidebarOpen`).
    - Animation fluide avec `transition-all duration-300`.
- **Navigation :**
    - Items avec `rounded-xl`.
    - Effets au survol : `hover:bg-emerald-700/40`.
    - État actif : `bg-emerald-600/40 text-white shadow-sm`.

### 2. Header (En-tête Fixe)
- **Position :** `fixed top-0`, s'ajuste dynamiquement à la largeur de la sidebar.
- **Design :** Fond blanc (`dark:bg-gray-900`) avec une bordure inférieure fine `border-emerald-100`.
- **Fonctionnalités :** Barre de recherche centrale arrondie (`rounded-xl`), notifications avec point rouge, menu utilisateur en dropdown.

### 3. Boutons
- **Primary Button :** Style "High Contrast". Fond `gray-800` (mode clair) / `gray-200` (mode sombre). Texte en majuscules (`uppercase`), espacement large (`tracking-widest`), police `font-semibold text-xs`.
- **Action Buttons :** Utilisation de `emerald-600` pour les actions positives (Ajouter, Enregistrer) et `slate-700` ou `blue-600` pour les actions secondaires/utilitaires (Scanner, Importer). Toujours avec `rounded-xl`.

### 4. Tableaux & Listes
- **Style :** Épuré, `text-sm`, bordures fines `border-gray-100` (clair) / `border-gray-800` (sombre).
- **Interactivité :** Effet au survol de ligne avec une légère teinte émeraude (`hover:bg-emerald-50/30`).
- **En-têtes :** Texte en gris (`gray-500`) pour contraster avec les données.

### 5. Modales & Overlays
- **Structure :** Centrées via Flexbox, fond semi-transparent `bg-black/40` ou `bg-black/60`.
- **Design :** Coins très arrondis (`rounded-xl`), padding généreux (`p-6`).
- **Feedback :** Utilisation intensive d'animations d'entrée/sortie (via Alpine.js ou transitions CSS).

### 6. Badges & Statuts
- **Format :** Petits labels arrondis (`rounded`), police grasse (`font-semibold`), texte très petit (`text-xs`).
- **Code Couleur :**
    - Disponible : Vert (`emerald`).
    - Sorti : Jaune (`yellow`).
    - Maintenance : Bleu (`blue`).
    - Perdu/Urgent : Rouge (`red`).

### 7. Formulaires & Inputs
- **Inputs :** Fond `gray-900` en mode sombre, bordures `gray-300/700`.
- **Focus :** Anneau indigo (`focus:ring-indigo-500`) pour une visibilité claire.
- **Photos :** Zones de preview avec bordures en pointillés (`border-dashed`) et coins arrondis.

## 🛠️ Logique & Patterns
- **Gestion des Rôles :** Logique de permission directement dans Blade via des variables calculées (ex: `$isSuperAdmin`, `$isAdmin`).
- **Mode Sombre (Dark Mode) :** Support natif via les classes `dark:` de Tailwind.
- **Interactivité :** Priorité à Alpine.js pour les toggles, modals, et menus sans rechargement de page.
- **Responsive :** Utilisation intensive des variantes `lg:`, `md:`, `sm:` pour adapter la sidebar et le header (le header devient plein écran sur mobile, la sidebar devient un overlay).

## 💡 Philosophie de Design
Un style **"Clean Enterprise"** : Professionnel, utilisant des gradients profonds pour la structure (Sidebar) et des espaces clairs/aérés pour le contenu. L'accent est mis sur la fluidité des transitions et la clarté des actions.
