            		</section><!-- /.scrollable-->
            	</section><!-- /.vbox -->
            </section><!-- /.screenBox -->
        </section><!-- /#adminContent -->
        <!-- FOR ROBIN - the main content section ends here -->
    </section><!-- /.screenBox -->
</div><!-- /.admin -->
<?php if (isset($jsApp)) : ?>
  <div class="app-trigger" id="_<?= $jsApp['title']; ?>" rel='<?= json_encode($jsApp['data']); ?>'></div>
<?php endif; ?>