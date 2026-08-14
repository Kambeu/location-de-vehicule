<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>
            <?= $mode === 'ajout' ? '➕ Ajouter un' : '✏️ Modifier le' ?>
            <span class="accent">véhicule</span>
        </h1>
        <p><?= $mode === 'ajout' ? 'Enregistrer un nouveau véhicule dans le parc' : 'Mettre à jour les informations du véhicule' ?></p>
    </div>

    <a href="<?= APP_URL ?>/index.php?page=admin-vehicules" class="btn btn-ghost btn-sm" style="margin-bottom:28px;">
        ← Retour à la liste
    </a>

    <!-- ERREURS -->
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error" style="margin-bottom:24px;">
        <ul style="margin:0;padding-left:18px;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="admin-form-card">

        <form method="POST"
              action="<?= APP_URL ?>/index.php?page=<?= $mode === 'ajout' ? 'admin-vehicule-store' : 'admin-vehicule-update' ?>"
              enctype="multipart/form-data"
              novalidate>

            <?php $csrf = Session::generateCsrf(); ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <?php if ($mode === 'edition'): ?>
                <input type="hidden" name="vehicule_id" value="<?= (int)$vehicule['ID_VEHICULE'] ?>">
            <?php endif; ?>

            <div class="admin-form-grid">

                <!-- COLONNE 1 -->
                <div class="admin-form-col">

                    <h3 class="admin-form-section-title">📋 Informations générales</h3>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Marque <span class="required">*</span></label>
                        <input type="text" name="marque"
                               placeholder="Toyota, Renault, BMW..."
                               value="<?= htmlspecialchars($old['marque'] ?? $vehicule['MARQUE'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Modèle <span class="required">*</span></label>
                        <input type="text" name="modele"
                               placeholder="Corolla, Clio, Série 3..."
                               value="<?= htmlspecialchars($old['modele'] ?? $vehicule['MODELE'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Immatriculation <span class="required">*</span></label>
                        <input type="text" name="immatriculation"
                               placeholder="DL-1234-AB"
                               value="<?= htmlspecialchars($old['immatriculation'] ?? $vehicule['IMMATRICULATION'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Année <span class="required">*</span></label>
                        <input type="date" name="annee"
                               value="<?= htmlspecialchars($old['annee'] ?? $vehicule['ANNEE'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Catégorie <span class="required">*</span></label>
                        <select name="id_categorie" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['ID_CATEGORIE'] ?>"
                                    <?= ((int)($old['id_categorie'] ?? $vehicule['ID_CATEGORIE'] ?? 0)) === (int)$cat['ID_CATEGORIE'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['NOM_CATEGORIE']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Agence <span class="required">*</span></label>
                        <select name="id_agence" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($agences as $ag): ?>
                                <option value="<?= (int)$ag['ID_AGENCE'] ?>"
                                    <?= ((int)($old['id_agence'] ?? $vehicule['ID_AGENCE'] ?? 0)) === (int)$ag['ID_AGENCE'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ag['NOM_AGENCE_']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- COLONNE 2 -->
                <div class="admin-form-col">

                    <h3 class="admin-form-section-title">⚙️ Caractéristiques techniques</h3>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Transmission <span class="required">*</span></label>
                        <select name="transmission" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach (['Manuelle', 'Automatique'] as $t): ?>
                                <option value="<?= $t ?>"
                                    <?= ($old['transmission'] ?? $vehicule['TRANSMISSION'] ?? '') === $t ? 'selected' : '' ?>>
                                    <?= $t ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Carburant <span class="required">*</span></label>
                        <select name="carburant" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach (['Essence', 'Diesel', 'Hybride', 'Électrique', 'GPL'] as $c): ?>
                                <option value="<?= $c ?>"
                                    <?= ($old['carburant'] ?? $vehicule['CARBURANT'] ?? '') === $c ? 'selected' : '' ?>>
                                    <?= $c ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Nombre de places <span class="required">*</span></label>
                        <input type="number" name="nombre_places" min="1" max="50"
                               placeholder="5"
                               value="<?= (int)($old['nombre_places'] ?? $vehicule['NOMBRE_PLACES'] ?? 0) ?: '' ?>"
                               required>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>Tarif journalier (FCFA) <span class="required">*</span></label>
                        <input type="number" name="tarif_jour" min="0" step="500"
                               placeholder="25000"
                               value="<?= (float)($old['tarif_jour'] ?? $vehicule['TARIF_JOUR'] ?? 0) ?: '' ?>"
                               required>
                    </div>

                    <h3 class="admin-form-section-title" style="margin-top:8px;">🖼️ Photo du véhicule</h3>

                    <?php if (!empty($vehicule['IMAGE_PRINCIPALE'])): ?>
                    <div style="margin-bottom:14px;">
                        <img src="<?= APP_URL ?>/assets/uploads/<?= htmlspecialchars($vehicule['IMAGE_PRINCIPALE']) ?>"
                             alt="Photo actuelle"
                             style="width:100%;max-height:160px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                        <p style="font-size:12px;color:var(--text-muted);margin-top:6px;">Photo actuelle — laisser vide pour conserver</p>
                    </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>
                            <?= $mode === 'ajout' ? 'Photo' : 'Nouvelle photo' ?>
                            <small style="color:var(--text-muted);text-transform:none;font-weight:400;">(JPG, PNG, WebP — max 3 Mo)</small>
                        </label>
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                               style="padding:10px;cursor:pointer;">
                    </div>

                </div>
            </div>

            <!-- BOUTONS -->
            <div style="display:flex;gap:12px;margin-top:8px;padding-top:24px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary btn-lg">
                    <?= $mode === 'ajout' ? '✅ Enregistrer le véhicule' : '💾 Mettre à jour' ?>
                </button>
                <a href="<?= APP_URL ?>/index.php?page=admin-vehicules" class="btn btn-ghost btn-lg">
                    Annuler
                </a>
            </div>

        </form>
    </div>

</main>
