<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>Paramètres</h1>
    <hr class="wp-header-end">

    <div class="scf-container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Paramètres généraux</h5>
                        <form method="post" action="options.php">
                            <?php settings_fields('scf_settings'); ?>
                            <div class="alert alert-info">
                                Les paramètres seront disponibles dans une prochaine mise à jour.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">À propos</h5>
                        <p>Simple Custom Fields est un plugin qui permet de créer facilement des champs personnalisés pour vos contenus WordPress.</p>
                        <p>Version: 1.0.0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
