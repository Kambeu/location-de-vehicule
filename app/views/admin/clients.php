<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Gestion des <span class="accent">clients</span></h1>
        <p>Liste de tous les utilisateurs inscrits</p>
    </div>

    <a href="<?= APP_URL ?>/index.php?page=admin-dashboard" class="btn btn-ghost btn-sm" style="margin-bottom:24px;">
        ← Tableau de bord
    </a>

    <?php if (empty($clients)): ?>
        <div class="empty-state">
            <span class="empty-icon">👥</span>
            <p>Aucun utilisateur inscrit.</p>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Inscription</th>
                    <th>Rôle actuel</th>
                    <th>Changer rôle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $c): ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">#<?= (int)$c['ID_UTILSATEUR'] ?></td>
                    <td><strong><?= htmlspecialchars($c['PRENOM'] . ' ' . $c['NOM']) ?></strong></td>
                    <td style="font-size:13px;"><?= htmlspecialchars($c['ADRESSE_EMAIL'] ?? '—') ?></td>
                    <td style="font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($c['NUMERO_DE_TELEPHONE'] ?? '—') ?></td>
                    <td style="font-size:13px;">
                        <?= !empty($c['DATE_D_INSCRIPTION']) ? date('d/m/Y', strtotime($c['DATE_D_INSCRIPTION'])) : '—' ?>
                    </td>
                    <td>
                        <?php $rc = $c['ROLE'] === 'admin' ? 'badge-error' : 'badge-blue'; ?>
                        <span class="badge <?= $rc ?>"><?= htmlspecialchars($c['ROLE'] ?? 'client') ?></span>
                    </td>
                    <td>
                        <?php if ((int)$c['ID_UTILSATEUR'] !== (int)Session::get('user_id')): ?>
                        <form method="POST" action="<?= APP_URL ?>/index.php?page=admin-client-role"
                              style="display:flex;gap:6px;align-items:center;">
                            <?php $csrf = Session::generateCsrf(); ?>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="client_id" value="<?= (int)$c['ID_UTILSATEUR'] ?>">
                            <select name="role" style="padding:5px 8px;background:var(--bg-surface);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:13px;">
                                <option value="client" <?= ($c['ROLE'] ?? '') === 'client' ? 'selected' : '' ?>>Client</option>
                                <option value="admin"  <?= ($c['ROLE'] ?? '') === 'admin'  ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">✓</button>
                        </form>
                        <?php else: ?>
                            <small style="color:var(--text-muted);">Votre compte</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</main>
