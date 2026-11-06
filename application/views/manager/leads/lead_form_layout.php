<?php /* Shared layout for the Add Database and Edit Lead forms: look only, same fields, names and saving. Include after crmCompactLeadForm(). */ ?>
<style>
/* Add Database and Edit Lead (shared): same look as Add Staff - label beside its box, similar fields grouped and lined up (look only: same fields, names and saving) */
.ldb, .ldb .form-control, .ldb .btn, .ldb select, .ldb .box-title{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
.ldb .content-header{padding:10px 15px 6px}
.ldb .content-header h1{font-size:22px;font-weight:700;letter-spacing:-.02em;color:var(--crm-ink,#0f172a);text-align:left}
.ldb .content{padding-top:0}
.ldb .crm-compact-form .box{margin-bottom:8px !important;border-radius:8px;box-shadow:none;border:1px solid var(--crm-line,#e2e8f0);overflow:visible}
.ldb .crm-compact-form .box > .box-header{padding:8px 12px !important;min-height:0 !important;text-align:left !important;border-left:3px solid var(--crm-ocean,#1e3a8a);border-radius:8px 8px 0 0}
.ldb .crm-compact-form .box > .box-header .box-title{font-size:12.5px !important;font-weight:700 !important;text-transform:uppercase;letter-spacing:.06em;line-height:1.6 !important;color:var(--crm-ink,#0f172a)}
.ldb .crm-compact-form .box > .box-body{padding:10px 12px 4px !important}
/* Grid: 4 fields per row, each group starts on a new row */
.ldb .ldb-grid,.ldb .crm-compact-form .row.ldb-grid{display:grid !important;grid-template-columns:repeat(4,minmax(0,1fr));column-gap:18px;margin:0 !important;width:100%}
.ldb .ldb-grid::before,.ldb .ldb-grid::after{display:none}
.ldb .ldb-grid > [class*="col-"]{width:auto !important;float:none !important;padding:0 !important;min-width:0}
.ldb .ldb-grid > .ldb-wide{grid-column:span 2}
.ldb .ldb-grid > .ldb-full{grid-column:1 / -1}
.ldb .ldb-sub{grid-column:1 / -1;margin:8px 0 7px;padding-bottom:3px;border-bottom:1px dashed var(--crm-line,#e2e8f0);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--crm-ocean,#1e3a8a)}
.ldb .ldb-sub:first-child{margin-top:0}
/* Label on the left, box on the right */
.ldb .ldb-grid .form-group{display:grid;grid-template-columns:118px minmax(0,1fr);column-gap:8px;align-items:center;margin-bottom:7px !important}
.ldb .ldb-grid .form-group > label{margin:0 !important;text-align:right;font-size:12.5px !important;font-weight:500 !important;color:var(--crm-ink-2,#475569) !important;line-height:1.2;word-break:break-word}
.ldb .ldb-grid .form-group > label span,.ldb .ldb-grid .form-group > label .text-danger{color:#dc2626;font-weight:700}
.ldb .ldb-grid .form-group > :not(label){grid-column:2;min-width:0}
.ldb .ldb-grid .form-group.ldb-top{align-items:start}
.ldb .ldb-grid .form-group.ldb-top > label{padding-top:8px}
.ldb .ldb-grid .form-control{height:32px;font-size:13.5px;border-radius:6px}
.ldb .ldb-grid textarea.form-control{height:auto;min-height:56px}
.ldb .ldb-grid .select2-container{width:100% !important}
.ldb .ldb-grid .form-control:focus{border-color:var(--crm-ocean,#1e3a8a) !important;box-shadow:0 0 0 3px rgba(30,58,138,.12) !important}
.ldb .ldb-cmp-link{font-size:12px;font-weight:600;color:var(--crm-ocean,#1e3a8a);margin-top:3px;justify-self:start}
.ldb .crm-addl{margin-top:4px}
/* Edit Lead: record summary */
.ldb .ldb-sum{display:flex;align-items:center;gap:10px 24px;flex-wrap:wrap;margin-bottom:8px;padding:10px 14px;border:1px solid var(--crm-line,#e2e8f0);border-left:3px solid var(--crm-ocean,#1e3a8a);border-radius:8px;background:var(--crm-surface,#fff)}
.ldb .ldb-sum-name{font-size:17px;font-weight:700;color:var(--crm-ink,#0f172a);text-transform:capitalize;margin-right:auto}
.ldb .ldb-sum dl{display:flex;flex-wrap:wrap;gap:6px 22px;margin:0}
.ldb .ldb-sum dt{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--crm-muted,#64748b)}
.ldb .ldb-sum dd{margin:0;font-size:13.5px;font-weight:600;color:var(--crm-ink,#0f172a)}
@media (max-width:575px){.ldb .ldb-sum dl{display:grid;grid-template-columns:1fr 1fr;width:100%}}
/* Save bar always in view */
.ldb .ldb-footer.box-footer{position:sticky;bottom:0;z-index:20;display:flex;justify-content:center;gap:10px;padding:10px 12px !important;margin:6px 0 12px;border:1px solid var(--crm-line,#e2e8f0);border-radius:8px;background:var(--crm-surface,#fff);box-shadow:0 -6px 16px rgba(15,23,42,.08)}
.ldb .ldb-footer .btn{min-width:110px;height:34px;font-weight:600}
@media (max-width:1399px){.ldb .ldb-grid .form-group{grid-template-columns:104px minmax(0,1fr)}}
@media (max-width:1199px){.ldb .ldb-grid,.ldb .crm-compact-form .row.ldb-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:991px){.ldb .ldb-grid,.ldb .crm-compact-form .row.ldb-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:575px){
  .ldb .ldb-grid,.ldb .crm-compact-form .row.ldb-grid{grid-template-columns:1fr}
  .ldb .ldb-grid > .ldb-wide{grid-column:1 / -1}
  .ldb .ldb-grid .form-group{grid-template-columns:1fr}
  .ldb .ldb-grid .form-group > label{text-align:left;margin-bottom:3px !important;padding-top:0 !important}
  .ldb .ldb-grid .form-group > :not(label){grid-column:1}
  .ldb .content-header h1{font-size:19px}
}
</style>
<script>
jQuery(function ($) {
  var $form = $('form[name="add_form"]');
  if (!$form.length) return;
  // Groups of similar fields, in order (fields not listed go to "Other")
  var MAIN = [
    ['Company & contact', ['customer', 'poc', 'contact1', 'alt_contact', 'email', 'location']],
    ['Compliance', ['dnd_status', 'dnd_check_date', 'tl_authority']],
    ['Deal value', ['qty', 'mrc', 'mrr']],
    ['Status & next follow-up', ['status', 'followup_date', 'followup_time']]
  ];
  var MORE = [
    ['Account', ['a_number', 'a_department', 'r_amount', 'r_date']],
    ['Company profile', ['category', 'company_status', 'industry_category', 'lead_cat', 'corporate_tax_status', 'vat_status', 'database_category']],
    ['Products & services', ['s_cat', 'p_cat', 'p_details']],
    ['Trade licence', ['tl_number', 'tl_emirate', 'tl_zone', 'tl_issue_date', 'tl_expire_date']],
    ['Quote & deal', ['revenue', 'otc', 'quote_status', 'quote_mode', 'quote_amount', 'close_date', 'status_category', 'status_remarks']],
    ['Address', ['address', 'country', 'state', 'city']],
    ['Other contacts', ['poc2', 'contact2', 'c_partner']],
    ['Owner & source', ['primary_owner', 'lead_assign', 'lead_source', 'data_source', 'data_batch_referral_no']]
  ];
  function keyOf($col) {
    var k = '';
    $col.find('[name],[data-mirror-of]').each(function () { k = String($(this).attr('name') || $(this).attr('data-mirror-of')).replace('[]', ''); return false; });
    return k;
  }
  function regroup($row, groups) {
    if (!$row.length) return;
    var $cols = $row.children('[class*="col-"]'), byKey = {}, used = [], $tail = $();
    $cols.each(function () { var k = keyOf($(this)); if (k) byKey[k] = $(this); });
    $row.addClass('ldb-grid');
    var $frag = $('<div>');
    $.each(groups, function (_, g) {
      var items = [];
      $.each(g[1], function (_, k) { if (byKey[k]) { items.push(byKey[k][0]); used.push(byKey[k][0]); } });
      if (items.length) { $frag.append($('<div class="ldb-sub">').text(g[0])); $.each(items, function (_, el) { $frag.append(el); }); }
    });
    $cols.each(function () {
      if (used.indexOf(this) !== -1) return;
      if ($(this).find('textarea').length) $tail = $tail.add(this); else $tail = $tail.add(this);
    });
    var $plain = $tail.filter(function () { return !$(this).find('textarea').length; });
    if ($plain.length) $frag.append($('<div class="ldb-sub">').text('Other')).append($plain);
    $frag.append($tail.filter(function () { return $(this).find('textarea').length; }).addClass('ldb-full'));
    $row.prepend($frag.children());
  }
  var $initial = $form.children('.box').first();
  regroup($initial.children('.box-body').children('.row').first(), MAIN);
  regroup($form.find('.crm-addl-grid').first(), MORE);

  // Company: "Add New Company" link moves under the box so the label lines up with the others
  var $link = $('#toggleCompanyModeBtn'), $hd = $link.parent();
  if ($hd.length && $hd.find('label').length) {
    $hd.before($hd.find('label').removeAttr('style'));
    $link.addClass('ldb-cmp-link').removeAttr('style');
    $('#companyInputWrap').after($link);
    $hd.remove();
  }

  // Meeting and Vertical boxes: same grid, label beside box
  $form.children('.box').each(function () {
    var t = $(this).find('.box-title').first().text();
    var $body = $(this).children('.box-body');
    if (/meeting/i.test(t)) {
      // One grid; on Edit Lead each saved meeting gets its own heading, then the new one
      var $g = $('<div class="row ldb-grid">');
      var $sets = $body.find('.remark_row');
      if (!$sets.length) $sets = $body.children();
      $sets.each(function (i) {
        if ($sets.length > 1) $g.append($('<div class="ldb-sub">').text(i < $sets.length - 1 ? 'Meeting ' + (i + 1) : 'New meeting'));
        $(this).find('.form-group').each(function () {
          var $c = $('<div class="col-lg-3">').append(this);
          if ($(this).find('textarea').length) $c.addClass('ldb-wide');
          $g.append($c);
        });
      });
      $sets.remove();
      $body.prepend($g);
    } else if (/vertical/i.test(t)) {
      var $r = $body.children('.row').first().addClass('ldb-grid');
      $r.children('[class*="col-"]').last().addClass('ldb-wide');
    }
  });
  $form.find('.ldb-grid .form-group').each(function () { if ($(this).find('textarea, select[multiple]').length) $(this).addClass('ldb-top'); });
  // Save bar: out of the Back Office box (it was hidden while that box was closed), always in view
  $('#add_btn').closest('.box-footer').addClass('ldb-footer').appendTo($form);
});
</script>
