<?php
$h = function ($x) { return htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8'); };
$company = $set['company_name'] !== '' ? $set['company_name'] : 'Company name (set it in HR Settings)';
$title = $types[$l['type']];
$to = $l['addressed_to'] !== '' ? $l['addressed_to'] : 'To whom it may concern';
$csrf_n = $this->security->get_csrf_token_name();
$csrf_v = $this->security->get_csrf_hash();
$when = $l['issued_at'] ? strtotime($l['issued_at']) : time();
?><!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $h($title . ' - ' . $u['name']); ?></title>
<style>
  * { box-sizing: border-box; }
  body { margin: 0; background: #eef2f7; font: 14px/1.5 -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #0f172a; }
  .wrap { max-width: 820px; margin: 0 auto; padding: 16px; }
  .bar { display: flex; gap: 8px; justify-content: space-between; flex-wrap: wrap; align-items: center; }
  .btn { font: inherit; padding: 8px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #0f172a; text-decoration: none; cursor: pointer; display: inline-block; }
  .btn.p { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }
  .btn.r { color: #b91c1c; border-color: #fecaca; }
  .edit { background: #fff; border-radius: 12px; padding: 16px; margin: 12px 0; border: 1px solid #fde68a; }
  .edit label { display: block; font-size: 12.5px; font-weight: 600; color: #475569; margin: 10px 0 4px; }
  .edit input, .edit textarea { width: 100%; font: inherit; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; }
  .edit textarea { min-height: 260px; }
  .row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
  .muted { color: #64748b; font-size: 12.5px; }
  .sheet { background: #fff; border-radius: 12px; padding: 40px 48px; margin-top: 12px; box-shadow: 0 4px 20px rgba(15, 23, 42, .06); min-height: 900px; }
  .lh { border-bottom: 2px solid #1e3a8a; padding-bottom: 12px; margin-bottom: 28px; }
  .lh b { font-size: 20px; }
  .meta { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 22px; }
  h2 { font-size: 17px; text-align: center; text-decoration: underline; margin: 0 0 22px; }
  .body { white-space: pre-wrap; }
  .sign { margin-top: 60px; }
  .sign .line { margin-top: 50px; border-top: 1px solid #94a3b8; width: 220px; padding-top: 4px; }
  @media (max-width: 600px) { .sheet { padding: 22px 18px; min-height: 0; } }
  @media print { body { background: #fff; } .wrap { padding: 0; max-width: none; } .bar, .edit { display: none; } .sheet { box-shadow: none; margin: 0; border-radius: 0; padding: 0; min-height: 0; } @page { margin: 18mm; } }
</style>
</head><body><div class="wrap">
<div class="bar"><a class="btn" href="<?php echo base_url() . $h($back); ?>">&larr; Back</a><span class="muted"><?php echo $h($title); ?> for <?php echo $h($u['name']); ?> · <?php echo $l['status'] === 'Requested' ? 'not issued yet' : $h(strtolower($l['status'])) . ($issued_name ? ' by ' . $h($issued_name) : ''); ?></span><?php if ($l['status'] === 'Issued') { ?><button type="button" class="btn p" onclick="window.print()">Print / Save as PDF</button><?php } ?></div>

<?php if ($can_edit) { ?>
<form class="edit" method="post" action="<?php echo base_url(); ?>manager/hr/letter_save/<?php echo (int)$l['id']; ?>" id="ltForm">
  <input type="hidden" name="<?php echo $h($csrf_n); ?>" value="<?php echo $h($csrf_v); ?>">
  <b>Check the letter, change anything, then issue it.</b>
  <?php if ($l['purpose']) { ?><div class="muted" style="margin-top:4px;">Asked for: <?php echo $h($l['purpose']); ?></div><?php } ?>
  <label for="lt1">Addressed to</label><input id="lt1" name="addressed_to" maxlength="150" value="<?php echo $h($l['addressed_to']); ?>" placeholder="To whom it may concern">
  <label for="lt2">Text</label><textarea id="lt2" name="body"><?php echo $h($body); ?></textarea>
  <div class="muted">Words in [square brackets] are missing from the HR file; fill them in.</div>
  <label for="lt3">Reason, if you decline</label><input id="lt3" name="note" maxlength="255">
  <div class="row"><button type="submit" name="act" value="issue" class="btn p">Issue letter</button><button type="submit" name="act" value="reject" class="btn r" onclick="if(!document.getElementById('lt3').value.trim()){alert('Write the reason first.');document.getElementById('lt3').focus();return false;}">Decline</button></div>
</form>
<?php } ?>

<div class="sheet">
  <div class="lh"><b><?php echo $h($company); ?></b><?php if ($set['trade_licence_no'] !== '') { ?><div class="muted">Trade licence <?php echo $h($set['trade_licence_no']); ?></div><?php } ?></div>
  <div class="meta"><span>Ref: <?php echo $h($l['ref_no'] ?: '(given when issued)'); ?></span><span>Date: <?php echo $h(date('d F Y', $when)); ?></span></div>
  <p id="ltTo"><?php echo $h($to); ?></p>
  <h2><?php echo $h(strtoupper($title)); ?></h2>
  <div class="body" id="ltBody"><?php echo $h($body); ?></div>
  <div class="sign">Yours faithfully,<br>For <?php echo $h($company); ?><div class="line">Authorised signatory</div></div>
</div>
</div>
<script>
(function () {
  var b = document.getElementById('lt2'), t = document.getElementById('lt1');
  if (b) b.addEventListener('input', function () { document.getElementById('ltBody').textContent = b.value; });
  if (t) t.addEventListener('input', function () { document.getElementById('ltTo').textContent = t.value || 'To whom it may concern'; });
})();
</script>
</body></html>
