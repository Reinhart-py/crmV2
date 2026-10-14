<?php 
  $current_user_id = $this->session->userdata('manager_id');
  $footer_admin_name = '';
  if ($current_user_id) {
    $u_db = $this->db->select('name, email, mobile')->get_where('ci_user', array('id' => $current_user_id))->row_array();
    if (!empty($u_db['name'])) {
      $footer_admin_name = $u_db['name'];
    }
  }
  if (empty($footer_admin_name)) {
    $footer_admin_name = $this->session->userdata('manager_name') ? $this->session->userdata('manager_name') : 'User';
  }
  $footer_email = $this->session->userdata('manager_email') ? $this->session->userdata('manager_email') : 'hr@7strokes.com';
  $footer_mobile = $this->session->userdata('manager_mobile') ? $this->session->userdata('manager_mobile') : '+971 58 563 7737';
?>
<footer class="main-footer crm-global-footer">
  <div class="footer-container">
    <div class="footer-left">
      &copy; <?php echo date('Y'); ?> 7 Strokes CRM Platform. All rights reserved.
    </div>
    <div class="footer-right">
      Enterprise Real-Time Architecture
    </div>
  </div>
</footer>
</div>
<!-- ./wrapper -->
<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/fastclick/fastclick.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/demo.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo base_url(); ?>assets/plugins/chartjs/Chart.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/multiselect/multiselect-js.js"></script>

<!--<script src="<?php //echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>-->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
<script>
if (window.jQuery && $.fn && $.fn.dataTable) {
  $.extend(true, $.fn.dataTable.defaults, {
    responsive: true,
    autoWidth: false,
    scrollX: false
  });
  var _origDtFn = $.fn.dataTable;
  $.fn.dataTable = function(options) {
    options = options || {};
    options.responsive = true;
    options.scrollX = false;
    return _origDtFn.apply(this, [options]);
  };
  for (var k in _origDtFn) {
    if (Object.prototype.hasOwnProperty.call(_origDtFn, k)) {
      $.fn.dataTable[k] = _origDtFn[k];
    }
  }

  var _origDTFn = $.fn.DataTable;
  $.fn.DataTable = function(options) {
    options = options || {};
    options.responsive = true;
    options.scrollX = false;
    return _origDTFn.apply(this, [options]);
  };
  for (var k2 in _origDTFn) {
    if (Object.prototype.hasOwnProperty.call(_origDTFn, k2)) {
      $.fn.DataTable[k2] = _origDTFn[k2];
    }
  }
}
</script>
<script src="<?php echo base_url(); ?>assets/plugins/countdown/countdown-timer.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/app.min.js"></script>
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery.geocomplete.js"></script> 
<!-- Uplodify-->
<script src="<?php echo base_url(); ?>assets/plugins/uploadify/js/jquery.dm-uploader.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/uploadify/demo-ui.js"></script>
<!--Opup-->
 <script src="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.js"></script>
    <link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css" />
<!-- Page script -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAY0iXbSmvQSxjus3PEJoKnJZreLXQuP1Y&amp;libraries=places"></script> 

<script>
  $(function () {

    $('form').attr('autocomplete', 'off');
    $('.only_number').keyup(function () { 
    this.value = this.value.replace(/[^0-9.]/g,'');
    });

    $('.only_alpha').keyup(function () {
         this.value = this.value.replace(/[^A-Za-z ]/g, "");   

    });
    //Initialize Select2 Elements
    $(".select2").select2();
    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    //Datemask2 mm/dd/yyyy
    $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
    //Money Euro
    $("[data-mask]").inputmask();
    //Date range picker
    $('#reservation').daterangepicker();
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
    //Date range as a button
    $('#daterange-btn').daterangepicker(
        {
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function (start, end) {
          $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    );
    //Date picker

   
    $('.multiselect-ui').multiselect({
        includeSelectAllOption: true
    });	
	
 $('.datepicker').datepicker({
     format: "dd-mm-yyyy",
     autoclose: true,
}).on('changeDate', function (ev) {
     $(this).datepicker('hide');
});
 $('.datepicker1').datepicker({
     format: "yyyy-mm-dd",
     autoclose: true,
}).on('changeDate', function (ev) {
     $(this).datepicker('hide');
});

	
$('.datepicker2').datepicker({
     format: "dd-mm-yyyy",
     startDate: new Date(),
     autoclose: true,
}).on('changeDate', function (ev) {
     $(this).datepicker('hide');
});

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });
    //Colorpicker
    $(".my-colorpicker1").colorpicker();
    //color picker with addon
    $(".my-colorpicker2").colorpicker();
    //Timepicker
    $(".timepicker").timepicker({
      showInputs: false
    });
  });
 $(function(){
	 var addreess_fg= $("#geocomplete").val();
	 //alert(addreess_fg);
        $("#geocomplete").geocomplete({
          	map: ".map_canvas",
			location: addreess_fg,		
          	details: "form",
		  	types: ['establishment'],         
		   	autoselect: true,
          	blur: true,
		 	markerOptions: {
      			draggable: true
    		},
          geocodeAfterResult: true
        });
		
		 $("#geocomplete").bind("geocode:dragged", function(event, latLng){
			
			
          $("input[name=lat]").val(latLng.lat());
          $("input[name=lng]").val(latLng.lng());
		  $.ajax({ 
		  url:'https://maps.googleapis.com/maps/api/geocode/json?latlng='+latLng.lat()+','+latLng.lng()+'&key=AIzaSyAY0iXbSmvQSxjus3PEJoKnJZreLXQuP1Y',
         success: function(data){
            $("input[name=address]").val(data.results[0].formatted_address);
             /*or you could iterate the components for only the city and state*/
         }
}); 
		  
          $("#reset").show();
        });
        
        
        $("#reset").click(function(){
          $("#geocomplete").geocomplete("resetMarker");
          $("#reset").hide();
          return false;
        });
		
 });
  
  $(function () {
    $("#example1").DataTable({
      "pageLength": 50,
      rowReorder: {
            selector: 'td:nth-child(2)'
        },
      responsive: false,
      scrollX: true,
      autoWidth: false
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
  
 
  $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    //CKEDITOR.replace('editor1');
    //bootstrap WYSIHTML5 - text editor
    $(".textarea").wysihtml5();
  });
</script>
<script>
  $(function () {
    //Enable iCheck plugin for checkboxes
    //iCheck for checkbox and radio inputs
    $('.mailbox-messages input[type="checkbox"]').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    });
    //Enable check and uncheck all functionality
    $(".checkbox-toggle").click(function () {
      var clicks = $(this).data('clicks');
      if (clicks) {
        //Uncheck all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
        $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
      } else {
        //Check all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("check");
        $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
      }
      $(this).data("clicks", !clicks);
    });
    //Handle starring for glyphicon and font awesome
   
  });

$(".logout").click(function(){       
    
       swal({
        title: "Are you sure?",
        text: "You want to logout!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: true
      },
      function(isConfirm) {
        if (isConfirm) {
          $.ajax({
             url: '/ci_admin/logout/',            
             error: function() {
                alert('Something is wrong');
             },
             success: function(data) {
                 // $("#"+id).remove();
                swal("Logged Out!", "", "success");
                location.reload();
                  //swal("Logged Out!", "", "success");
             }
          });
        } 
      });
     
    });
</script>

<script src="<?php echo base_url(); ?>assets/dist/js/jquery.slimmenu.js"></script> 
<script>
        $('.munk_menu').slimmenu(
            {
                resizeWidth: '0',
                collapserTitle: '',
                animSpeed: 'medium',
                indentChildren: false,
                childrenIndenter: ''
            });
    </script>
    
<script>
$(window).scroll(function(){
if ($(window).scrollTop() >= 150) {
$('header').addClass('fixed-header');
}
else {
$('header').removeClass('fixed-header');
}
});
</script>

<!-- Floating Help Icon (Bottom-Left) -> Opens Dedicated Help Desk Page -->
<a href="<?php echo base_url(); ?>manager/tickets" class="floating-help-fab" title="Help Desk" id="floatingHelpFab">
  <i class="fa fa-life-ring"></i>
  <span>Help Desk</span>
</a>

<script>
// Authoritative mobile navigation handler for Android / touch / small screen devices
(function() {
  function closeDrawer() {
    document.body.classList.remove('sidebar-open');
  }

  function openDrawer() {
    document.body.classList.remove('sidebar-collapse');
    document.body.classList.add('sidebar-open');
  }

  function toggleDrawer(e) {
    if (e) {
      e.preventDefault();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
      else if (e.stopPropagation) e.stopPropagation();
    }
    if (document.body.classList.contains('sidebar-open')) {
      closeDrawer();
    } else {
      openDrawer();
    }
  }

  document.addEventListener('click', function(e) {
    // 1. Mobile hamburger toggle
    var hamburger = e.target.closest('#crmMobileHamburger');
    if (hamburger) {
      toggleDrawer(e);
      return;
    }

    // 2. Mobile drawer close button
    var closeBtn = e.target.closest('#crmSidebarClose');
    if (closeBtn) {
      e.preventDefault();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
      closeDrawer();
      return;
    }

    // 3. Treeview dropdown toggle inside sidebar
    var treeviewLink = e.target.closest('.main-sidebar .sidebar-menu li.treeview > a');
    if (treeviewLink) {
      e.preventDefault();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
      var li = treeviewLink.closest('li.treeview');
      var isExpanded = li.classList.contains('menu-open') || li.classList.contains('is-expanded');
      var subMenu = li.querySelector('.treeview-menu');

      // Close sibling treeviews in sidebar
      var siblings = li.parentElement.querySelectorAll('li.treeview');
      for (var i = 0; i < siblings.length; i++) {
        var sib = siblings[i];
        if (sib !== li) {
          sib.classList.remove('active', 'menu-open', 'is-expanded');
          var sibSub = sib.querySelector('.treeview-menu');
          if (sibSub) sibSub.style.display = 'none';
          var sibArrow = sib.querySelector('.sidebar-arrow');
          if (sibArrow) {
            sibArrow.classList.remove('fa-angle-down');
            sibArrow.classList.add('fa-angle-left');
          }
        }
      }

      var arrow = li.querySelector('.sidebar-arrow');
      if (isExpanded) {
        li.classList.remove('active', 'menu-open', 'is-expanded');
        if (subMenu) subMenu.style.display = 'none';
        if (arrow) {
          arrow.classList.remove('fa-angle-down');
          arrow.classList.add('fa-angle-left');
        }
      } else {
        li.classList.add('menu-open', 'is-expanded');
        if (subMenu) subMenu.style.display = 'block';
        if (arrow) {
          arrow.classList.remove('fa-angle-left');
          arrow.classList.add('fa-angle-down');
        }
      }
      return;
    }

    // 4. Outside click or backdrop click closes mobile drawer
    if (document.body.classList.contains('sidebar-open')) {
      if (!e.target.closest('.main-sidebar') && !e.target.closest('#crmMobileHamburger')) {
        closeDrawer();
      }
    }
  }, true); // Capture phase ensures this fires first before any library can intercept or cancel

  // Expand active route on load
  function initActiveTreeview() {
    var activeLi = document.querySelector('.main-sidebar .sidebar-menu li.treeview.active');
    if (activeLi) {
      activeLi.classList.add('menu-open', 'is-expanded');
      var subMenu = activeLi.querySelector('.treeview-menu');
      if (subMenu) subMenu.style.display = 'block';
      var arr = activeLi.querySelector('.sidebar-arrow');
      if (arr) {
        arr.classList.remove('fa-angle-left');
        arr.classList.add('fa-angle-down');
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initActiveTreeview);
  } else {
    initActiveTreeview();
  }
})();
</script>

</body>
</html>
