<div class="margin">
    <div class="row">
        <div class="col-lg-12">
            <section class="panel light">
                <div class="wrapper">
                    <form action="/admin/categories/edit/<?=$aCategory->getId(); ?>">
                    <div class="heading"><input type="text" id="name" value="<?= $aCategory->getName(); ?>"></div>
                    <!-- Start description -->
                    <div class="row m-t">
                        <div class="col-lg-6 col-md-6 ">
                            <p><textarea name="description" id="description" cols="30" rows="10"><?= $aCategory->getDescription(); ?></textarea></p>
                        </div><!-- /.end description column -->
                        <div class="col-lg-6 col-md-6"></div>
                    </div><!--/.row -->
                        <input type="submit"  value="Submit" class="button button-info">
                    </form>
                </div><!-- /.wrapper -->
            </section><!-- /.panel -->
        </div><!-- /.fullwidth -->
    </div><!-- /.row -->
</div><!-- /.margin -->
<div class="app-trigger" id="_Admin" rel='<?= json_encode(array('p' => 'viewcategory', 'sessionID' => session_id(), 'iCategory' => $aCategory->getId())); ?>'></div>
