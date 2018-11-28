<div class="margin">
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">

                <!-- Button trigger modal -->
                <a href="#" data-toggle="modal" data-target="#modalNewSupplier">
                    <img src="/images/add-admin.svg" alt="Add new item" class="pull-right margin" height="50" width="50"/>
                </a>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a aria-controls="activeSupplier" role="tab" data-toggle="tab" href="#activeSupplier">Published Suppliers</a>
                    </li>
                    <li role="presentation">
                        <a aria-controls="InactiveSupplier" role="tab" data-toggle="tab" href="#InactiveSupplier">Unpublished Suppliers</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="activeSupplier">
                            <table id="activeSupplierTable" class="table table-hover table-responsive m-t">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php  foreach ($activeSuppliers as $oSupplier) : ?>
                                    <tr id="tr_<?=$oSupplier->getId();?>">
                                        <td>
                                            <a href="javascript:;" class="editSupplier" id="<?=$oSupplier->getId(); ?>">
                                                <?= $oSupplier->getName(); ?>
                                            </a>
                                        </td>
                                        <td ><?= $oSupplier->getDescription(); ?></td>
                                        <td>
                                            <a style="cursor: pointer;" class="changeSupplierStatus active"  data-id="<?=$oSupplier->getId(); ?>">
                                                <i class="fa fa-times fa-2x"></i>
                                            </a>
                                            <div class="hidden" id="hidden_<?=$oSupplier->getId();?>">
                                                <input type="hidden" name="Id" value="<?= $oSupplier->getId(); ?>">
                                                <input type="hidden" name="Name" value="<?= $oSupplier->getName(); ?>">
                                                <input type="hidden" name="Description" value="<?= $oSupplier->getDescription(); ?>">

                                            </div>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="InactiveSupplier">
                            <table id="InactiveSupplierTable" class="table table-hover table-responsive m-t">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php  foreach ($inActiveSuppliers as $oSupplier) : ?>
                                    <tr id="tr_<?=$oSupplier->getId();?>">
                                        <td>
                                            <a href="javascript:;" class="editSupplier" id="<?=$oSupplier->getId(); ?>">
                                                <?= $oSupplier->getName(); ?>
                                            </a>
                                        </td>
                                        <td ><?= $oSupplier->getDescription(); ?></td>
                                        <td>
                                            <a style="cursor: pointer;" class="changeSupplierStatus"  data-id="<?=$oSupplier->getId(); ?>">
                                                <i class="fa fa-check fa-2x"></i>
                                            </a>
                                            <div class="hidden" id="hidden_<?=$oSupplier->getId();?>">
                                                <input type="hidden" name="Id" value="<?= $oSupplier->getId(); ?>">
                                                <input type="hidden" name="Name" value="<?= $oSupplier->getName(); ?>">
                                                <input type="hidden" name="Description" value="<?= $oSupplier->getDescription(); ?>">

                                            </div>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section><!-- /.panel -->
        </div><!-- /.fullwidth -->
    </div><!-- /.row -->
</div><!-- /.margin -->

<!-- Modal -->
<div class="modal fade" id="modalNewSupplier" tabindex="-1" role="dialog" aria-labelledby="New Supplier" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="title">Add new Supplier</div>
            </div><!-- End modal header -->
            <div class="wrapper margin">
                <form id="newSuppliers">
                    <input type="hidden" name="prefix" value="p">
                    <div class="form-group">
                        <label for="pName">Name</label> <input class="form-control" id="pName" name="Name" placeholder="Enter name" type="text">

                        <div class="error-notify"></div>
                    </div><!-- End group -->


                    <div class="form-group">
                        <label for="pDescription">Description</label> <textarea class="form-control" id="pDescription" name="Description" placeholder="Enter description" rows="3"></textarea>

                        <div class="error-notify"></div>
                    </div><!-- End group -->


                    <button class="btn btn-default" type="submit" id="submitNewSupplier">Save</button>
                    <input type="hidden" name="returnURL" value="/admin/suppliers" />
                </form><!-- / end form -->
            </div><!-- /.wrapper -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="modalEditSupplier" tabindex="-1" role="dialog" aria-labelledby="Edit Supplier" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="title">Edit Item</div>
            </div><!-- End modal header -->
            <div class="wrapper margin">
                <form id="editSupplier">
                    <input type="hidden" name="iSupplier" id="Id">
                    <div class="form-group">
                        <label for="Title">Name</label> <input class="form-control" id="Name" name="Name" placeholder="Enter name" type="text">
                        <div class="error-notify"></div>
                    </div><!-- End group -->



                    <div class="form-group">
                        <label for="Description">Description</label> <textarea class="form-control" id="Description" name="Description" placeholder="Enter description" rows="3"></textarea>
                        <div class="error-notify"></div>
                    </div><!-- End group -->


                    <button class="btn btn-default" type="submit" id="submitEditSupplier">Save</button>
                    <input type="hidden" name="returnURL" value="/admin/suppliers" />
                </form><!-- / end form -->
            </div><!-- /.wrapper -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    var activeSuppliers = '<?=( isset($activeSuppliers) && count($activeSuppliers) > 0 ) ? true : false ?>';
    var inactiveSuppliers = '<?=( isset($inActiveSuppliers) && count($inActiveSuppliers) > 0 ) ? true : false ?>';

        $('#activeSupplierTable').DataTable({
            "bInfo"    : false,
            "aaSorting": [],
            "columnDefs": [
                { "orderable": false, "targets": 2 }
            ]
        });
        $('#activeSupplierTable_wrapper').find('div').eq(0).css('padding', '30px 15px');

        $('#InactiveSupplierTable').DataTable({
            "bInfo" : false,
            "aaSorting": [],
            "columnDefs": [
                { "orderable": false, "targets": 2 }
            ]
        });
        $('#InactiveSupplierTable_wrapper').find('div').eq(0).css('padding', '30px 15px');
</script>