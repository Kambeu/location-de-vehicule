<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Gestion des <span class="accent">agences</span></h1>
        <p>Créer, modifier et gérer les agences de location</p>
    </div>

    <a href="<?= APP_URL ?>/index.php?page=admin-dashboard" class="btn btn-ghost btn-sm" style="margin-bottom:28px;">
        ← Dashboard
    </a>

    <div class="admin-two-col">

        <!-- ============================================================
             FORMULAIRE AJOUT
        ============================================================ -->
        <div class="admin-form-card">
            <h3 class="admin-form-section-title">➕ Ajouter une agence</h3>

            <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-agence-store" novalidate>
                <?php $csrf = Session::generateCsrf(); ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group" style="margin-bottom:16px;">
                    <label>Nom de l'agence <span class="required">*</span></label>
                    <input type="text" name="nom" placeholder="Agence Douala Centre" required>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label>Ville <span class="required">*</span></label>
                    <input type="text" name="ville" placeholder="Douala" required>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label>Adresse <small style="color:var(--text-muted);text-transform:none;font-weight:400;">(optionnel)</small></label>
                    <input type="text" name="adresse" placeholder="Rue de la Joie, BP 1234">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="number" name="latitude"  step="0.000001" placeholder="4.051056">
                    </div>
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="number" name="longitude" step="0.000001" placeholder="9.767869">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">
                    ✅ Enregistrer l'agence
                </button>
            </form>
        </div>

        <!-- ============================================================
             LISTE DES AGENCES
        ============================================================ -->
        <div>
            <?php if (empty($agences)): ?>
                <div class="empty-state">
                    <span class="empty-icon">🏢</span>
                    <p>Aucune agence enregistrée.</p>
                </div>
            <?php else: ?>
                <p class="result-count"><?= count($agences) ?> agence(s)</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Agence</th>
                                <th>Ville</th>
                                <th>Véhicules</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($agences as $ag): ?>
                        <tr>
                            <td style="color:var(--text-muted);font-size:12px;"><?= (int)$ag['ID_AGENCE'] ?></td>

                            <td>
                                <strong><?= htmlspecialchars($ag['NOM_AGENCE_']) ?></strong>
                                <?php if (!empty($ag['ADRESSE_'])): ?>
                                <small style="display:block;color:var(--text-muted);font-size:11px;">
                                    <?= htmlspecialchars($ag['ADRESSE_']) ?>
                                </small>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($ag['VILLE'] ?? '—') ?></td>

                            <td>
                                <span class="badge badge-blue"><?= (int)$ag['nb_vehicules'] ?> véhicule(s)</span>
                            </td>

                            <td>
                                <?php $sc = ($ag['STATU_VALIDATION'] === 'active') ? 'badge-success' : 'badge-neutral'; ?>
                                <span class="badge <?= $sc ?>">
                                    <?= ($ag['STATU_VALIDATION'] === 'active') ? '● Active' : '○ Inactive' ?>
                                </span>
                            </td>

                            <td>
                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">

                                    <!-- Modifier -->
                                    <button class="btn btn-ghost btn-sm"
                                            onclick="openEditAgence(
                                                <?= (int)$ag['ID_AGENCE'] ?>,
                                                '<?= htmlspecialchars(addslashes($ag['NOM_AGENCE_'])) ?>',
                                                '<?= htmlspecialchars(addslashes($ag['ADRESSE_'] ?? '')) ?>',
                                                '<?= htmlspecialchars(addslashes($ag['VILLE'] ?? '')) ?>',
                                                '<?= $ag['LATITUDE'] ?? '' ?>',
                                                '<?= $ag['LONGITUDE'] ?? '' ?>',
                                                '<?= $ag['STATU_VALIDATION'] ?? 'active' ?>'
                                            )" title="Modifier">✏️</button>

                                    <!-- Toggle statut -->
                                    <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-agence-statut">
                                        <?php $cs = Session::generateCsrf(); ?>
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($cs) ?>">
                                        <input type="hidden" name="agence_id"  value="<?= (int)$ag['ID_AGENCE'] ?>">
                                        <input type="hidden" name="statut"     value="<?= $ag['STATU_VALIDATION'] === 'active' ? 'inactive' : 'active' ?>">
                                        <button type="submit" class="btn btn-ghost btn-sm"
                                                title="<?= $ag['STATU_VALIDATION'] === 'active' ? 'Désactiver' : 'Activer' ?>">
                                            <?= $ag['STATU_VALIDATION'] === 'active' ? '🔴' : '🟢' ?>
                                        </button>
                                    </form>

                                    <!-- Supprimer -->
                                    <?php if ((int)$ag['nb_vehicules'] === 0): ?>
                                    <a href="<?= APP_URL ?>/index.php?page=admin-agence-supprimer&id=<?= (int)$ag['ID_AGENCE'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Supprimer cette agence ?')" title="Supprimer">🗑️</a>
                                    <?php else: ?>
                                    <span class="btn btn-ghost btn-sm" style="opacity:.4;cursor:not-allowed;" title="Des véhicules sont rattachés">🗑️</span>
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
         MODAL ÉDITION AGENCE
    ============================================================ -->
    <div id="modal-edit-agence" class="admin-modal" style="display:none;">
        <div class="admin-modal-box">
            <h3 style="color:var(--white);margin-bottom:20px;">✏️ Modifier l'agence</h3>

            <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-agence-update" novalidate>
                <?php $csrf2 = Session::generateCsrf(); ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf2) ?>">
                <input type="hidden" name="agence_id"  id="edit-ag-id">

                <div class="form-group" style="margin-bottom:14px;">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="nom" id="edit-ag-nom" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label>Ville <span class="required">*</span></label>
                    <input type="text" name="ville" id="edit-ag-ville" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label>Adresse</label>
                    <input type="text" name="adresse" id="edit-ag-adresse">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="number" name="latitude" id="edit-ag-lat" step="0.000001">
                    </div>
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="number" name="longitude" id="edit-ag-lng" step="0.000001">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Statut</label>
                    <select name="statut" id="edit-ag-statut">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">💾 Mettre à jour</button>
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-agence')">Annuler</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
function openEditAgence(id, nom, adresse, ville, lat, lng, statut) {
    document.getElementById('edit-ag-id').value     = id;
    document.getElementById('edit-ag-nom').value    = nom;
    document.getElementById('edit-ag-adresse').value= adresse;
    document.getElementById('edit-ag-ville').value  = ville;
    document.getElementById('edit-ag-lat').value    = lat;
    document.getElementById('edit-ag-lng').value    = lng;
    document.getElementById('edit-ag-statut').value = statut;
    document.getElementById('modal-edit-agence').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
document.getElementById('modal-edit-agence').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-edit-agence');
});
</script>
