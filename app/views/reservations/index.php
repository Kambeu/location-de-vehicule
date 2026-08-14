<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Mes <span class="accent">réservations</span></h1>
        <p>Historique et suivi de toutes vos réservations</p>
    </div>

    <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-primary btn-sm" style="margin-bottom:28px;">
        + Nouvelle réservation
    </a>

    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <span class="empty-icon">📋</span>
            <p>Vous n'avez pas encore effectué de réservation.</p>
            <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-primary btn-sm" style="margin-top:16px;">
                🚗 Parcourir les véhicules
            </a>
        </div>

    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Véhicule</th>
                        <th>Catégorie</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Lieu prise</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px;">#<?= (int)$r['ID_RESERVATION'] ?></td>
                        <td><strong><?= htmlspecialchars($r['MARQUE'] . ' ' . $r['MODELE']) ?></strong></td>
                        <td><span class="badge badge-blue"><?= htmlspecialchars($r['NOM_CATEGORIE'] ?? '—') ?></span></td>
                        <td><?= date('d/m/Y', strtotime($r['DATE_DEBUT'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['DATE_FIN'])) ?></td>
                        <td style="font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($r['LIEU_PRISE__EN_CHARGE'] ?? '—') ?></td>
                        <td><strong style="color:var(--orange);"><?= number_format((float)$r['MONTANT_TOTAL'], 0, ',', ' ') ?> FCFA</strong></td>
                        <td>
                            <?php
                            $badges = [
                                'confirmee' => ['label' => '✔ Confirmée', 'class' => 'badge-success'],
                                'annulee'   => ['label' => '✖ Annulée',   'class' => 'badge-error'],
                                'terminee'  => ['label' => '✓ Terminée',  'class' => 'badge-neutral'],
                            ];
                            $b = $badges[$r['STATUT']] ?? ['label' => ucfirst($r['STATUT'] ?? ''), 'class' => 'badge-neutral'];
                            ?>
                            <span class="badge <?= $b['class'] ?>"><?= $b['label'] ?></span>
                        </td>
                        <td>
                            <?php if ($r['STATUT'] === 'confirmee'): ?>
                                <a href="<?= APP_URL ?>/index.php?page=reservation-annuler&id=<?= (int)$r['ID_RESERVATION'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Annuler cette réservation ?')">
                                    Annuler
                                </a>
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:13px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>
