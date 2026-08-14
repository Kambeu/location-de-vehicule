<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Gestion des <span class="accent">véhicules</span></h1>
        <p>Parc complet — ajout, édition, suppression et statuts</p>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <a href="<?= APP_URL ?>/index.php?page=admin-dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
        <a href="<?= APP_URL ?>/index.php?page=admin-vehicule-ajouter" class="btn btn-primary">
            ➕ Ajouter un véhicule
        </a>
    </div>

    <?php if (empty($vehicules)): ?>
        <div class="empty-state">
            <span class="empty-icon">🚗</span>
            <p>Aucun véhicule enregistré.</p>
            <a href="<?= APP_URL ?>/index.php?page=admin-vehicule-ajouter" class="btn btn-primary btn-sm" style="margin-top:16px;">
                Ajouter le premier véhicule
            </a>
        </div>
    <?php else: ?>
        <p class="result-count"><?= count($vehicules) ?> véhicule(s) au total</p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Véhicule</th>
                        <th>Immatriculation</th>
                        <th>Catégorie</th>
                        <th>Tarif/jour</th>
                        <th>Statut</th>
                        <th>Changer statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($vehicules as $v): ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">#<?= (int)$v['ID_VEHICULE'] ?></td>

                    <td>
                        <?php if (!empty($v['IMAGE_PRINCIPALE'])): ?>
                            <img src="<?= APP_URL ?>/assets/uploads/<?= htmlspecialchars($v['IMAGE_PRINCIPALE']) ?>"
                                 alt="" style="width:60px;height:42px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                        <?php else: ?>
                            <div style="width:60px;height:42px;background:var(--bg-surface);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:20px;border:1px solid var(--border);">🚗</div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <strong><?= htmlspecialchars($v['MARQUE'] . ' ' . $v['MODELE']) ?></strong>
                        <small style="display:block;color:var(--text-muted);">
                            <?= htmlspecialchars($v['TRANSMISSION'] ?? '') ?> · <?= htmlspecialchars($v['CARBURANT'] ?? '') ?> · <?= (int)($v['NOMBRE_PLACES'] ?? 0) ?> places
                        </small>
                    </td>

                    <td style="font-size:13px;"><?= htmlspecialchars($v['IMMATRICULATION'] ?? '—') ?></td>
                    <td><span class="badge badge-blue"><?= htmlspecialchars($v['NOM_CATEGORIE'] ?? '—') ?></span></td>

                    <td><strong style="color:var(--orange);"><?= number_format((float)$v['TARIF_JOUR'], 0, ',', ' ') ?> FCFA</strong></td>

                    <td>
                        <?php
                        $sb = ['disponible' => 'badge-success', 'loue' => 'badge-error', 'maintenance' => 'badge-neutral'];
                        $sc = $sb[$v['STATUT_DISPONIBLE']] ?? 'badge-neutral';
                        ?>
                        <span class="badge <?= $sc ?>"><?= htmlspecialchars($v['STATUT_DISPONIBLE']) ?></span>
                    </td>

                    <td>
                        <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-vehicule-statut"
                              style="display:flex;gap:6px;align-items:center;">
                            <?php $csrf = Session::generateCsrf(); ?>
                            <input type="hidden" name="csrf_token"  value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="vehicule_id" value="<?= (int)$v['ID_VEHICULE'] ?>">
                            <select name="statut"
                                    style="padding:5px 8px;background:var(--bg-surface);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:12px;">
                                <option value="disponible"  <?= $v['STATUT_DISPONIBLE'] === 'disponible'  ? 'selected':'' ?>>Disponible</option>
                                <option value="loue"        <?= $v['STATUT_DISPONIBLE'] === 'loue'        ? 'selected':'' ?>>Loué</option>
                                <option value="maintenance" <?= $v['STATUT_DISPONIBLE'] === 'maintenance' ? 'selected':'' ?>>Maintenance</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm" title="Appliquer">✓</button>
                        </form>
                    </td>

                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="<?= APP_URL ?>/index.php?page=admin-vehicule-editer&id=<?= (int)$v['ID_VEHICULE'] ?>"
                               class="btn btn-ghost btn-sm" title="Modifier">✏️</a>
                            <a href="<?= APP_URL ?>/index.php?page=admin-vehicule-supprimer&id=<?= (int)$v['ID_VEHICULE'] ?>"
                               class="btn btn-danger btn-sm" title="Supprimer"
                               onclick="return confirm('Supprimer ce véhicule définitivement ?')">🗑️</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>
