<div class="margin">
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">

                <!-- Button trigger modal -->
                    <a href="#" data-toggle="modal" data-target="#modalNewProduct">
                        <img src="/images/add-admin.svg" alt="Add new product" class="margin pull-right" height="50" width="50"/>
                    </a>

                <div class="clearboth">
                    <table id="productsTable" class="table table-hover table-responsive m-t">
                    <thead>
                    <tr>
                        <th>Bag</th>
                        <th>Category</th>
                        <th>Box Type</th>
                        <th>Points</th>
                        <th>Price</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($aProducts as $oProduct) : ?>
                        <tr>
                            <td>
                                <?= $oProduct['Title']; ?>
                            </td>
                            <td><?= $oProduct['ProductCategory']['Title']; ?></td>
                            <td><?= $oProduct['Size']; ?></td>
                            <td><?= $oProduct['Points']; ?></td>
                            <td><?= $oProduct['Price']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
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
                <div class="title">Add new product</div>
            </div><!-- End modal header -->
            <div class="wrapper margin">
                <form id="newProducts">

                    <div class="form-group">
                        <label for="Title">Title</label> <input class="form-control" id="pTitle" name="Title"
                                                                placeholder="Enter name" type="text">
                        <div class="error-notify"></div>
                    </div><!-- End group -->
                    <input type="hidden" name="prefix" value="p">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="CategoryId">Category</label>
                                <select class="form-control" id="pCategoryId" name="CategoryId">
                                    <option value="" selected="selected">Select Category</option>
                                    <?= createDropDownOptions($aCategories); ?>
                                </select>

                                <div class="error-notify"></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Size">Size</label>
                                <select class="form-control" id="pSize" name="Size">
                                    <option value="" selected="selected">Select Size</option>
                                    <?php foreach ($aSizes as $aSize) : ?>
                                        <option value="<?= $aSize; ?>"><?= $aSize; ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="error-notify"></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->
                    </div><!-- /.row -->

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Point">Points</label> <input class="form-control" name="Points" id="pPoints"
                                                                         placeholder="Enter Points" type="number">
                                <div class="error-notify"></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->

                    </div><!-- /.row -->

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Point">Price</label> <input class="form-control" name="Price" id="pPrice"
                                                                         placeholder="Enter Points" type="number">
                                <div class="error-notify"></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->

                    </div><!-- /.row -->

                    <div class="form-group">
                        <label for="Description">Description</label> <textarea class="form-control" id="pDescription"
                                                                               name="Description"
                                                                               placeholder="Enter description"
                                                                               rows="3"></textarea>

                        <div class="error-notify"></div>
                    </div><!-- End group -->


                    <button class="btn btn-default" type="submit" id="submitNewProduct">Save</button>
                    <input type="hidden" name="returnURL" value="/admin/products"/>
                </form><!-- / end form -->
            </div><!-- /.wrapper -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="app-trigger" id="_Admin"
     rel='<?= json_encode(array('p' => 'products', 'sessionID' => session_id())); ?>'></div>
<script type="text/javascript">
    $('#productsTable').DataTable({
        "bInfo": false
    });
</script>