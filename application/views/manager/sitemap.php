<?php $this->load->view('manager/elements/header'); ?>

<section class="content-header">
  <h1><?php echo $info['page_heading']; ?></h1>
</section>

<div class="content">
  <div class="box p-4" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0; margin-bottom:20px; padding:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <h3 style="margin:0; font-size:16px; font-weight:700; color:#0f172a;">Application Directory</h3>
        <p style="margin:4px 0 0 0; font-size:13px; color:#64748b;">Complete navigational structure and section endpoints</p>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>sitemap.xml" target="_blank" class="btn btn-default" style="border:1px solid #cbd5e1; font-weight:600; font-size:12.5px;">
          <i class="fa fa-code"></i> View XML Sitemap
        </a>
      </div>
    </div>
  </div>

  <div class="box" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0; padding:24px;">
    <div class="row">
      <div class="col-md-3 col-sm-6" style="margin-bottom:24px;">
        <h4 style="font-size:14px; font-weight:700; color:#0072f0; margin-bottom:12px; border-bottom:2px solid #0072f0; padding-bottom:6px;">
          <i class="fa fa-th-large" style="margin-right:6px;"></i> My Zone
        </h4>
        <ul style="list-style:none; padding-left:0; line-height:2.2; font-size:13px;">
          <li><a href="<?php echo base_url(); ?>manager/dashboard" style="color:#334155; text-decoration:none;">Dashboard</a></li>
          <li><a href="<?php echo base_url(); ?>manager/leads/add" style="color:#334155; text-decoration:none;">Add Database</a></li>
          <li><a href="<?php echo base_url(); ?>manager/leads" style="color:#334155; text-decoration:none;">View Database</a></li>
          <li><a href="<?php echo base_url(); ?>manager/leads/view_leads" style="color:#334155; text-decoration:none;">View Leads</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders/my" style="color:#334155; text-decoration:none;">View Orders</a></li>
          <li><a href="<?php echo base_url(); ?>manager/sales" style="color:#334155; text-decoration:none;">View Sales</a></li>
          <li><a href="<?php echo base_url(); ?>manager/leads/followups" style="color:#334155; text-decoration:none;">My Follow-Up</a></li>
          <li><a href="<?php echo base_url(); ?>manager/leads/meetings" style="color:#334155; text-decoration:none;">My Meetings</a></li>
          <li><a href="<?php echo base_url(); ?>manager/verticals/add" style="color:#334155; text-decoration:none;">Add Vertical</a></li>
          <li><a href="<?php echo base_url(); ?>manager/verticals" style="color:#334155; text-decoration:none;">View Vertical</a></li>
          <li><a href="<?php echo base_url(); ?>manager/verticals/followups" style="color:#334155; text-decoration:none;">Vertical Follow-up</a></li>
          <li><a href="<?php echo base_url(); ?>manager/tasks/internal" style="color:#334155; text-decoration:none;">View Internal Task</a></li>
          <li><a href="<?php echo base_url(); ?>manager/tasks/external" style="color:#334155; text-decoration:none;">View External Task</a></li>
          <li><a href="<?php echo base_url(); ?>manager/database-transfer" style="color:#334155; text-decoration:none;">Database Transfer</a></li>
        </ul>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom:24px;">
        <h4 style="font-size:14px; font-weight:700; color:#0072f0; margin-bottom:12px; border-bottom:2px solid #0072f0; padding-bottom:6px;">
          <i class="fa fa-users" style="margin-right:6px;"></i> Team
        </h4>
        <ul style="list-style:none; padding-left:0; line-height:2.2; font-size:13px;">
          <li><a href="<?php echo base_url(); ?>manager/team/members" style="color:#334155; text-decoration:none;">Members</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/chart" style="color:#334155; text-decoration:none;">Chart</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/database" style="color:#334155; text-decoration:none;">Database</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/leads" style="color:#334155; text-decoration:none;">Leads</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/orders" style="color:#334155; text-decoration:none;">Orders</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/verticals" style="color:#334155; text-decoration:none;">Verticals</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/followups" style="color:#334155; text-decoration:none;">Follow up</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/meetings" style="color:#334155; text-decoration:none;">Meetings</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/assignmeetings" style="color:#334155; text-decoration:none;">Assigned Meetings</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/assignleads" style="color:#334155; text-decoration:none;">Assign Database</a></li>
          <li><a href="<?php echo base_url(); ?>manager/tasks" style="color:#334155; text-decoration:none;">Task</a></li>
          <li><a href="<?php echo base_url(); ?>manager/team/tickets" style="color:#334155; text-decoration:none;">Tickets</a></li>
        </ul>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom:24px;">
        <h4 style="font-size:14px; font-weight:700; color:#0072f0; margin-bottom:12px; border-bottom:2px solid #0072f0; padding-bottom:6px;">
          <i class="fa fa-briefcase" style="margin-right:6px;"></i> Back Office
        </h4>
        <ul style="list-style:none; padding-left:0; line-height:2.2; font-size:13px;">
          <li><a href="<?php echo base_url(); ?>manager/leads/review_leads" style="color:#334155; text-decoration:none;">Pending Leads Approval</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders/pending_process" style="color:#334155; text-decoration:none;">Pending Order Process</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders/pending_sales_approval" style="color:#334155; text-decoration:none;">Pending Sales Approval</a></li>
          <li><a href="<?php echo base_url(); ?>manager/kam/onboard" style="color:#334155; text-decoration:none;">Onboard Client</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders/open" style="color:#334155; text-decoration:none;">Open Orders</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders/followups" style="color:#334155; text-decoration:none;">Orders Follow-up</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders?status=completed" style="color:#334155; text-decoration:none;">Completed Orders</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders?status=closed" style="color:#334155; text-decoration:none;">Closed Orders</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders?status=rejected_lost" style="color:#334155; text-decoration:none;">Rejected / Lost Orders</a></li>
          <li><a href="<?php echo base_url(); ?>manager/orders" style="color:#334155; text-decoration:none;">View All Orders</a></li>
        </ul>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom:24px;">
        <h4 style="font-size:14px; font-weight:700; color:#0072f0; margin-bottom:12px; border-bottom:2px solid #0072f0; padding-bottom:6px;">
          <i class="fa fa-phone" style="margin-right:6px;"></i> Operations & Tools
        </h4>
        <ul style="list-style:none; padding-left:0; line-height:2.2; font-size:13px;">
          <li><a href="<?php echo base_url(); ?>manager/calls" style="color:#334155; text-decoration:none;">Phone Dialer & Calls</a></li>
          <li><a href="<?php echo base_url(); ?>manager/live" style="color:#334155; text-decoration:none;">Live Operations</a></li>
          <li><a href="<?php echo base_url(); ?>manager/hr" style="color:#334155; text-decoration:none;">HR Management</a></li>
          <li><a href="<?php echo base_url(); ?>manager/reports/revenue-list" style="color:#334155; text-decoration:none;">Revenue Reports</a></li>
          <li><a href="<?php echo base_url(); ?>manager/reports/company-projections" style="color:#334155; text-decoration:none;">Company Projections</a></li>
          <li><a href="<?php echo base_url(); ?>manager/admin-logs" style="color:#334155; text-decoration:none;">Admin Audit Logs</a></li>
          <li><a href="<?php echo base_url(); ?>manager/users" style="color:#334155; text-decoration:none;">User Management</a></li>
          <li><a href="<?php echo base_url(); ?>manager/roles" style="color:#334155; text-decoration:none;">Roles & Permissions</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view('manager/elements/footer'); ?>
