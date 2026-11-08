<footer class="crm-footer">
</footer>
</div>
</div>
<script src="<?php echo base_url(); ?>assets/plugins/fastclick/fastclick.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/chartjs/Chart.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/multiselect/multiselect-js.js"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
<script>
(function ($) {
  if (!$ || !$.fn || !$.fn.dataTable) {
    return;
  }
  if (window.innerWidth < 992) {
    $.extend(true, $.fn.dataTable.defaults, { responsive: true });
  }
  $(function () {
    setTimeout(function () {
      $('.content-wrapper table.table[id^="example"]').each(function () {
        var $t = $(this);
        if ($.fn.DataTable.isDataTable(this) || $t.closest('.full-screen-dash').length) {
          return;
        }
        if (!$t.children('thead').length || $t.find('tbody td[colspan]').length || !$t.find('tbody tr').length) {
          return;
        }
        $t.DataTable({ pageLength: 50, responsive: true, order: [] });
      });
    }, 0);
  });
})(window.jQuery);
</script>
<script src="<?php echo base_url(); ?>assets/dist/js/app.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/plugins/sweetalert/sweetalert.js?v=1.0.1"></script>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/sweetalert/sweetalert.css?v=1.0.1" />

<script>
  $(function () {

    $('form').attr('autocomplete', 'off');
    $('.only_number').keyup(function () { 
    this.value = this.value.replace(/[^0-9.]/g,'');
    });

    $('.only_alpha').keyup(function () {
         this.value = this.value.replace(/[^A-Za-z ]/g, "");   

    });
    $("select.select2").select2(); // selects only: a page's own select2 wrappers also have the class
   
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

    $(".timepicker").timepicker({
      showInputs: false
    });
  });
  $(function () {
    // a list with no records shows one row across all columns; DataTables cannot read it, so leave it as it is
    function crmDtOk(sel) { var $t = $(sel); return $t.length && !$.fn.DataTable.isDataTable(sel) && $t.find('tbody tr').length && !$t.find('tbody td[colspan]').length; }
    if (crmDtOk('#example1')) $("#example1").DataTable({
      "pageLength": 50,
      rowReorder: {
            selector: 'td:nth-child(2)'
        },
      responsive: true
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
    if (crmDtOk('#example_lead')) {
      $('#example_lead').DataTable({
        "pageLength": 50,
        responsive: true
      });
    }
  });
  
 
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-logout.css?v=20261108_1">
<script>window.CRM_LO = { base: '<?php echo base_url(); ?>', csrf: '<?php echo $this->security->get_csrf_token_name(); ?>' };</script>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-logout.js?v=20261108_1"></script>

<script src="<?php echo base_url(); ?>assets/dist/js/jquery.slimmenu.js"></script> 
<script>
        $('.munk_menu').slimmenu(
            {
                resizeWidth: '767',
                collapserTitle: '',
                animSpeed: 'medium',
                indentChildren: true,
                childrenIndenter: '&raquo;'
            });
    </script>
    
<script>
$(document).ready(function() {
    $(document).on('click', '#crmMobileHamburger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('body').toggleClass('sidebar-open');
    });

    $(document).on('click', '#crmSidebarClose', function(e) {
        e.preventDefault();
        $('body').removeClass('sidebar-open');
    });

    $(document).on('click', function(e) {
        if ($('body').hasClass('sidebar-open')) {
            if (!$(e.target).closest('.main-sidebar').length && !$(e.target).closest('#crmMobileHamburger').length) {
                $('body').removeClass('sidebar-open');
            }
        }
    });

    $(document).on('click', '.main-sidebar .sidebar-menu > li.treeview > a', function(e) {
        e.preventDefault();
        var $li = $(this).parent('li.treeview');
        var isOpen = $li.hasClass('menu-open') || $li.hasClass('is-expanded');
        $('.main-sidebar .sidebar-menu > li.treeview').removeClass('menu-open is-expanded').find('> .treeview-menu').slideUp(200);
        if (!isOpen) {
            $li.addClass('menu-open is-expanded').find('> .treeview-menu').slideDown(200);
        }
    });

    $(document).on('click', '.munk_menu > li.has-submenu > a', function(e) {
        var href = $(this).attr('href');
        if (href === '#' || href === 'javascript:void(0);' || !href) {
            e.preventDefault();
            var $li = $(this).parent('li');
            var $sub = $li.find('> ul');
            if ($sub.length) {
                var isVis = $sub.is(':visible');
                $('.munk_menu > li.has-submenu > ul').not($sub).slideUp(180);
                if (isVis) {
                    $sub.slideUp(180);
                } else {
                    $sub.slideDown(180);
                }
            }
        }
    });

    $(document).on('click', 'a.btn:contains("Back"), a:has(i.fa-arrow-left)', function(e) {
        if ($(this).attr('href') === 'javascript:window.history.back();' || $(this).data('action') === 'back') {
            e.preventDefault();
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '<?php echo base_url(); ?>manager/dashboard';
            }
        }
    });

    var cName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var cHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    $('form').each(function() {
        var m = $(this).attr('method');
        if (m && m.toLowerCase() === 'post') {
            if (!$(this).find('input[name="' + cName + '"]').length) {
                $(this).prepend('<input type="hidden" name="' + cName + '" value="' + cHash + '">');
            }
        }
    });
    $(document).on('submit', 'form', function() {
        var m = $(this).attr('method');
        if (m && m.toLowerCase() === 'post') {
            if (!$(this).find('input[name="' + cName + '"]').length) {
                $(this).prepend('<input type="hidden" name="' + cName + '" value="' + cHash + '">');
            }
        }
    });
    $(document).ajaxSend(function(event, jqXHR, settings) {
        if (settings.type && settings.type.toUpperCase() === 'POST') {
            jqXHR.setRequestHeader('X-CSRF-TOKEN', cHash);
            if (typeof settings.data === 'string') {
                if (settings.data.indexOf(cName + '=') === -1) {
                    settings.data += (settings.data.length > 0 ? '&' : '') + cName + '=' + encodeURIComponent(cHash);
                }
            } else if (settings.data instanceof FormData) {
                if (!settings.data.has(cName)) {
                    settings.data.append(cName, cHash);
                }
            } else if (typeof settings.data === 'object' && settings.data !== null) {
                settings.data[cName] = cHash;
            }
        }
    });

    function initUniversalPasswordToggles() {
        $('input[type="password"]').each(function() {
            var $pwd = $(this);
            if ($pwd.data('has-eye-toggle')) return;
            $pwd.data('has-eye-toggle', true);
            var $wrap = $pwd.parent();
            if ($wrap.css('position') === 'static' || !$wrap.css('position')) {
                $wrap.css('position', 'relative');
            }
            $pwd.css('padding-right', '38px');
            var $btn = $('<button type="button" class="btn btn-link btn-pwd-eye" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); z-index:5; color:#64748b; padding:4px 6px; text-decoration:none; outline:none; border:none; background:transparent; cursor:pointer;" title="Toggle Password Visibility"><i class="fa fa-eye"></i></button>');
            $btn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if ($pwd.attr('type') === 'password') {
                    $pwd.attr('type', 'text');
                    $btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $pwd.attr('type', 'password');
                    $btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            $pwd.after($btn);
        });
    }
    initUniversalPasswordToggles();
    $(document).ajaxComplete(function() {
        initUniversalPasswordToggles();
    });

    // Sign out after 5 minutes without activity. Activity in any open CRM tab counts (shared through localStorage);
    // a warning shows for the last 60 seconds. Live Support sets crmIdleExempt while the agent is on duty,
    // which keeps every tab signed in.
    (function () {
        var MAX = 300000, WARN = 60000, KEY = 'crmLastAct', last = Date.now(), wrote = 0, bar = null, gone = false;
        function shared() { try { return parseInt(localStorage.getItem(KEY), 10) || 0; } catch (e) { return 0; } }
        function mark(force) {
            last = Date.now();
            if (force || last - wrote > 5000) { wrote = last; try { localStorage.setItem(KEY, String(last)); } catch (e) {} }
            if (bar) { bar.remove(); bar = null; }
        }
        mark(true);
        $(document).on('mousemove keydown scroll click touchstart', function () { mark(false); });
        function warn(left) {
            if (!bar) {
                bar = document.createElement('div');
                bar.className = 'crm-idle';
                bar.setAttribute('role', 'alertdialog');
                bar.setAttribute('aria-live', 'assertive');
                bar.innerHTML = '<span>You will be signed out in <b></b> for inactivity.</span><button type="button" class="btn btn-primary btn-sm">Stay signed in</button>';
                bar.querySelector('button').addEventListener('click', function () { mark(true); });
                document.body.appendChild(bar);
            }
            bar.querySelector('b').textContent = Math.ceil(left / 1000) + ' s';
        }
        setInterval(function () {
            if (gone) return;
            if (window.crmIdleExempt) { mark(true); return; }
            var idle = Date.now() - Math.max(last, shared());
            if (idle >= MAX) {
                gone = true;
                window.location.href = '<?php echo base_url(); ?>manager/logout?timeout=1';
            } else if (idle >= MAX - WARN) {
                warn(MAX - idle);
            } else if (bar) {
                bar.remove(); bar = null; // another tab was used
            }
        }, 1000);
    })();

});
</script>

<style>.crm-idle{position:fixed;left:50%;bottom:18px;transform:translateX(-50%);z-index:3000;display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:center;max-width:calc(100% - 32px);padding:10px 14px;border-radius:10px;background:#0f172a;color:#fff;font-size:13.5px;box-shadow:0 10px 30px rgba(15,23,42,.3)}.crm-idle b{font-variant-numeric:tabular-nums}</style>
</body>
</html>
