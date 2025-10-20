jQuery(document).ready(function($) {
    'use strict';
    
    // Toggle Options de l'écran
    $('#scf-screen-options-toggle').on('click', function() {
        var $panel = $('#scf-screen-options');
        var $button = $(this);
        
        $panel.slideToggle(200);
        $button.toggleClass('active');
    });
    
    // Toggle colonnes du tableau
    $('.scf-column-toggle').on('change', function() {
        var column = $(this).data('column');
        var isChecked = $(this).is(':checked');
        
        $('.column-' + column).toggle(isChecked);
        
        // Sauvegarder la préférence dans localStorage
        localStorage.setItem('scf_column_' + column, isChecked ? '1' : '0');
    });
    
    // Restaurer les préférences de colonnes
    $('.scf-column-toggle').each(function() {
        var column = $(this).data('column');
        var saved = localStorage.getItem('scf_column_' + column);
        
        if (saved !== null) {
            var isVisible = saved === '1';
            $(this).prop('checked', isVisible);
            $('.column-' + column).toggle(isVisible);
        }
    });
    
    // Filtres (Tous, Actifs, Inactifs)
    $('.scf-filter-link').on('click', function() {
        var filter = $(this).data('filter');
        
        // Mettre à jour l'état actif
        $('.scf-filter-link').removeClass('scf-filter-active');
        $(this).addClass('scf-filter-active');
        
        // Filtrer les lignes du tableau
        if (filter === 'all') {
            $('.scf-groups-table tbody tr').show();
        } else if (filter === 'active') {
            $('.scf-groups-table tbody tr').each(function() {
                var status = $(this).find('.column-status .scf-badge').text().trim();
                $(this).toggle(status.includes('Activé'));
            });
        } else if (filter === 'inactive') {
            $('.scf-groups-table tbody tr').each(function() {
                var status = $(this).find('.column-status .scf-badge').text().trim();
                $(this).toggle(status.includes('Désactivé'));
            });
        }
        
        // Mettre à jour le compteur
        updateVisibleCount();
    });
    
    // Recherche
    var searchTimeout;
    $('#scf-search-groups').on('input', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val().toLowerCase();
        
        searchTimeout = setTimeout(function() {
            if (searchTerm === '') {
                $('.scf-groups-table tbody tr').show();
            } else {
                $('.scf-groups-table tbody tr').each(function() {
                    var title = $(this).find('.column-title').text().toLowerCase();
                    var description = $(this).find('.description').text().toLowerCase();
                    var matches = title.includes(searchTerm) || description.includes(searchTerm);
                    $(this).toggle(matches);
                });
            }
            updateVisibleCount();
        }, 300);
    });
    
    // Bouton de recherche
    $('.scf-search-btn').on('click', function() {
        $('#scf-search-groups').focus();
    });
    
    // Fonction pour mettre à jour le compteur de résultats visibles
    function updateVisibleCount() {
        var visibleCount = $('.scf-groups-table tbody tr:visible').length;
        var totalCount = $('.scf-groups-table tbody tr').length;
        
        // Vous pouvez ajouter un élément pour afficher le compteur si nécessaire
        console.log('Affichage de ' + visibleCount + ' sur ' + totalCount + ' groupes');
    }
    
    // Fermer le panneau Options si on clique en dehors
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#scf-screen-options, #scf-screen-options-toggle').length) {
            if ($('#scf-screen-options').is(':visible')) {
                $('#scf-screen-options').slideUp(200);
                $('#scf-screen-options-toggle').removeClass('active');
            }
        }
    });
});
