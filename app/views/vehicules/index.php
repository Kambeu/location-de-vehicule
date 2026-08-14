<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Nos <span class="accent">véhicules</span></h1>
        <p>Choisissez parmi notre catalogue de véhicules disponibles à la location</p>
    </div>

    <!-- FILTRES -->
    <form method="GET" action="<?= APP_URL ?>/index.php" class="filtres-wrap">
        <input type="hidden" name="page" value="vehicules">
        <p class="filtres-title">🔍 Filtrer les résultats</p>
        <div class="filtres-grid">

            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['NOM_CATEGORIE']) ?>"
                            <?= ($filtres['categorie'] === $cat['NOM_CATEGORIE']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['NOM_CATEGORIE']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Transmission</label>
                <select name="transmission">
                    <option value="">Toutes</option>
                    <option value="Manuelle"    <?= ($filtres['transmission'] === 'Manuelle')    ? 'selected' : '' ?>>Manuelle</option>
                    <option value="Automatique" <?= ($filtres['transmission'] === 'Automatique') ? 'selected' : '' ?>>Automatique</option>
                </select>
            </div>

            <div class="form-group">
                <label>Prix max / jour (FCFA)</label>
                <input type="number" name="prix_max" min="0" step="1000"
                       placeholder="Ex : 50 000"
                       value="<?= htmlspecialchars($filtres['prix_max'] ?? '') ?>">
            </div>

            <div class="form-group" style="display:flex;gap:8px;align-self:flex-end;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Filtrer</button>
                <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-ghost">✖</a>
            </div>

        </div>
    </form>

    <!-- RÉSULTATS -->
    <?php if (empty($vehicules)): ?>
        <div class="empty-state">
            <span class="empty-icon">🔎</span>
            <p>Aucun véhicule ne correspond à vos critères.</p>
            <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-outline btn-sm">Réinitialiser</a>
        </div>
    <?php else: ?>
        <p class="result-count"><?= count($vehicules) ?> véhicule(s) trouvé(s)</p>
        <div class="vehicules-grid">
            <?php foreach ($vehicules as $v): ?>
            <div class="vehicule-card">
                <div class="card-img">
                    <?php if (!empty($v['IMAGE_PRINCIPALE'])): ?>
                        <img src="<?= APP_URL ?>/assets/uploads/<?= htmlspecialchars($v['IMAGE_PRINCIPALE']) ?>"
                             alt="<?= htmlspecialchars($v['MARQUE'] . ' ' . $v['MODELE']) ?>">
                    <?php else: ?>
                        <div class="card-img-placeholder">🚗</div>
                    <?php endif; ?>
                    <span class="card-badge"><?= htmlspecialchars($v['NOM_CATEGORIE'] ?? 'Véhicule') ?></span>
                </div>
                <div class="card-body">
                    <div class="card-title"><?= htmlspecialchars($v['MARQUE'] . ' ' . $v['MODELE']) ?></div>
                    <div class="card-specs">
                        <?php if (!empty($v['TRANSMISSION'])): ?>
                            <span class="spec-tag">⚙️ <?= htmlspecialchars($v['TRANSMISSION']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($v['CARBURANT'])): ?>
                            <span class="spec-tag">⛽ <?= htmlspecialchars($v['CARBURANT']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($v['NOMBRE_PLACES'])): ?>
                            <span class="spec-tag">💺 <?= (int)$v['NOMBRE_PLACES'] ?> places</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <div class="card-price">
                            <span class="amount"><?= number_format((float)$v['TARIF_JOUR'], 0, ',', ' ') ?> FCFA</span>
                            <span class="unit">par jour</span>
                        </div>
                        <a href="<?= APP_URL ?>/index.php?page=vehicule-detail&id=<?= (int)$v['ID_VEHICULE'] ?>"
                           class="btn btn-primary btn-sm">Voir →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>
