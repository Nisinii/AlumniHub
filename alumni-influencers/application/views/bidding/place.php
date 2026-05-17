<div class="card">
    <h2>Place a Bid for Tomorrow</h2>
    <?php if ( ! empty($errors)): ?>
        <div class="alert alert-danger"><ul><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <?php if ( ! $bidding_open): ?>
        <div class="alert alert-danger">Bidding is closed after 6PM. Please come back tomorrow.</div>
        <a href="<?= site_url('bidding') ?>" class="btn btn-secondary">Back</a>

    <?php elseif ($monthly['limit_reached']): ?>
        <div class="alert alert-danger">You have reached your monthly limit of <?= $monthly['monthly_limit'] ?> slots.</div>
        <a href="<?= site_url('bidding') ?>" class="btn btn-secondary">Back</a>

    <?php elseif ($balance <= 0): ?>
        <div class="alert alert-danger">You have no available balance. Accept a sponsor offer first.</div>
        <a href="<?= site_url('bidding/sponsors') ?>" class="btn btn-primary">View Sponsor Offers</a>

    <?php else: ?>
        <div class="alert alert-info">
            Monthly status: <strong><?= $monthly['wins_this_month'] ?>/<?= $monthly['monthly_limit'] ?></strong> slots used.
            Available balance: <strong>£<?= number_format($balance, 2) ?></strong>.
            This is a <strong>blind auction</strong> — you will not see other bids.
        </div>

        <?= form_open('bidding/place') ?>
            <label>Your Bid Amount (£) *</label>
            <input type="number" name="bid_amount" step="0.01" min="0.01" max="<?= $balance ?>" placeholder="e.g. 250.00" required>
            <p class="hint">Max: £<?= number_format($balance, 2) ?>. Winner selected at midnight.</p>
            <div style="display:flex;gap:.8rem">
                <button type="submit" class="btn btn-primary">Place Bid</button>
                <a href="<?= site_url('bidding') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        <?= form_close() ?>
    <?php endif; ?>
</div>