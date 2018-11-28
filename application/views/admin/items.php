<link href='//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css' rel='stylesheet'/>
<script src='//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js'></script>
<?php $temp = [];
$points_array = []; ?>
<div class='margin'>
    <div class='row'>
        <div class='col-lg-12'>
            <section class='panel'>

                <!-- Button trigger modal -->
                <a href='#' data-toggle='modal' data-target='#modalNewItem'>
                    <img src='/images/add-admin.svg' alt='Add new item' class='pull-right margin' height='50'
                         width='50'/>
                </a>

                <!-- Nav tabs -->
                <ul class='nav nav-tabs' role='tablist'>
                    <li role='presentation' class='active'>
                        <a aria-controls='activeItems' role='tab' data-toggle='tab' href='#activeItems' id="activeItem">Published
                            Items</a>
                    </li>
                    <li role='presentation'>
                        <a aria-controls='InactiveItems' role='tab' data-toggle='tab' href='#InactiveItems' id="InactiveItem">Unpublished
                            Items</a>
                    </li>
                </ul>

                <div class='tab-content'>
                    <div role='tabpanel' class='tab-pane active' id='activeItems'>
                        <table id="activeItemsTable" class='table table-hover table-responsive m-t'>
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Suppliers</th>
                                <th>Points</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($aItems as $oItem) : $points = null;
                                $supplier_name = null; ?>
                                <?php if ($oItem->getActive() == true): ?>
                                    <tr id='tr_<?= $oItem->getId(); ?>'>
                                        <td class="<?= ($oItem->getSecondary() == 1) ? 'gray' : ''; ?>">
                                            <a href='javascript:;' class='editItem' id='<?= $oItem->getId(); ?>'>
                                                <?= strtoupper($oItem->getName()); ?>
                                            </a>
                                        </td>
                                        <td><?= $oItem->getProductCategory()->getTitle(); ?></td>
                                        <td>
                                            <?php
                                            foreach ($oItem->getItemSuppliers() as $itemSupplier) {
                                                $temp[$oItem->getId()][] = $itemSupplier->getSupplierId();
                                                $supplier_name = (empty($supplier_name)) ? $itemSupplier->getSuppliers()->getName() : $itemSupplier->getSuppliers()->getName() . ',' . $supplier_name;
                                            }
                                            echo $supplier_name;
                                            ?>
                                        </td>
                                        <td style="width: 25%">
                                            <?php foreach ($oItem->getItemsPoints() as $key) :

                                                $points_array[$oItem->getId()][$key->getProduct()->getTitle()] = [
                                                    'point' => $key->getPoints(), 'id' => $key->getProductId(), 'title' => $key->getProduct()->getTitle(), 'size' => $key->getProduct()->getSize()
                                                ];
                                                $tt = '<div class="">' . strtoupper($key->getProduct()->getTitle()) . '&nbsp;<span class="badge">(' . $key->getPoints() . ')</span></div>';
                                                $points = (empty($points)) ? $tt : $tt . $points . '<br>';
                                            endforeach;

                                            ?>
                                            <?php echo $points; ?>
                                        </td>
                                        <td>
                                            <a href="javascript:;" class='changeItemStatus'
                                               data-href='/admin/changeItemStatus/' id="<?= $oItem->getId(); ?>">
                                                <i class='fa fa-times fa-2x'></i>
                                            </a>

                                            <div class='hidden' id='hidden_<?= $oItem->getId(); ?>'>
                                                <input type='hidden' name='Id' value='<?= $oItem->getId(); ?>'>
                                                <input type='hidden' name='Name' value='<?= $oItem->getName(); ?>'>
                                                <input type='hidden' name='CategoryName'
                                                       value='<?= $oItem->getProductCategory()->getTitle(); ?>'>
                                                <input type='hidden' name='CategoryId'
                                                       value='<?= $oItem->getCategoryId(); ?>'>
                                                <input type="hidden" name="Description"
                                                       value="<?= $oItem->getDescription(); ?>">
                                                <input type="hidden" name="Secondary"
                                                       value="<?php $t = $oItem->getSecondary();
                                                       echo $t; ?>">
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div role='tabpanel' class='tab-pane' id='InactiveItems'>
                        <table id="InactiveItemsTable" class='table table-hover table-responsive m-t'>
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Suppliers</th>
                                <th>Points</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($aItems as $oItem) : $points = null;
                                $supplier_name = null; ?>
                                <?php if ($oItem->getActive() == false): ?>
                                    <tr id='tr_<?= $oItem->getId(); ?>'>
                                        <td class="<?= ($oItem->getSecondary() == 1) ? 'gray' : ''; ?>">
                                            <a href='javascript:;' class='editItem' id='<?= $oItem->getId(); ?>'>
                                                <?= strtoupper($oItem->getName()); ?>
                                            </a>
                                        </td>
                                        <td><?= $oItem->getProductCategory()->getTitle(); ?></td>
                                        <td>
                                            <?php
                                            foreach ($oItem->getItemSuppliers() as $itemSupplier) {
                                                $temp[$oItem->getId()][] = $itemSupplier->getSupplierId();
                                                $supplier_name = (empty($supplier_name)) ? $itemSupplier->getSuppliers()->getName() : $itemSupplier->getSuppliers()->getName() . ',' . $supplier_name;
                                            }
                                            echo $supplier_name;
                                            ?>
                                        </td>
                                        <td style="width: 25%">
                                            <?php foreach ($oItem->getItemsPoints() as $key) :

                                                $points_array[$oItem->getId()][$key->getProduct()->getTitle()] = [
                                                    'point' => $key->getPoints(), 'id' => $key->getProductId(), 'title' => $key->getProduct()->getTitle(), 'size' => $key->getProduct()->getSize()
                                                ];
                                                $tt = '<div class="">' . strtoupper($key->getProduct()->getTitle()) . '&nbsp;<span class="badge">(' . $key->getPoints() . ')</span></div>';
                                                $points = (empty($points)) ? $tt : $tt . $points . '<br>';
                                            endforeach;
                                            ?>
                                            <?php echo $points; ?>
                                        </td>
                                        <td>
                                            <a href="javascript:;" class='changeItemStatus'
                                               data-href='/admin/changeItemStatus/' id="<?= $oItem->getId(); ?>">
                                                <i class='fa fa-check fa-2x'></i>
                                            </a>

                                            <div class='hidden' id='hidden_<?= $oItem->getId(); ?>'>
                                                <input type='hidden' name='iItem' value='<?= $oItem->getId(); ?>'>
                                                <input type='hidden' name='Name' value='<?= $oItem->getName(); ?>'>
                                                <input type='hidden' name='CategoryName'
                                                       value='<?= $oItem->getProductCategory()->getTitle(); ?>'>
                                                <input type='hidden' name='CategoryId'
                                                       value='<?= $oItem->getCategoryId(); ?>'>
                                                <input type="hidden" name="Description"
                                                       value="<?= $oItem->getDescription(); ?>">
                                                <input type="hidden" name="Secondary"
                                                       value="<?php $t = $oItem->getSecondary();
                                                       echo $t; ?>">
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
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
<div class='modal fade' id='modalNewItem' tabindex='-1' role='dialog' aria-labelledby='New Item' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <div class='title'>Add new item</div>
            </div><!-- End modal header -->
            <div class='wrapper margin'>
                <div class="row">
                    <div class="newItemsError alert alert-danger">

                    </div>
                </div>
                <form id='newItems'>
                    <div class="row">
                        <div class='always-available'>
                            <div class=''>
                                <label for='pSecondary'>Always Available</label>
                                <input id="pSecondary" name="Secondary" type="checkbox"/>

                                <div class='error-notify'></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->
                    </div>
                    <div class='row'>
                        <div class='form-group'>
                            <label for='pName'>Name</label> <input class='form-control' id='pName' name='Name'
                                                                    placeholder='Enter name' type='text'>

                            <div class='error-notify'></div>
                        </div><!-- End group -->
                    </div>

                    <div class='row'>
                        <div class='pull-left'>
                            <div class='form-group'>
                                <label style='width: 100%' for='suppliers'>Suppliers</label>
                                <select style='width: 100%' multiple='multiple' class='form-control' name='suppliers[]'
                                        id='suppliers'>
                                    <?= createDropDownOptions($aSuppliers); ?>
                                </select>

                                <div class='error-notify'></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->
                    </div><!-- /.row -->

                    <div class='row'>
                        <div class='pull-left'>
                            <div class='form-group'>
                                <label for='pCategory'>Category</label>
                                <select class='form-control productCategory' name='CategoryId' id='pCategory'>
                                    <option selected="selected">Select Category</option>
                                    <?= createDropDownOptions($aCategories); ?>
                                </select>

                                <div class='error-notify'></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->

                    </div><!-- /.row -->


                    <div class='row'>
                        <div class='pull-left'>
                            <div class='form-group' id="sizeDiv">

                            </div><!-- End group -->
                        </div><!-- /. half -->

                    </div><!-- /.row -->
                    <div class="row">
                        <div class='form-group m-t-md'>
                            <label for='pDescription'>Description</label> <textarea class='form-control'
                                                                                    id='pDescription' name='Description'
                                                                                    placeholder='Enter description'
                                                                                    rows='3'></textarea>

                            <div class='error-notify'></div>
                        </div><!-- End group -->
                    </div>
                    <button class='btn btn-default' type='submit' id='submitNewItem'>Save</button>
                    <input type='hidden' name='returnURL' value='/admin/items'/>
                </form><!-- / end form -->
            </div><!-- /.wrapper -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class='modal fade' id='modalEditItem' tabindex='-1' role='dialog' aria-labelledby='Edit Site' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <div class='title'>Edit Item</div>
            </div><!-- End modal header -->
            <div class='wrapper margin'>
                <div class="row">
                    <div class="editItemsError alert alert-danger">

                    </div>
                </div>
                <form id='editItems'>
                    <input type='hidden' name='iItem' id='Id'>

                    <div class="row">
                        <div class='always-available'>
                            <div class=''>
                                <label for='Secondary'>Always Available</label>
                                <input id="Secondary" name="Secondary" type="checkbox"/>

                                <div class='error-notify'></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->
                    </div>
                    <div class="row">
                        <div class='form-group'>
                            <label for='Title'>Name</label> <input class='form-control' id='Name' name='Name'
                                                                    placeholder='Enter name' type='text'>

                            <div class='error-notify'></div>
                        </div><!-- End group -->
                    </div>


                    <div class='row'>
                        <div class='pull-left'>
                            <div class='form-group'>
                                <label style='width: 100%' for='suppliers_edit'>Suppliers</label>
                                <select style='width: 100%' multiple='multiple' class='form-control' name='suppliers[]'
                                        id='suppliers_edit'>
                                    <?= createDropDownOptions($aSuppliers); ?>
                                </select>

                                <div class='error-notify'></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->

                    </div><!-- /.row -->

                    <div class='row'>
                        <div class='pull-left'>
                            <div class='form-group'>
                                <label for='CategoryName'>Category</label>
                                <input readonly="readonly" type="text" class='form-control' name='CategoryName' id='CategoryName'>
                                <input type="hidden" class='form-control' name='CategoryId' id='CategoryId'>
                                <div class='error-notify'></div>
                            </div><!-- End group -->
                        </div><!-- /. half -->

                    </div><!-- /.row -->

                    <div class='row'>
                        <div class='pull-left'>
                            <div class='form-group' id="editSizeDiv">
                            </div><!-- End group -->
                        </div><!-- /. half -->

                    </div><!-- /.row -->
                    <div class="row">
                        <div class='form-group m-t-md'>
                            <label for='Description'>Description</label> <textarea class='form-control' id='Description'
                                                                                   name='Description'
                                                                                   placeholder='Enter description'
                                                                                   rows='3'></textarea>

                            <div class='error-notify'></div>
                        </div><!-- End group -->
                    </div>


                    <button class='btn btn-default' type='submit' id='submitEditItem'>Save</button>
                    <input type='hidden' name='returnURL' value='/admin/items'/>
                </form><!-- / end form -->
            </div><!-- /.wrapper -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type='text/javascript'>
    var suppliers_name;
    var points_array;
    $('.newItemsError').hide();
    $('.editItemsError').hide();
    $(document).ready(function () {
        $('#activeItemsTable').DataTable({
            "bInfo": false,
            "aaSorting": [],
            "columnDefs": [
                {"orderable": false, "targets": 3},
                {"orderable": false, "targets": 4}
            ]
        });
        $('#InactiveItemsTable').DataTable({
            "bInfo": false,
            "aaSorting": [],
            "columnDefs": [
                {"orderable": false, "targets": 3},
                {"orderable": false, "targets": 4}
            ]
        });

        suppliers_name = <?=json_encode($temp)?>;
        points_array = <?=json_encode($points_array)?>;

        $('#items-table_wrapper').find('div').eq(0).css('padding', '30px 15px')

    });

    $('.editItem').on('click', function () {
        var id = $(this).attr('id');
        var data = getAllTds( id );
        // Set Bag Type array into data
        var point = points_array[id];
        var str = '';
        for (var key in point) {
            if (point.hasOwnProperty(key)) {
                str += " <label class='text-capitalize' for='pPrice'>" + point[key].title + "<input class='form-control size' name='size[" + point[key].id + "]' value='" + point[key].point + "' type='text'> <div class='error-notify'></div> </label>";
            }
        }
        $('#editSizeDiv').html(str);
        fillForm('editItems', data);

        // Set Suppliers data
        var suppliers_options = $('#suppliers_edit');
        suppliers_options.select2({
            placeholder: 'Select Supplier From List'
        });
        suppliers_options.val(suppliers_name[data['Id']]).trigger('change');
        $('#modalEditItem').modal('show');
    });
</script>