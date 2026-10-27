<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.chart-fullscreen-container {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    height: calc(100vh - 170px);
    min-height: 720px;
    position: relative;
    overflow: auto;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.chart-toolbar {
    position: sticky;
    top: 0;
    left: 0;
    right: 0;
    z-index: 50;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.chart-toolbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.chart-toolbar-right {
    display: flex;
    align-items: center;
    gap: 8px;
}
.chart-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #0f172a;
    transition: all 0.15s ease;
    cursor: pointer;
    text-decoration: none !important;
}
.chart-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}
.chart-btn.btn-dark-pill {
    background: #0f172a;
    border-color: #0f172a;
    color: #ffffff;
}
.chart-btn.btn-dark-pill:hover {
    background: #1e293b;
    border-color: #1e293b;
    color: #ffffff;
}
.chart-canvas {
    flex: 1;
    min-width: 100%;
    min-height: 100%;
    padding: 40px 30px 80px;
    transform-origin: top center;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-block;
    box-sizing: border-box;
}
.tree-node-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}
.tree-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 14px;
    min-width: 170px;
    max-width: 220px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    text-decoration: none !important;
    color: #0f172a;
    position: relative;
    z-index: 5;
    cursor: pointer;
}
.tree-card:hover {
    border-color: #0072f0;
    box-shadow: 0 6px 16px rgba(0, 114, 240, 0.12);
    transform: translateY(-2px);
    color: #0f172a;
}
.tree-card.root-card {
    background: linear-gradient(135deg, #0072f0 0%, #0056b3 100%);
    border-color: #0056b3;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 114, 240, 0.25);
}
.tree-card.root-card .node-name {
    color: #ffffff;
    font-weight: 600;
}
.tree-card.root-card .node-sub {
    color: rgba(255, 255, 255, 0.85);
}
.node-avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    color: #334155;
    flex-shrink: 0;
    overflow: hidden;
}
.tree-card.root-card .node-avatar-circle {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}
.node-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.node-info-text {
    flex: 1;
    min-width: 0;
    text-align: left;
}
.node-name {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}
.node-sub {
    font-size: 11px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}
.node-count-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #0f172a;
    color: #ffffff;
    border: 2px solid #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}
.tree-children-container {
    display: flex;
    justify-content: center;
    padding-top: 36px;
    position: relative;
    margin: 0 auto;
}
.tree-children-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 2px;
    height: 36px;
    background: #cbd5e1;
    transform: translateX(-50%);
}
.tree-child-branch {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    padding: 0 14px;
}
.tree-child-branch::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 2px;
    height: 24px;
    background: #cbd5e1;
    transform: translateX(-50%);
}
.tree-children-container > .tree-child-branch::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #cbd5e1;
}
.tree-children-container > .tree-child-branch:first-child::after {
    left: 50%;
    width: 50%;
}
.tree-children-container > .tree-child-branch:last-child::after {
    width: 50%;
}
.tree-children-container > .tree-child-branch:only-child::after {
    display: none;
}
.tree-subchildren-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 28px;
    position: relative;
    width: 100%;
    align-items: center;
}
.tree-subchildren-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 2px;
    height: 28px;
    background: #cbd5e1;
    transform: translateX(-50%);
}
.tree-subcard {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 7px 10px;
    width: 100%;
    min-width: 150px;
    max-width: 180px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    text-decoration: none !important;
    color: #334155;
    transition: all 0.15s ease;
}
.tree-subcard:hover {
    border-color: #0072f0;
    color: #0072f0;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0, 114, 240, 0.1);
}
.tree-subcard .sub-name {
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.empty-children-pill {
    margin-top: 10px;
    font-size: 10px;
    color: #94a3b8;
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 10px;
}
</style>

<div class="content-wrapper px-4">
    <section class="content-header" style="padding-bottom: 8px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1 style="margin:0; font-size:22px; font-weight:600; color:#0f172a;">
                <?php echo htmlspecialchars($info['page_heading']); ?>
            </h1>
            <div style="font-size:13px; color:#64748b;">
                Viewing hierarchy for: <strong style="color:#0f172a;"><?php echo htmlspecialchars($main_user['name']); ?></strong>
            </div>
        </div>
    </section>

    <section class="content" style="padding-top: 6px;">
        <div class="row">
            <div class="col-xs-12">
                <div class="chart-fullscreen-container" id="chartContainer">
                    <div class="chart-toolbar">
                        <div class="chart-toolbar-left">
                            <?php if (!empty($back_user['manager']) && (int)$back_user['manager'] !== (int)$main_user['id']) { ?>
                            <a href="<?php echo base_url(); ?>manager/team/chart?id=<?php echo (int)$back_user['manager']; ?>" class="chart-btn btn-dark-pill" title="Navigate to upline manager">
                                <i class="fa fa-arrow-up"></i> Go to Manager
                            </a>
                            <?php } ?>
                            <a href="<?php echo base_url(); ?>manager/team/chart" class="chart-btn" title="Reset to my own hierarchy root">
                                <i class="fa fa-home"></i> My Team Root
                            </a>
                            <a href="<?php echo base_url(); ?>manager/team/members" class="chart-btn">
                                <i class="fa fa-list"></i> Table View
                            </a>
                        </div>
                        <div class="chart-toolbar-right">
                            <span style="font-size:12px; color:#64748b; margin-right:8px;">Zoom: <strong id="zoomLevel">100%</strong></span>
                            <button type="button" class="chart-btn" onclick="zoomChart(-0.1)" title="Zoom Out"><i class="fa fa-search-minus"></i></button>
                            <button type="button" class="chart-btn" onclick="resetZoom()" title="Reset Zoom"><i class="fa fa-undo"></i> 100%</button>
                            <button type="button" class="chart-btn" onclick="zoomChart(0.1)" title="Zoom In"><i class="fa fa-search-plus"></i></button>
                            <button type="button" class="chart-btn" onclick="toggleFullscreen()" title="Toggle Browser Fullscreen"><i class="fa fa-arrows-alt" id="fsIcon"></i> Fullscreen</button>
                        </div>
                    </div>

                    <div class="chart-canvas" id="chartCanvas">
                        <div class="tree-node-wrapper">
                            <div class="tree-card root-card" title="Click to refresh root">
                                <div class="node-avatar-circle">
                                    <?php if (!empty($main_user['avatar']) && file_exists(FCPATH . $main_user['avatar'])) { ?>
                                        <img src="<?php echo base_url() . $main_user['avatar']; ?>" class="node-avatar-img" alt="<?php echo htmlspecialchars($main_user['name']); ?>" />
                                    <?php } else { ?>
                                        <?php echo strtoupper(substr($main_user['name'], 0, 1)); ?>
                                    <?php } ?>
                                </div>
                                <div class="node-info-text">
                                    <div class="node-name"><?php echo htmlspecialchars($main_user['name']); ?></div>
                                    <div class="node-sub"><?php echo !empty($main_user['department']) ? htmlspecialchars($main_user['department']) : 'Team Leader'; ?></div>
                                </div>
                                <?php if (!empty($team_tree['children'])) { ?>
                                    <span class="node-count-badge" title="Direct team members"><?php echo count($team_tree['children']); ?></span>
                                <?php } ?>
                            </div>

                            <?php if (!empty($team_tree['children'])) { ?>
                            <div class="tree-children-container">
                                <?php foreach ($team_tree['children'] as $child) { 
                                    $sub_count = !empty($child['children']) ? count($child['children']) : 0;
                                ?>
                                <div class="tree-child-branch">
                                    <a href="<?php echo base_url(); ?>manager/team/chart?id=<?php echo $child['id']; ?>" class="tree-card" title="Click to view full branch of <?php echo htmlspecialchars($child['name']); ?>">
                                        <div class="node-avatar-circle">
                                            <?php if (!empty($child['avatar']) && file_exists(FCPATH . $child['avatar'])) { ?>
                                                <img src="<?php echo base_url() . $child['avatar']; ?>" class="node-avatar-img" alt="<?php echo htmlspecialchars($child['name']); ?>" />
                                            <?php } else { ?>
                                                <?php echo strtoupper(substr($child['name'], 0, 1)); ?>
                                            <?php } ?>
                                        </div>
                                        <div class="node-info-text">
                                            <div class="node-name"><?php echo htmlspecialchars($child['name']); ?></div>
                                            <div class="node-sub"><?php echo !empty($child['department']) ? htmlspecialchars($child['department']) : 'Team Member'; ?></div>
                                        </div>
                                        <?php if ($sub_count > 0) { ?>
                                            <span class="node-count-badge" title="<?php echo $sub_count; ?> subordinate members"><?php echo $sub_count; ?></span>
                                        <?php } ?>
                                    </a>

                                    <?php if ($sub_count > 0) { ?>
                                    <div class="tree-subchildren-container">
                                        <?php foreach ($child['children'] as $subc) { ?>
                                        <a href="<?php echo base_url(); ?>manager/team/chart?id=<?php echo $subc['id']; ?>" class="tree-subcard" title="Subordinate under <?php echo htmlspecialchars($child['name']); ?>">
                                            <div class="node-avatar-circle" style="width:24px; height:24px; font-size:11px;">
                                                <?php echo strtoupper(substr($subc['name'], 0, 1)); ?>
                                            </div>
                                            <div style="flex:1; min-width:0;">
                                                <div class="sub-name"><?php echo htmlspecialchars($subc['name']); ?></div>
                                            </div>
                                            <?php if (!empty($subc['children'])) { ?>
                                                <span style="font-size:9px; background:#e2e8f0; color:#334155; padding:1px 5px; border-radius:6px; font-weight:600;"><?php echo count($subc['children']); ?></span>
                                            <?php } ?>
                                        </a>
                                        <?php } ?>
                                    </div>
                                    <?php } else { ?>
                                    <span class="empty-children-pill">No reportees</span>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } else { ?>
                            <div style="margin-top: 40px; padding: 20px 30px; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px; color: #64748b; font-size: 14px;">
                                <i class="fa fa-info-circle" style="margin-right: 6px;"></i> No direct team members report to this user.
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
var currentZoom = 1;
function updateZoom() {
    var canvas = document.getElementById('chartCanvas');
    var label = document.getElementById('zoomLevel');
    if (canvas) {
        canvas.style.transform = 'scale(' + currentZoom + ')';
    }
    if (label) {
        label.innerText = Math.round(currentZoom * 100) + '%';
    }
}
function zoomChart(delta) {
    currentZoom = Math.min(Math.max(0.4, currentZoom + delta), 2.0);
    updateZoom();
}
function resetZoom() {
    currentZoom = 1;
    updateZoom();
}
function toggleFullscreen() {
    var elem = document.getElementById('chartContainer');
    var icon = document.getElementById('fsIcon');
    if (!document.fullscreenElement) {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
        if (icon) {
            icon.className = 'fa fa-compress';
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
        if (icon) {
            icon.className = 'fa fa-arrows-alt';
        }
    }
}
document.addEventListener('fullscreenchange', function() {
    var icon = document.getElementById('fsIcon');
    if (!document.fullscreenElement && icon) {
        icon.className = 'fa fa-arrows-alt';
    }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>