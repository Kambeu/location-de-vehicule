<main>

    <?= Session::getFlash() ?>

    <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-ghost btn-sm" style="margin-bottom:32px;">
        ← Retour au catalogue
    </a>

    <div class="detail-grid">

        <!-- PHOTO -->
        <div class="detail-media">
            <?php if (!empty($vehicule['IMAGE_PRINCIPALE'])): ?>
                <img src="<?= APP_URL ?>/assets/uploads/<?= htmlspecialchars($vehicule['IMAGE_PRINCIPALE']) ?>"
                     alt="<?= htmlspecialchars($vehicule['MARQUE'] . ' ' . $vehicule['MODELE']) ?>">
            <?php else: ?>
                <div class="detail-media-placeholder">🚗</div>
            <?php endif; ?>
            <span class="card-badge"><?= htmlspecialchars($vehicule['NOM_CATEGORIE'] ?? 'Véhicule') ?></span>
        </div>

        <!-- INFOS -->
        <div class="detail-info">

            <h1 class="detail-title"><?= htmlspecialchars($vehicule['MARQUE'] . ' ' . $vehicule['MODELE']) ?></h1>
            <p class="detail-sub">
                Immat. : <?= htmlspecialchars($vehicule['IMMATRICULATION'] ?? '—') ?>
                &nbsp;·&nbsp;
                Année : <?= !empty($vehicule['ANNEE']) ? date('Y', strtotime($vehicule['ANNEE'])) : '—' ?>
            </p>

            <div class="specs-grid">
                <div class="spec-box"><span class="spec-ico">⚙️</span><span class="spec-val"><?= htmlspecialchars($vehicule['TRANSMISSION'] ?? '—') ?></span></div>
                <div class="spec-box"><span class="spec-ico">⛽</span><span class="spec-val"><?= htmlspecialchars($vehicule['CARBURANT'] ?? '—') ?></span></div>
                <div class="spec-box"><span class="spec-ico">💺</span><span class="spec-val"><?= (int)($vehicule['NOMBRE_PLACES'] ?? 0) ?> places</span></div>
                <div class="spec-box"><span class="spec-ico">📦</span><span class="spec-val"><?= htmlspecialchars($vehicule['NOM_CATEGORIE'] ?? '—') ?></span></div>
            </div>

            <div class="detail-price">
                <?= number_format((float)$vehicule['TARIF_JOUR'], 0, ',', ' ') ?> FCFA
                <small>/ jour</small>
            </div>

            <!-- RÉSERVATION -->
            <?php if ($vehicule['STATUT_DISPONIBLE'] === 'disponible'): ?>
                <?php if (Session::isLoggedIn()): ?>

                <form method="POST" action="<?= APP_URL ?>/index.php?page=vehicule-reserver">
                    <input type="hidden" name="vehicule_id" value="<?= (int)$vehicule['ID_VEHICULE'] ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label>📅 Date de début <span class="required">*</span></label>
                            <input type="date" name="date_debut" id="d1" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>📅 Date de fin <span class="required">*</span></label>
                            <input type="date" name="date_fin" id="d2" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>📍 Lieu de prise en charge</label>
                            <input type="text" name="lieu_prise" placeholder="Ex : Aéroport Douala">
                        </div>
                        <div class="form-group">
                            <label>📍 Lieu de retour</label>
                            <input type="text" name="lieu_retour" placeholder="Ex : Centre-ville">
                        </div>
                    </div>

                    <div class="montant-preview" id="preview">
                        💰 Estimation : <strong id="mt">—</strong> FCFA pour <strong id="jrs">—</strong> jour(s)
                    </div>

                    <button type="submit" class="btn btn-primary"
                            style="width:100%;margin-top:20px;padding:14px;font-size:15px;">
                        ✅ Confirmer la réservation
                    </button>
                </form>

                <script>
                const tarif = <?= (float)$vehicule['TARIF_JOUR'] ?>;
                const d1 = document.getElementById('d1');
                const d2 = document.getElementById('d2');
                const prev = document.getElementById('preview');
                const mt = document.getElementById('mt');
                const jrs = document.getElementById('jrs');
                function calc() {
                    if (!d1.value || !d2.value) { prev.style.display='none'; return; }
                    const diff = Math.ceil((new Date(d2.value) - new Date(d1.value)) / 86400000);
                    if (diff <= 0) { prev.style.display='none'; return; }
                    jrs.textContent = diff;
                    mt.textContent = (diff * tarif).toLocaleString('fr-FR');
                    prev.style.display = 'block';
                    d2.min = d1.value;
                }
                d1.addEventListener('change', calc);
                d2.addEventListener('change', calc);
                </script>

                <?php else: ?>
                <div class="alert alert-info" style="margin-top:20px;">
                    ℹ️ <a href="<?= APP_URL ?>/index.php?page=login" style="color:#93C5FD;font-weight:600;">Connectez-vous</a> pour réserver ce véhicule.
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-error" style="margin-top:20px;">
                    ❌ Ce véhicule n'est pas disponible actuellement.
                </div>
            <?php endif; ?>

        </div>
    </div>

</main>
