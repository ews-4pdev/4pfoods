                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                                <table id="cron_table" class="table table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Outcome</th>
                                            <th>Error</th>
                                            <th>Result</th>
                                            <th>Timestamp</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($aLogs as $oLog) : ?>
                                        <tr>
                                            <td><?= $oLog->getTask(); ?></td>
                                            <td><?= ($oLog->getIsSuccess()) ? 'Success' : 'Fail'; ?></td>
                                            <td><?= $oLog->getErrorCode(); ?></td>
                                            <td><?= $oLog->getPrintout(); ?></td>
                                            <td><?= $oLog->getCreatedAt('m-d-Y H:i:s'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
							</section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                </div><!-- /.margin -->