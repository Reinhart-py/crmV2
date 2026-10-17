<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
.report-section-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 22px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}
.report-section-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}
.report-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    cursor: pointer;
    user-select: none;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}
.report-section-title-wrap {
    display: inline-flex;
    align-items: center;
    gap: 12px;
}
.report-section-chevron {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
    transition: transform 0.2s ease;
}
.report-section-title {
    font-size: 16px;
    font-weight: 700;
    color: #111827;
    margin: 0;
    letter-spacing: -0.2px;
}
.report-section-card.collapsed .report-section-chevron {
    transform: rotate(-90deg);
}
.report-section-card.collapsed .report-section-body {
    display: none;
}
.report-items-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.report-item-row {
    border-bottom: 1px solid #f1f5f9;
}
.report-item-row:last-child {
    border-bottom: none;
}
.report-item-link {
    display: block;
    padding: 14px 20px 14px 44px;
    color: #111827;
    font-weight: 500;
    font-size: 14.5px;
    text-decoration: none;
    transition: background-color 0.15s ease, color 0.15s ease, padding-left 0.15s ease;
}
.report-item-link:hover, .report-item-link:focus {
    background-color: #f8fafc;
    color: #000000;
    padding-left: 48px;
    text-decoration: none;
}
.report-count-pill {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 10px;
    border-radius: 12px;
}
</style>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 18px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div>
                <h1 style="font-weight: 700; color: #111827; margin: 0 0 5px 0; font-size: 24px;">
                    Download Report
                </h1>
                <p style="color: #64748b; margin: 0; font-size: 13px;">
                    <?php if ($is_admin_or_support) { ?>
                    Browse all department reports, view data metrics, or download CSV files for any staff member.
                    <?php } else { ?>
                    Access your personal performance and activity reports. Click any report to view and download your records.
                    <?php } ?>
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <button type="button" class="btn btn-sm btn-default" id="toggleAllSectionsBtn" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; color: #111827;">
                    <i class="fa fa-expand" id="toggleAllIcon"></i> <span id="toggleAllText">Expand All</span>
                </button>
            </div>
        </div>
    </section>

    <section class="content" style="padding-top: 0;">
        <div class="row" style="margin-bottom: 18px;">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <div class="input-group" style="box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                    <span class="input-group-addon" style="background: #ffffff; border-right: none; color: #64748b;">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" id="reportSearchFilter" class="form-control" placeholder="Search reports (e.g. Lead, Performance, Quote, VAT)..." style="border-left: none; height: 38px; font-size: 13.5px; border-radius: 0 4px 4px 0;">
                </div>
            </div>
        </div>

        <div id="reportsContainer">
            <?php foreach ($sections as $dept_slug => $dept_data) { ?>
            <div class="report-section-card collapsed" data-dept="<?php echo $dept_slug; ?>">
                <div class="report-section-header" onclick="toggleSection(this);">
                    <div class="report-section-title-wrap">
                        <i class="fa fa-chevron-down report-section-chevron"></i>
                        <h3 class="report-section-title"><?php echo htmlspecialchars($dept_data['dept_title']); ?></h3>
                    </div>
                    <span class="report-count-pill"><?php echo count($dept_data['reports']); ?> reports</span>
                </div>
                <div class="report-section-body">
                    <ul class="report-items-list">
                        <?php foreach ($dept_data['reports'] as $report_slug => $r_item) { ?>
                        <li class="report-item-row" data-title="<?php echo strtolower(htmlspecialchars($r_item['title'])); ?>">
                            <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept_slug; ?>/<?php echo $report_slug; ?>" class="report-item-link">
                                <?php echo htmlspecialchars($r_item['title']); ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <div id="noReportsFound" style="display: none; padding: 40px 20px; text-align: center; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 8px;">
            <i class="fa fa-file-text-o" style="font-size: 32px; color: #94a3b8; margin-bottom: 10px;"></i>
            <h4 style="color: #111827; font-weight: 700; margin: 0 0 5px 0;">No matching reports found</h4>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Try searching with a different term.</p>
        </div>
    </section>
</div>

<script>
function toggleSection(headerElem) {
    var card = headerElem.closest('.report-section-card');
    var isCurrentlyCollapsed = card.classList.contains('collapsed');
    var allCards = document.querySelectorAll('.report-section-card');
    allCards.forEach(function(c) {
        c.classList.add('collapsed');
    });
    if (isCurrentlyCollapsed) {
        card.classList.remove('collapsed');
    }
    var toggleIcon = document.getElementById('toggleAllIcon');
    var toggleText = document.getElementById('toggleAllText');
    if (toggleIcon && toggleText) {
        var anyOpen = document.querySelectorAll('.report-section-card:not(.collapsed)').length > 0;
        toggleIcon.className = anyOpen ? 'fa fa-compress' : 'fa fa-expand';
        toggleText.textContent = anyOpen ? 'Collapse All' : 'Expand All';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('reportSearchFilter');
    var toggleBtn = document.getElementById('toggleAllSectionsBtn');
    var toggleIcon = document.getElementById('toggleAllIcon');
    var toggleText = document.getElementById('toggleAllText');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var cards = document.querySelectorAll('.report-section-card');
            var anyOpen = document.querySelectorAll('.report-section-card:not(.collapsed)').length > 0;
            cards.forEach(function(c) {
                if (anyOpen) {
                    c.classList.add('collapsed');
                } else {
                    c.classList.remove('collapsed');
                }
            });
            if (anyOpen) {
                toggleIcon.className = 'fa fa-expand';
                toggleText.textContent = 'Expand All';
            } else {
                toggleIcon.className = 'fa fa-compress';
                toggleText.textContent = 'Collapse All';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.trim().toLowerCase();
            var cards = document.querySelectorAll('.report-section-card');
            var totalVisible = 0;

            cards.forEach(function(card) {
                var items = card.querySelectorAll('.report-item-row');
                var cardHasMatch = false;

                items.forEach(function(item) {
                    var title = item.getAttribute('data-title') || '';
                    if (!q || title.indexOf(q) !== -1) {
                        item.style.display = '';
                        cardHasMatch = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (cardHasMatch) {
                    card.style.display = '';
                    if (q) {
                        card.classList.remove('collapsed');
                    }
                    totalVisible++;
                } else {
                    card.style.display = 'none';
                }
            });

            var noMatch = document.getElementById('noReportsFound');
            if (noMatch) {
                noMatch.style.display = (totalVisible === 0) ? 'block' : 'none';
            }
        });
    }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
