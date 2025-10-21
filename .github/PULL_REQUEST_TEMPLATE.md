# Description

<!-- Décrivez vos changements en détail -->

## Type de changement

<!-- Cochez les cases appropriées -->

- [ ] 🐛 Bug fix (correction non-breaking)
- [ ] ✨ Nouvelle fonctionnalité (changement non-breaking)
- [ ] 💥 Breaking change (correction ou fonctionnalité qui casse la compatibilité)
- [ ] 📝 Documentation
- [ ] 🎨 Style (formatage, points-virgules manquants, etc.)
- [ ] ♻️ Refactorisation
- [ ] ⚡ Performance
- [ ] ✅ Tests

## Motivation et contexte

<!-- Pourquoi ce changement est-il nécessaire ? Quel problème résout-il ? -->
<!-- Si cela corrige un bug ou une issue, incluez le lien -->

Fixes #(numéro de l'issue)

## Comment cela a-t-il été testé ?

<!-- Décrivez les tests que vous avez effectués -->

- [ ] Tests unitaires
- [ ] Tests d'intégration
- [ ] Tests manuels

**Configuration de test :**
- Version WordPress :
- Version PHP :
- Navigateur :

## Captures d'écran (si approprié)

<!-- Ajoutez des captures d'écran pour illustrer les changements visuels -->

## Checklist

<!-- Cochez toutes les cases applicables -->

### Code

- [ ] Mon code suit les standards WordPress
- [ ] J'ai effectué une auto-revue de mon code
- [ ] J'ai commenté mon code, particulièrement dans les zones difficiles
- [ ] J'ai fait les changements correspondants dans la documentation
- [ ] Mes changements ne génèrent pas de nouveaux warnings
- [ ] J'ai ajouté des tests qui prouvent que ma correction est efficace ou que ma fonctionnalité fonctionne
- [ ] Les tests unitaires nouveaux et existants passent localement avec mes changements

### Sécurité

- [ ] J'ai vérifié tous les nonces
- [ ] J'ai vérifié les permissions utilisateur
- [ ] J'ai sanitizé toutes les entrées
- [ ] J'ai échappé toutes les sorties
- [ ] J'ai utilisé des requêtes préparées pour la base de données

### Documentation

- [ ] J'ai mis à jour le README.md si nécessaire
- [ ] J'ai mis à jour le CHANGELOG.md
- [ ] J'ai ajouté/mis à jour les PHPDoc
- [ ] J'ai mis à jour la documentation API si nécessaire

### Qualité

- [ ] `composer phpcs` passe sans erreur
- [ ] `composer phpstan` passe sans erreur
- [ ] `composer test` passe sans erreur
- [ ] Le code est compatible PHP 7.4+
- [ ] Le code est compatible WordPress 5.0+

## Notes supplémentaires

<!-- Toute information supplémentaire que les reviewers devraient connaître -->
