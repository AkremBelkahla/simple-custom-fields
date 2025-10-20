<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap scf-documentation-page">
    <div class="scf-doc-header">
        <h1>
            <span class="dashicons dashicons-book"></span>
            Documentation Simple Custom Fields
        </h1>
        <p class="scf-doc-subtitle">Guide complet pour utiliser les champs personnalisés</p>
    </div>

    <div class="scf-doc-container">
        <!-- Sidebar Navigation -->
        <aside class="scf-doc-sidebar">
            <nav class="scf-doc-nav">
                <h3>Navigation</h3>
                <ul>
                    <li><a href="#introduction">Introduction</a></li>
                    <li><a href="#getting-started">Démarrage rapide</a></li>
                    <li><a href="#field-types">Types de champs</a></li>
                    <li><a href="#display-values">Afficher les valeurs</a></li>
                    <li><a href="#examples">Exemples pratiques</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="scf-doc-content">
            
            <!-- Introduction -->
            <section id="introduction" class="scf-doc-section">
                <h2>Introduction</h2>
                <p>Simple Custom Fields vous permet d'ajouter des champs personnalisés à vos contenus WordPress. Ces champs vous donnent la flexibilité de stocker des informations supplémentaires au-delà du titre et du contenu standard.</p>
            </section>

            <!-- Démarrage rapide -->
            <section id="getting-started" class="scf-doc-section">
                <h2>Démarrage rapide</h2>
                
                <div class="scf-doc-steps">
                    <div class="scf-doc-step">
                        <div class="scf-step-number">1</div>
                        <div class="scf-step-content">
                            <h3>Créer un groupe de champs</h3>
                            <p>Allez dans <strong>Groupes de champs</strong> → <strong>Ajouter un groupe</strong></p>
                        </div>
                    </div>
                    
                    <div class="scf-doc-step">
                        <div class="scf-step-number">2</div>
                        <div class="scf-step-content">
                            <h3>Ajouter des champs</h3>
                            <p>Cliquez sur <strong>Ajouter un champ</strong> et configurez le type souhaité</p>
                        </div>
                    </div>
                    
                    <div class="scf-doc-step">
                        <div class="scf-step-number">3</div>
                        <div class="scf-step-content">
                            <h3>Définir l'emplacement</h3>
                            <p>Choisissez où afficher vos champs (Articles, Pages, etc.)</p>
                        </div>
                    </div>
                    
                    <div class="scf-doc-step">
                        <div class="scf-step-number">4</div>
                        <div class="scf-step-content">
                            <h3>Publier</h3>
                            <p>Cliquez sur <strong>Publier</strong> pour activer votre groupe</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Types de champs -->
            <section id="field-types" class="scf-doc-section">
                <h2>Types de champs disponibles</h2>
                
                <?php 
                $field_types = array(
                    array(
                        'icon' => '📝',
                        'name' => 'Texte',
                        'usage' => 'Pour du texte court sur une seule ligne',
                        'example' => 'Sous-titre, nom, référence',
                        'code' => "get_post_meta(get_the_ID(), 'sous_titre', true)"
                    ),
                    array(
                        'icon' => '📄',
                        'name' => 'Zone de texte',
                        'usage' => 'Pour du texte long sur plusieurs lignes',
                        'example' => 'Description, résumé, notes',
                        'code' => "get_post_meta(get_the_ID(), 'description', true)"
                    ),
                    array(
                        'icon' => '🔢',
                        'name' => 'Nombre',
                        'usage' => 'Pour des valeurs numériques',
                        'example' => 'Prix, quantité, score',
                        'code' => "get_post_meta(get_the_ID(), 'prix', true)"
                    ),
                    array(
                        'icon' => '📧',
                        'name' => 'Email',
                        'usage' => 'Pour des adresses email avec validation',
                        'example' => 'Email de contact',
                        'code' => "get_post_meta(get_the_ID(), 'email', true)"
                    ),
                    array(
                        'icon' => '🔗',
                        'name' => 'URL',
                        'usage' => 'Pour des liens web avec validation',
                        'example' => 'Site web, lien externe',
                        'code' => "get_post_meta(get_the_ID(), 'site_web', true)"
                    ),
                    array(
                        'icon' => '📅',
                        'name' => 'Date',
                        'usage' => 'Pour sélectionner une date',
                        'example' => 'Date d\'événement, deadline',
                        'code' => "get_post_meta(get_the_ID(), 'date_event', true)"
                    ),
                    array(
                        'icon' => '⏰',
                        'name' => 'Heure',
                        'usage' => 'Pour sélectionner une heure',
                        'example' => 'Heure de début, horaire',
                        'code' => "get_post_meta(get_the_ID(), 'heure', true)"
                    ),
                    array(
                        'icon' => '🎨',
                        'name' => 'Couleur',
                        'usage' => 'Pour choisir une couleur',
                        'example' => 'Couleur du thème, accent',
                        'code' => "get_post_meta(get_the_ID(), 'couleur', true)"
                    ),
                    array(
                        'icon' => '📋',
                        'name' => 'Liste déroulante',
                        'usage' => 'Pour choisir une option parmi plusieurs',
                        'example' => 'Catégorie, niveau, statut',
                        'code' => "get_post_meta(get_the_ID(), 'niveau', true)"
                    ),
                    array(
                        'icon' => '🔘',
                        'name' => 'Boutons radio',
                        'usage' => 'Pour choisir une seule option visuellement',
                        'example' => 'Format, type',
                        'code' => "get_post_meta(get_the_ID(), 'format', true)"
                    ),
                    array(
                        'icon' => '☑️',
                        'name' => 'Cases à cocher',
                        'usage' => 'Pour sélectionner plusieurs options',
                        'example' => 'Tags, caractéristiques',
                        'code' => "get_post_meta(get_the_ID(), 'tags', true)"
                    ),
                    array(
                        'icon' => '✏️',
                        'name' => 'Éditeur WYSIWYG',
                        'usage' => 'Pour du contenu riche avec mise en forme',
                        'example' => 'Contenu additionnel',
                        'code' => "get_post_meta(get_the_ID(), 'contenu', true)"
                    ),
                    array(
                        'icon' => '🖼️',
                        'name' => 'Image',
                        'usage' => 'Pour uploader une image',
                        'example' => 'Bannière, logo, photo',
                        'code' => "get_post_meta(get_the_ID(), 'image', true)"
                    ),
                    array(
                        'icon' => '📎',
                        'name' => 'Fichier',
                        'usage' => 'Pour uploader tout type de fichier',
                        'example' => 'PDF, ZIP, document',
                        'code' => "get_post_meta(get_the_ID(), 'fichier', true)"
                    )
                );
                
                foreach ($field_types as $type): ?>
                <div class="scf-field-type-card">
                    <div class="scf-field-type-header">
                        <span class="scf-field-icon"><?php echo $type['icon']; ?></span>
                        <h3><?php echo $type['name']; ?></h3>
                    </div>
                    <p class="scf-field-usage"><strong>Usage :</strong> <?php echo $type['usage']; ?></p>
                    <p class="scf-field-example"><strong>Exemple :</strong> <?php echo $type['example']; ?></p>
                    <div class="scf-field-code">
                        <code>&lt;?php echo <?php echo $type['code']; ?>; ?&gt;</code>
                    </div>
                </div>
                <?php endforeach; ?>
            </section>

            <!-- Afficher les valeurs -->
            <section id="display-values" class="scf-doc-section">
                <h2>Afficher les valeurs dans votre thème</h2>
                
                <div class="scf-doc-code-block">
                    <h3>Exemple complet</h3>
                    <pre><code>&lt;?php
// Récupérer les valeurs
$sous_titre = get_post_meta(get_the_ID(), 'sous_titre', true);
$prix = get_post_meta(get_the_ID(), 'prix', true);
$image = get_post_meta(get_the_ID(), 'image_banniere', true);

// Afficher
if ($sous_titre) {
    echo '&lt;h2&gt;' . esc_html($sous_titre) . '&lt;/h2&gt;';
}

if ($prix) {
    echo '&lt;div class="prix"&gt;' . number_format($prix, 2) . ' €&lt;/div&gt;';
}

if ($image) {
    echo wp_get_attachment_image($image, 'large');
}
?&gt;</code></pre>
                </div>
            </section>

            <!-- Exemples pratiques -->
            <section id="examples" class="scf-doc-section">
                <h2>Exemples pratiques</h2>
                
                <div class="scf-example-grid">
                    <div class="scf-example-card">
                        <h3>📰 Article de blog</h3>
                        <ul>
                            <li>Sous-titre (Texte)</li>
                            <li>Temps de lecture (Nombre)</li>
                            <li>Auteur invité (Texte)</li>
                        </ul>
                    </div>
                    
                    <div class="scf-example-card">
                        <h3>🏠 Fiche immobilière</h3>
                        <ul>
                            <li>Prix (Nombre)</li>
                            <li>Surface (Nombre)</li>
                            <li>Équipements (Cases à cocher)</li>
                        </ul>
                    </div>
                    
                    <div class="scf-example-card">
                        <h3>🎫 Événement</h3>
                        <ul>
                            <li>Date (Date)</li>
                            <li>Heure (Heure)</li>
                            <li>Lieu (Texte)</li>
                            <li>Prix (Nombre)</li>
                        </ul>
                    </div>
                    
                    <div class="scf-example-card">
                        <h3>👤 Équipe</h3>
                        <ul>
                            <li>Poste (Texte)</li>
                            <li>Email (Email)</li>
                            <li>LinkedIn (URL)</li>
                            <li>Photo (Image)</li>
                        </ul>
                    </div>
                </div>
            </section>

        </main>
    </div>
</div>
