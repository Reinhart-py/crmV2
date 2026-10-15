<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1> <?php echo $info['page_heading']; ?> </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="box-body">
                        <div class="alert alert-info">No reports found.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
