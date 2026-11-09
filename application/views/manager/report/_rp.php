<?php
/* Shared look for the report pages: filter bar, tiles, table, pager. Include after the header. */
$e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$num = function ($v) { return number_format((float)$v); };
$aed = function ($v) { return 'AED ' . number_format((float)$v, 0); };
$day = function ($v) { $v = substr(trim((string)$v), 0, 10); $t = ($v !== '' && $v !== '0000-00-00') ? strtotime($v) : false; return $t ? date('d-M-Y', $t) : ''; };
$csv_url = function () { $q = $_GET; $q['export'] = 'csv'; return '?' . http_build_query($q); };
?>
<style>
.rp-head{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:14px 15px 10px}
.rp-head h1{margin:0;font-size:20px;font-weight:700;color:var(--crm-text,#0f172a)}
.rp-box{border:1px solid var(--crm-border,#e2e8f0);border-radius:6px;background:var(--crm-surface,#fff);padding:12px 14px;margin-bottom:12px}
.rp-filter{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.rp-filter label{display:block;font-size:12px;color:var(--crm-text-muted,#475569);margin:0 0 3px;font-weight:600}
.rp-filter .form-control{height:34px;min-width:140px}
.rp-filter .rp-btns{display:flex;gap:6px}
.rp-filter .btn{height:34px}
.rp-kpis{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px}
.rp-kpi{flex:1 1 130px;min-width:120px;background:var(--crm-surface,#fff);border:1px solid var(--crm-border,#e2e8f0);border-radius:6px;padding:8px 12px}
.rp-kpi span{display:block;font-size:11px;font-weight:600;color:var(--crm-text-muted,#64748b);text-transform:uppercase;letter-spacing:.3px;white-space:nowrap}
.rp-kpi b{display:block;font-size:17px;color:var(--crm-text,#0f172a);white-space:nowrap}
.rp-kpi b.rp-sm{font-size:14px;white-space:normal}
.rp-table{margin:0}
.rp-table th{background:#f1f5f9;color:#0f172a;font-weight:700;white-space:nowrap;font-size:12.5px}
.rp-table td{font-size:13px;vertical-align:top}
.rp-table td.n,.rp-table th.n{text-align:right;white-space:nowrap}
.rp-table td.nw{white-space:nowrap}
.rp-table tr.rp-grp td{background:#f8fafc;font-weight:700;color:#0f172a}
.rp-table tfoot td{font-weight:700;background:#f8fafc}
.rp-muted{color:#64748b}
[data-theme="dark"] .rp-muted{color:#94a3b8}
.rp-wrap{max-width:420px;white-space:normal;word-break:break-word}
.rp-count{font-size:12px;color:var(--crm-text-muted,#64748b);margin:0 0 8px}
.rp-empty{margin:0}
@media (max-width:700px){.rp-filter>div{flex:1 1 140px}.rp-filter .form-control{min-width:0;width:100%}}
</style>
