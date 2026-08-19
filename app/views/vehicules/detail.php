<main>

    <?= Session::getFlash() ?>

    <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-ghost btn-sm" style="margin-bottom:32px;">
        ← Retour au catalogue
    </a>

    <div class="detail-grid">

        <!-- ============================================================
             COLONNE GAUCHE — Carrousel photos + infos
        ============================================================ -->
        <div>

            <?php
            // Construire la liste des photos (table photo_vehicule + IMAGE_PRINCIPALE de secours)
            $allPhotos = [];

            if (!empty($photos)) {
                foreach ($photos as $p) {
                    $src = str_starts_with($p['URL_PHOTO'], 'http')
                        ? $p['URL_PHOTO']
                        : APP_URL . '/assets/uploads/' . $p['URL_PHOTO'];
                    $allPhotos[] = $src;
                }
            }

            // Fallback : si pas de photos dans photo_vehicule, utiliser IMAGE_PRINCIPALE
            if (empty($allPhotos) && !empty($vehicule['IMAGE_PRINCIPALE'])) {
                $img = $vehicule['IMAGE_PRINCIPALE'];
                $allPhotos[] = str_starts_with($img, 'http')
                    ? $img
                    : APP_URL . '/assets/uploads/' . $img;
            }
            ?>

            <!-- ===== CARROUSEL ===== -->
            <div class="carrousel-wrap">

                <?php if (!empty($allPhotos)): ?>

                <!-- Image principale affichée -->
                <div class="carrousel-main">
                    <img id="main-photo"
                         src="<?= htmlspecialchars($allPhotos[0]) ?>"
                         alt="<?= htmlspecialchars($vehicule['MARQUE'] . ' ' . $vehicule['MODELE']) ?>"
                         onerror="this.src=''; this.parentElement.innerHTML='<div class=\'carr-placeholder\'>🚗</div>'">

                    <?php if (count($allPhotos) > 1): ?>
                    <!-- Boutons navigation -->
                    <button class="carr-btn carr-prev" onclick="changePhoto(-1)" aria-label="Photo précédente">&#8249;</button>
                    <button class="carr-btn carr-next" onclick="changePhoto(1)"  aria-label="Photo suivante">&#8250;</button>

                    <!-- Indicateurs points -->
                    <div class="carr-dots">
                        <?php foreach ($allPhotos as $i => $p): ?>
                        <span class="carr-dot <?= $i === 0 ? 'active' : '' ?>"
                              onclick="goToPhoto(<?= $i ?>)"></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Badge catégorie -->
                    <span class="card-badge"><?= htmlspecialchars($vehicule['NOM_CATEGORIE'] ?? 'Véhicule') ?></span>

                    <!-- Badge statut -->
                    <?php if ($vehicule['STATUT_DISPONIBLE'] === 'disponible'): ?>
                    <span class="card-badge-blue" style="top:14px;left:auto;right:14px;">✓ Disponible</span>
                    <?php else: ?>
                    <span style="position:absolute;top:14px;right:14px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;">
                        Indisponible
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Miniatures -->
                <?php if (count($allPhotos) > 1): ?>
                <div class="carr-thumbs">
                    <?php foreach ($allPhotos as $i => $p): ?>
                    <div class="carr-thumb <?= $i === 0 ? 'active' : '' ?>"
                         onclick="goToPhoto(<?= $i ?>)">
                        <img src="<?= htmlspecialchars($p) ?>"
                             alt="Photo <?= $i + 1 ?>"
                             onerror="this.style.display='none'">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <!-- Aucune photo -->
                <div class="carr-main">
                    <div class="carr-placeholder">🚗</div>
                </div>
                <?php endif; ?>

            </div>
            <!-- FIN CARROUSEL -->

        </div>

        <!-- ============================================================
             COLONNE DROITE — Description + Réservation
        ============================================================ -->
        <div class="detail-info">

            <h1 class="detail-title">
                <?= htmlspecialchars($vehicule['MARQUE'] . ' ' . $vehicule['MODELE']) ?>
            </h1>
            <p class="detail-sub">
                Immat. : <strong><?= htmlspecialchars($vehicule['IMMATRICULATION'] ?? '—') ?></strong>
                &nbsp;·&nbsp;
                Année : <strong><?= !empty($vehicule['ANNEE']) ? date('Y', strtotime($vehicule['ANNEE'])) : '—' ?></strong>
            </p>

            <!-- Spécifications -->
            <div class="specs-grid">
                <div class="spec-box">
                    <span class="spec-ico">⚙️</span>
                    <div>
                        <small style="color:var(--text-muted);font-size:11px;text-transform:uppercase;">Transmission</small>
                        <span class="spec-val"><?= htmlspecialchars($vehicule['TRANSMISSION'] ?? '—') ?></span>
                    </div>
                </div>
                <div class="spec-box">
                    <span class="spec-ico">⛽</span>
                    <div>
                        <small style="color:var(--text-muted);font-size:11px;text-transform:uppercase;">Carburant</small>
                        <span class="spec-val"><?= htmlspecialchars($vehicule['CARBURANT'] ?? '—') ?></span>
                    </div>
                </div>
                <div class="spec-box">
                    <span class="spec-ico">💺</span>
                    <div>
                        <small style="color:var(--text-muted);font-size:11px;text-transform:uppercase;">Places</small>
                        <span class="spec-val"><?= (int)($vehicule['NOMBRE_PLACES'] ?? 0) ?> places</span>
                    </div>
                </div>
                <div class="spec-box">
                    <span class="spec-ico">📦</span>
                    <div>
                        <small style="color:var(--text-muted);font-size:11px;text-transform:uppercase;">Catégorie</small>
                        <span class="spec-val"><?= htmlspecialchars($vehicule['NOM_CATEGORIE'] ?? '—') ?></span>
                    </div>
                </div>
            </div>

            <!-- Prix -->
            <div class="detail-price">
                <?= number_format((float)$vehicule['TARIF_JOUR'], 0, ',', ' ') ?> FCFA
                <small>/ jour</small>
            </div>

            <!-- ============================================================
                 FORMULAIRE DE RÉSERVATION
            ============================================================ -->
            <?php if ($vehicule['STATUT_DISPONIBLE'] === 'disponible'): ?>

                <?php if (Session::isLoggedIn()): ?>

                <form method="POST" action="<?= APP_URL ?>/index.php?page=vehicule-reserver" class="resa-form">
                    <?php $csrf = Session::generateCsrf(); ?>
                    <input type="hidden" name="csrf_token"  value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="vehicule_id" value="<?= (int)$vehicule['ID_VEHICULE'] ?>">

                    <h3 style="font-size:15px;font-weight:700;color:var(--white);margin-bottom:16px;">
                        📅 Choisir vos dates
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de début <span class="required">*</span></label>
                            <input type="date" name="date_debut" id="d1"
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Date de fin <span class="required">*</span></label>
                            <input type="date" name="date_fin" id="d2"
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
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

                    <!-- Aperçu prix dynamique -->
                    <div class="montant-preview" id="preview" style="display:none;">
                        💰 Estimation : <strong id="mt">—</strong> FCFA
                        pour <strong id="jrs">—</strong> jour(s)
                    </div>

                    <button type="submit" class="btn btn-primary"
                            style="width:100%;margin-top:20px;padding:15px;font-size:16px;border-radius:10px;">
                        ✅ Confirmer la réservation
                    </button>
                </form>

                <script>
                const tarif = <?= (float)$vehicule['TARIF_JOUR'] ?>;
                const d1 = document.getElementById('d1');
                const d2 = document.getElementById('d2');
                const prev = document.getElementById('preview');
                const mt  = document.getElementById('mt');
                const jrs = document.getElementById('jrs');

                function calc() {
                    if (!d1.value || !d2.value) { prev.style.display='none'; return; }
                    const diff = Math.ceil((new Date(d2.value) - new Date(d1.value)) / 86400000);
                    if (diff <= 0) { prev.style.display='none'; return; }
                    jrs.textContent = diff;
                    mt.textContent  = (diff * tarif).toLocaleString('fr-FR');
                    prev.style.display = 'block';
                    d2.min = d1.value;
                }
                d1.addEventListener('change', calc);
                d2.addEventListener('change', calc);
                </script>

                <?php else: ?>
                <div class="alert alert-info" style="margin-top:20px;">
                    ℹ️ <a href="<?= APP_URL ?>/index.php?page=login"
                           style="color:#93C5FD;font-weight:700;">Connectez-vous</a>
                    pour réserver ce véhicule.
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

<!-- ============================================================
     JAVASCRIPT CARROUSEL
============================================================ -->
<script>
const photos    = <?= json_encode(array_values($allPhotos)) ?>;
let   current   = 0;
const mainImg   = document.getElementById('main-photo');
const dots      = document.querySelectorAll('.carr-dot');
const thumbs    = document.querySelectorAll('.carr-thumb');

function goToPhoto(index) {
    current = (index + photos.length) % photos.length;
    if (mainImg)  mainImg.src = photos[current];

    dots.forEach((d, i)   => d.classList.toggle('active',   i === current));
    thumbs.forEach((t, i) => t.classList.toggle('active',   i === current));
}

function changePhoto(dir) {
    goToPhoto(current + dir);
}

// Auto-défilement toutes les 4 secondes
let autoplay = setInterval(() => changePhoto(1), 4000);

// Pause au survol
const wrap = document.querySelector('.carrousel-wrap');
if (wrap) {
    wrap.addEventListener('mouseenter', () => clearInterval(autoplay));
    wrap.addEventListener('mouseleave', () => {
        autoplay = setInterval(() => changePhoto(1), 4000);
    });
}
</script>
