<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$fd = function ($d) { $d = (float)$d; return ((floor($d) == $d) ? (int)$d : rtrim(rtrim(number_format($d, 1), '0'), '.')); };
$stage = $a['stage'];
$chip = array('Self review' => 'w', 'Manager review' => 'on', 'Completed' => 'ok');
$act = base_url() . 'manager/hr/review_save/' . (int)$a['id'];
$labels = array(1 => '1 - Needs a lot of improvement', 2 => '2 - Below expectations', 3 => '3 - Meets expectations', 4 => '4 - Above expectations', 5 => '5 - Outstanding');
$opts = function ($sel, $half = false) use ($labels, $h) {
  $o = '<option value="">Choose</option>';
  for ($v = 1; $v <= 5; $v += ($half ? .5 : 1)) { $o .= '<option value="' . $v . '"' . ($sel !== null && $sel !== '' && (float)$sel == (float)$v ? ' selected' : '') . '>' . (isset($labels[(string)$v]) ? $h($labels[(string)$v]) : $v) . '</option>'; }
  return $o;
};
$show_self = $acc['own'] || $stage !== 'Self review';
$show_mgr = $stage === 'Completed' || $acc['reviewer'];
$edit_self = $acc['own'] && $stage === 'Self review';
$edit_mgr = $acc['reviewer'] && $stage !== 'Completed';
$back = $acc['hr'] || (!$acc['own'] && !$acc['reviewer']) ? base_url() . 'manager/hr/appraisals' : base_url() . 'manager/hr/me#reviews';
$stars = function ($r) { $s = (int)round((float)$r); return '<span style="color:#f59e0b;letter-spacing:1px;">' . str_repeat('&#9733;', $s) . '<span style="color:#cbd5e1">' . str_repeat('&#9733;', 5 - $s) . '</span></span>'; };
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .rv-steps { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
  .rv-steps span { padding: 6px 12px; border-radius: 999px; font-size: 13px; border: 1px solid var(--crm-line, #e2e8f0); color: var(--crm-muted, #64748b); background: var(--crm-surface, #fff); }
  .rv-steps .on { border-color: var(--crm-ocean, #1e3a8a); color: var(--crm-ocean, #1e3a8a); font-weight: 600; }
  .rv-steps .done:before { content: "\f00c"; font-family: FontAwesome; color: #15803d; margin-right: 6px; }
  .rv-text { white-space: pre-wrap; font-size: 14px; margin: 0 0 12px; }
  .rv-crit { display: grid; grid-template-columns: 1fr 240px; gap: 8px 14px; align-items: center; }
  .rv-crit label { margin: 0; font-weight: 500; }
  @media (max-width: 600px) { .rv-crit { grid-template-columns: 1fr; } }
</style>
<div class="content-wrapper sx sx-hr">
  <section class="sx-head">
    <h1><a href="<?php echo $back; ?>" class="text-muted" aria-label="Back" style="margin-right:6px;"><i class="fa fa-angle-left"></i></a><?php echo $h($a['name']); ?> <span class="chip <?php echo $chip[$stage]; ?>" style="vertical-align:middle;"><?php echo $h($stage); ?></span>
      <small><?php echo $h($a['cycle_name']); ?><?php echo $a['period_from'] ? ' · ' . $h(date('d M Y', strtotime($a['period_from'])) . ' to ' . date('d M Y', strtotime($a['period_to']))) : ''; ?><?php echo $designation ? ' · ' . $h($designation) : ''; ?> · Reviewer: <?php echo $h($a['reviewer_name'] ?: '-'); ?><?php echo $a['due_date'] && $stage !== 'Completed' ? ' · Finish by ' . $h(date('d M Y', strtotime($a['due_date']))) : ''; ?></small></h1>
    <div class="sx-btns">
      <?php if ($acc['hr'] && $stage === 'Self review') { ?><form method="post" action="<?php echo $act; ?>" onsubmit="return confirm('Skip the self review and let the manager review now?');"><input type="hidden" name="act" value="skip_self"><button type="submit" class="btn btn-default"><i class="fa fa-forward"></i> Skip self review</button></form><?php } ?>
      <?php if ($acc['hr'] && $stage === 'Completed' && $a['cycle_id']) { ?><form method="post" action="<?php echo $act; ?>" onsubmit="return confirm('Reopen this review for the manager?');"><input type="hidden" name="act" value="reopen"><button type="submit" class="btn btn-default"><i class="fa fa-unlock"></i> Reopen</button></form><?php } ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <?php if ($a['cycle_id']) { ?>
    <div class="rv-steps"><span class="<?php echo $stage === 'Self review' ? 'on' : 'done'; ?>">1. Self review</span><span class="<?php echo $stage === 'Manager review' ? 'on' : ($stage === 'Completed' ? 'done' : ''); ?>">2. Manager review</span><span class="<?php echo $stage === 'Completed' && !$a['ack_at'] ? 'on' : ($a['ack_at'] ? 'done' : ''); ?>">3. Read and confirmed</span></div>
    <?php } ?>

    <?php if ($a['cycle_id']) { ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-user"></i> Self review</h3><?php if ($a['self_at']) { ?><span class="sx-count">Sent <?php echo $h(date('d M Y', strtotime($a['self_at']))); ?></span><?php } ?></div>
      <div class="sx-card-b">
        <?php if ($edit_self) { ?>
        <form method="post" action="<?php echo $act; ?>">
          <div class="form-group"><label for="rs1">How did the period go? Your main achievements, what was hard, and what you want to learn next.</label><textarea id="rs1" name="self_text" class="form-control" rows="7" maxlength="5000"><?php echo $h($a['self_text']); ?></textarea></div>
          <div class="form-group" style="max-width:340px;"><label for="rs2">How would you rate yourself?</label><select id="rs2" name="self_rating" class="form-control"><?php echo $opts($a['self_rating']); ?></select></div>
          <button type="submit" name="act" value="self_save" class="btn btn-default">Save draft</button> <button type="submit" name="act" value="self_submit" class="btn btn-primary" onclick="return confirm('Send your self review to <?php echo $h(addslashes($a['reviewer_name'])); ?>? You cannot change it afterwards.');"><i class="fa fa-paper-plane"></i> Send to manager</button>
        </form>
        <?php } elseif ($show_self && ($a['self_text'] || $a['self_rating'])) { ?>
          <p class="rv-text"><?php echo $h($a['self_text']); ?></p>
          <?php if ($a['self_rating']) { ?><p>Self rating: <?php echo $stars($a['self_rating']); ?> <b><?php echo $fd($a['self_rating']); ?></b> / 5</p><?php } ?>
        <?php } else { ?><div class="sx-empty"><?php echo $stage === 'Self review' ? 'Waiting for ' . $h($a['name']) . ' to send their self review.' : 'No self review was written.'; ?></div><?php } ?>
      </div>
    </div>
    <?php } ?>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-star-o"></i> <?php echo $a['cycle_id'] ? 'Manager review' : 'Review'; ?></h3><?php if ($a['manager_at']) { ?><span class="sx-count">Finished <?php echo $h(date('d M Y', strtotime($a['manager_at']))); ?></span><?php } ?></div>
      <div class="sx-card-b">
        <?php if ($edit_mgr) { ?>
          <?php if ($stage === 'Self review' && !$acc['hr']) { ?><div class="sx-empty">You can review once <?php echo $h($a['name']); ?> has sent their self review.</div><?php } else { ?>
          <form method="post" action="<?php echo $act; ?>">
            <span class="sx-label">Scores</span>
            <div class="rv-crit" style="margin-bottom:14px;"><?php foreach ($criteria as $i => $c) { ?><label for="sc<?php echo $i; ?>"><?php echo $h($c); ?></label><select id="sc<?php echo $i; ?>" name="score[<?php echo $i; ?>]" class="form-control rvSc"><?php echo $opts(isset($scores[$c]) ? $scores[$c] : ''); ?></select><?php } ?></div>
            <div class="sx-grid" style="grid-template-columns:1fr 1fr;">
              <div class="form-group"><label for="rm1">Overall rating</label><select id="rm1" name="rating" class="form-control"><?php echo $opts((float)$a['rating'] > 0 ? $a['rating'] : '', true); ?></select><p class="help-block" id="rmAvg"></p></div>
              <div class="form-group"><label for="rm2">Targets met (%)</label><input id="rm2" type="number" min="0" max="100" step="1" name="kpi_score" class="form-control" value="<?php echo (float)$a['kpi_score'] ? $h(0 + $a['kpi_score']) : ''; ?>"></div>
            </div>
            <div class="form-group"><label for="rm3">Strengths</label><textarea id="rm3" name="strengths" class="form-control" rows="3"><?php echo $h($a['strengths']); ?></textarea></div>
            <div class="form-group"><label for="rm4">To improve</label><textarea id="rm4" name="improvements" class="form-control" rows="3"><?php echo $h($a['improvements']); ?></textarea></div>
            <div class="form-group"><label for="rm5">Goals for the next period</label><textarea id="rm5" name="goals" class="form-control" rows="3"><?php echo $h($a['goals']); ?></textarea></div>
            <div class="form-group"><label for="rm6">Summary for <?php echo $h($a['name']); ?></label><textarea id="rm6" name="feedback" class="form-control" rows="4"><?php echo $h($a['feedback']); ?></textarea></div>
            <button type="submit" name="act" value="mgr_save" class="btn btn-default">Save draft</button> <button type="submit" name="act" value="mgr_submit" class="btn btn-primary" onclick="return confirm('Finish the review and share it with <?php echo $h(addslashes($a['name'])); ?>?');"><i class="fa fa-check"></i> Finish and share</button>
          </form>
          <?php } ?>
        <?php } elseif ($show_mgr && $stage === 'Completed') { ?>
          <p style="font-size:16px;">Overall: <?php echo $stars($a['rating']); ?> <b><?php echo $fd($a['rating']); ?></b> / 5<?php echo (float)$a['kpi_score'] ? ' · Targets met: <b>' . $fd($a['kpi_score']) . '%</b>' : ''; ?></p>
          <?php if ($scores) { ?><table class="sx-table" style="max-width:520px;margin-bottom:14px;"><?php foreach ($scores as $c => $v) { ?><tr><td><?php echo $h($c); ?></td><td class="nw"><?php echo $stars($v); ?> <?php echo $fd($v); ?></td></tr><?php } ?></table><?php } ?>
          <?php foreach (array('strengths' => 'Strengths', 'improvements' => 'To improve', 'goals' => 'Goals for the next period', 'feedback' => 'Summary') as $k => $t) if (trim((string)$a[$k]) !== '') { ?><span class="sx-label"><?php echo $t; ?></span><p class="rv-text"><?php echo $h($a[$k]); ?></p><?php } ?>
        <?php } else { ?><div class="sx-empty"><?php echo $stage === 'Self review' ? 'The manager reviews after the self review.' : 'Your manager is reviewing. You will be told when it is ready.'; ?></div><?php } ?>
      </div>
    </div>

    <?php if ($stage === 'Completed' && $a['cycle_id']) { ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-check-circle-o"></i> Read and confirmed</h3></div>
      <div class="sx-card-b">
        <?php if ($a['ack_at']) { ?><p><?php echo $h($a['name']); ?> confirmed reading this on <?php echo $h(date('d M Y', strtotime($a['ack_at']))); ?>.</p><?php if ($a['ack_note']) { ?><p class="rv-text"><?php echo $h($a['ack_note']); ?></p><?php } ?>
        <?php } elseif ($acc['own']) { ?>
          <form method="post" action="<?php echo $act; ?>"><input type="hidden" name="act" value="ack">
            <div class="form-group"><label for="ra1">Your comments (optional)</label><textarea id="ra1" name="ack_note" class="form-control" rows="3" maxlength="500"></textarea></div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> I have read my review</button>
          </form>
        <?php } else { ?><div class="sx-empty"><?php echo $h($a['name']); ?> has not confirmed yet.</div><?php } ?>
      </div>
    </div>
    <?php } ?>
  </section>
</div>
<script>
$(function () {
  function avg() { var t = 0, n = 0; $('.rvSc').each(function () { var v = parseFloat(this.value); if (v) { t += v; n++; } }); $('#rmAvg').text(n ? 'Average of the scores: ' + (Math.round(t / n * 10) / 10) + (($('#rm1').val() === '') ? ' (used if you leave this empty)' : '') : ''); }
  $('.rvSc, #rm1').on('change', avg); avg();
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
