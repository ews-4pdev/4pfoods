<div class="margin">
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <!-- Button trigger modal -->
                <a href="#" data-toggle="modal" data-target="#modalNewProduct">
                    <img src="/images/add-admin.svg" alt="Add new product" class="pull-right margin" height="50" width="50"/>
                </a>

                <!-- Nav tabs -->

                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a aria-controls="activeCategories" role="tab" data-toggle="tab" href="#activeCategories">Active Categories</a>
                    </li>
                    <li role="presentation">
                        <a aria-controls="InactiveCategories" role="tab" data-toggle="tab" href="#InactiveCategories">Inactive Categories</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="activeCategories">
                        <table class="table table-hover table-responsive">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            foreach ( $aCategories as $oCategory) :
                                if($oCategory->getIsPublished()) {
                                ?>
                                <tr>
                                    <td><?= $oCategory->getTitle(); ?></td>
                                    <td><?= $oCategory->getDescription(); ?></td>
                                </tr>
                            <?php } endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="InactiveCategories">
                        <table class="table table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                foreach ( $aCategories as $oCategory) :
                                    if(!$oCategory->getIsPublished()) {
                                    ?>
                                    <tr>
                                        <td><?= $oCategory->getTitle(); ?></td>
                                        <td><?= $oCategory->getDescription(); ?></td>
                                    </tr>
                                <?php } endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section><!-- /.panel -->
        </div><!-- /.fullwidth -->
    </div><!-- /.row -->
</div><!-- /.margin -->

<!-- Modal -->
<div class="modal fade" id="modalNewProduct" tabindex="-1" role="dialog" aria-labelledby="New Site" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="title">Add new Category</div>
            </div><!-- End modal header -->
            <div class="wrapper margin">
                <form id="newCategories">

                    <div class="form-group">
                        <label for="Title">Title</label> <input class="form-control" id="pTitle" name="title" placeholder="Enter title" type="text">

                        <div class="error-notify"></div>
                    </div><!-- End group -->

                    <div class="form-group">
                        <label for="Description">Description</label> <textarea class="form-control" id="pDescription" name="description" placeholder="Enter description" rows="3"></textarea>

                        <div class="error-notify"></div>
                    </div><!-- End group -->


                    <button class="btn btn-default" id="submitNewCategory" type="submit">Add new category</button>
                    <input type="hidden" name="returnURL" value="/admin/categories/" />
                </form><!-- / end form -->
            </div><!-- /.wrapper -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
