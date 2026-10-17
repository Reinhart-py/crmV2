<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  <section class="content-header">
    <h1><?php echo htmlspecialchars($info['page_heading']); ?></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tasks">Task</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>
  
  <section class="content"> 
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/tasks/edit/<?php echo $task['id']; ?>" method="post" name="edit_form">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Initial Category Fields</h3>
          <div class="pull-right">
            <a href="<?php echo base_url(); ?>manager/tasks" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Customer Name<span>*</span></label>
                <input type="text" name="customer" class="form-control" style="text-transform: capitalize;" value="<?php echo htmlspecialchars($task['customer']); ?>" required>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name<span>*</span></label>
                <input type="text" required class="form-control" name="poc" value="<?php echo htmlspecialchars($task['poc']); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number</label>
                <input type="text" class="form-control" name="contact1" value="<?php echo htmlspecialchars($task['contact1']); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($task['email']); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control" style="width: 100%;" name="category">
                  <option value="New" <?php echo ($task['category']=='New') ? 'selected' : ''; ?>>New</option>
                  <option value="Existing" <?php echo ($task['category']=='Existing') ? 'selected' : ''; ?>>Existing</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Business Category</label>
                <input type="text" class="form-control" name="business_category" value="<?php echo htmlspecialchars($task['business_category']); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                  <option value="Open" <?php echo ($task['status']=='Open') ? 'selected' : ''; ?>>Open</option>
                  <option value="In Progress" <?php echo ($task['status']=='In Progress') ? 'selected' : ''; ?>>In Progress</option>
                  <option value="Completed" <?php echo ($task['status']=='Completed') ? 'selected' : ''; ?>>Completed</option>
                  <option value="Closed" <?php echo ($task['status']=='Closed') ? 'selected' : ''; ?>>Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Assigned To</label>
                <select class="form-control" name="assigned_to">
                  <option value="0">Unassigned</option>
                  <?php if(!empty($u_list)){ foreach($u_list as $user){ ?>
                    <option value="<?php echo $user['id']; ?>" <?php echo ($task['assigned_to']==$user['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($user['name']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Follow-up Date</label>
                <input type="text" class="form-control datepicker" name="followup_date" value="<?php echo (!empty($task['followup_date']) && $task['followup_date']!='0000-00-00') ? date('d-m-Y', strtotime($task['followup_date'])) : ''; ?>" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Follow-up Time</label>
                <input type="text" class="form-control" name="followup_time" value="<?php echo htmlspecialchars($task['followup_time']); ?>" placeholder="HH:MM">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Due / Close Date</label>
                <input type="text" class="form-control datepicker" name="close_date" value="<?php echo (!empty($task['close_date']) && $task['close_date']!='0000-00-00') ? date('d-m-Y', strtotime($task['close_date'])) : ''; ?>" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($task['location']); ?>">
              </div>
            </div>

            <div class="col-lg-12 col-sm-12">
              <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($task['address']); ?></textarea>
              </div>
            </div>

            <div class="col-lg-12 col-sm-12">
              <div class="form-group">
                <label>Remarks / Task Details</label>
                <textarea name="remark" class="form-control" rows="3"><?php echo htmlspecialchars($task['remark']); ?></textarea>
              </div>
            </div>
          </div>
        </div>
        
        <div class="box-footer">
          <button type="submit" class="btn btn-primary">Update Task</button>
          <a href="<?php echo base_url(); ?>manager/tasks" class="btn btn-default">Cancel</a>
        </div>
      </div>
    </form>
  </section>
</div>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
