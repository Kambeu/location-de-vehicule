<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Gestion des <span class="accent">réservations</span></h1>
        <p>Toutes les réservations du système — confirmer, terminer ou annuler</p>
    </div>

    <a href="<?= APP_URL ?>/index.php?page=admin-dashboard" class="btn btn-ghost btn-sm" style="margin-bottom:24px;">
        ← Dashboard
    </a>

    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <span class="empty-icon">📋</span>
            <p>Aucune réservation enregistrée.</p>
        </div>
    <?php else: ?>
        <p class="result-count"><?= count($reservations) ?> réservation(s) au total</p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Véhicule</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Lieu prise</th>
                        <th>Lieu retour</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Modifier statut</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $r): ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">#<?= (int)$r['ID_RESERVATION'] ?></td>

                    <td>
                        <strong><?= htmlspecialchars($r['client_nom']) ?></strong>
                        <small style="display:block;color:var(--text-muted);font-size:11px;">
                            <?= htmlspecialchars($r['client_email']) ?>
                        </small>
                    </td>

                    <td><strong><?= htmlspecialchars($r['MARQUE'] . ' ' . $r['MODELE']) ?></strong></td>

                    <td><?= !empty($r['DATE_DEBUT']) ? date('d/m/Y', strtotime($r['DATE_DEBUT'])) : '—' ?></td>
                    <td><?= !empty($r['DATE_FIN'])   ? date('d/m/Y', strtotime($r['DATE_FIN']))   : '—' ?></td>

                    <td style="font-size:12px;color:var(--text-muted);">
                        <?= htmlspecialchars($r['LIEU_PRISE__EN_CHARGE'] ?? '—') ?>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);">
                        <?= htmlspecialchars($r['LIEU_RETOUR'] ?? '—') ?>
                    </td>

                    <td>
                        <strong style="color:var(--orange);">
                            <?= number_format((float)$r['MONTANT_TOTAL'], 0, ',', ' ') ?> FCFA
                        </strong>
                    </td>

                    <td>
                        <?php
                        $badges = [
                            'confirmee' => 'badge-success',
                            'annulee'   => 'badge-error',
                            'terminee'  => 'badge-neutral',
                        ];
                        $bc = $badges[$r['STATUT']] ?? 'badge-neutral';
                        ?>
                        <span class="badge <?= $bc ?>"><?= htmlspecialchars(ucfirst($r['STATUT'] ?? '')) ?></span>
                    </td>

                    <td>
                        <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-reservation-statut"
                              style="display:flex;gap:6px;align-items:center;">
                            <?php $csrf = Session::generateCsrf(); ?>
                            <input type="hidden" name="csrf_token"      value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="reservation_id"  value="<?= (int)$r['ID_RESERVATION'] ?>">
                            <select name="statut"
                                    style="padding:5px 8px;background:var(--bg-surface);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:12px;">
                                <option value="confirmee" <?= $r['STATUT'] === 'confirmee' ? 'selected':'' ?>>Confirmée</option>
                                <option value="terminee"  <?= $r['STATUT'] === 'terminee'  ? 'selected':'' ?>>Terminée</option>
                                <option value="annulee"   <?= $r['STATUT'] === 'annulee'   ? 'selected':'' ?>>Annulée</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm" title="Appliquer">✓</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>
