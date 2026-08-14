<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Catégories de <span class="accent">véhicules</span></h1>
        <p>Gérer les catégories disponibles pour le catalogue</p>
    </div>

    <a href="<?= APP_URL ?>/index.php?page=admin-dashboard" class="btn btn-ghost btn-sm" style="margin-bottom:28px;">
        ← Dashboard
    </a>

    <div class="admin-two-col">

        <!-- ============================================================
             FORMULAIRE AJOUT
        ============================================================ -->
        <div class="admin-form-card">
            <h3 class="admin-form-section-title">➕ Ajouter une catégorie</h3>

            <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-categorie-store" novalidate>
                <?php $csrf = Session::generateCsrf(); ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group" style="margin-bottom:16px;">
                    <label>Nom de la catégorie <span class="required">*</span></label>
                    <input type="text" name="nom_categorie"
                           placeholder="Ex : SUV, Berline, Utilitaire..."
                           required>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label>Description <small style="color:var(--text-muted);text-transform:none;font-weight:400;">(optionnel)</small></label>
                    <textarea name="description" rows="3"
                              placeholder="Description de la catégorie..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">
                    ✅ Enregistrer la catégorie
                </button>
            </form>
        </div>

        <!-- ============================================================
             LISTE DES CATÉGORIES
        ============================================================ -->
        <div>
            <?php if (empty($categories)): ?>
                <div class="empty-state">
                    <span class="empty-icon">📦</span>
                    <p>Aucune catégorie enregistrée.</p>
                </div>
            <?php else: ?>
                <p class="result-count"><?= count($categories) ?> catégorie(s)</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Véhicules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td style="color:var(--text-muted);font-size:12px;"><?= (int)$cat['ID_CATEGORIE'] ?></td>
                            <td><strong><?= htmlspecialchars($cat['NOM_CATEGORIE']) ?></strong></td>
                            <td style="font-size:13px;color:var(--text-muted);">
                                <?= htmlspecialchars($cat['DESCRIPTION'] ?? '—') ?>
                            </td>
                            <td>
                                <span class="badge badge-blue"><?= (int)$cat['nb_vehicules'] ?> véhicule(s)</span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;">

                                    <!-- Bouton modifier → ouvre le modal -->
                                    <button class="btn btn-ghost btn-sm"
                                            onclick="openEditCategorie(<?= (int)$cat['ID_CATEGORIE'] ?>, '<?= htmlspecialchars(addslashes($cat['NOM_CATEGORIE'])) ?>', '<?= htmlspecialchars(addslashes($cat['DESCRIPTION'] ?? '')) ?>')"
                                            title="Modifier">✏️</button>

                                    <!-- Supprimer -->
                                    <?php if ((int)$cat['nb_vehicules'] === 0): ?>
                                    <a href="<?= APP_URL ?>/index.php?page=admin-categorie-supprimer&id=<?= (int)$cat['ID_CATEGORIE'] ?>"
                                       class="btn btn-danger btn-sm"
                                       title="Supprimer"
                                       onclick="return confirm('Supprimer cette catégorie ?')">🗑️</a>
                                    <?php else: ?>
                                    <span class="btn btn-ghost btn-sm" style="opacity:.4;cursor:not-allowed;" title="Impossible : des véhicules utilisent cette catégorie">🗑️</span>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================
         MODAL ÉDITION
    ============================================================ -->
    <div id="modal-edit-categorie" class="admin-modal" style="display:none;">
        <div class="admin-modal-box">
            <h3 style="color:var(--white);margin-bottom:20px;">✏️ Modifier la catégorie</h3>

            <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-categorie-update" novalidate>
                <?php $csrf2 = Session::generateCsrf(); ?>
                <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf2) ?>">
                <input type="hidden" name="categorie_id"  id="edit-cat-id">

                <div class="form-group" style="margin-bottom:16px;">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="nom_categorie" id="edit-cat-nom" required>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label>Description</label>
                    <textarea name="description" id="edit-cat-desc" rows="3"></textarea>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">💾 Mettre à jour</button>
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-categorie')">Annuler</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
function openEditCategorie(id, nom, desc) {
    document.getElementById('edit-cat-id').value   = id;
    document.getElementById('edit-cat-nom').value  = nom;
    document.getElementById('edit-cat-desc').value = desc;
    document.getElementById('modal-edit-categorie').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
// Fermer en cliquant hors du modal
document.getElementById('modal-edit-categorie').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-edit-categorie');
});
</script>
