<div class="wrap">
    <h2>Link Optimizer - Overview
    <a class="add-new-h2" href="<?php echo add_query_arg(array('page' => 'link-optimizer', 'action' => 'edit')); ?>">Edit Links</a>
    </h2>
    <div class="settings-errors">
    <?php settings_errors() ?>
    </div>


    <table class="wp-list-table widefat fixed">
        <thead>
            <tr style="vertical-align: top">
                <th><strong>Set Name</strong></th>
                <th>
                    <strong>Tag</strong>
                    <br>
                    <small>Include this in your posts, pages, or text widgets</small>
                </th>
                <th><strong>Short Link</strong></th>
                <th><strong>Links</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($ctx['sets'] as $set){ ?>
            <tr class="alternate">
                <td><?php echo $set['name']; ?></td>
                <td><code>[linkoptimizer:<?php echo $set['id']; ?>]</code></td>
                <td>
                    <a href="<?php echo site_url() . "/" . $set['slug']; ?>">
                        <?php echo site_url() . "/" . $set['slug']; ?>
                    </a>
                </td>
                <td class="link-count">
                    <?php echo count($set['links']); ?>
                    <a class="show-link-count btn"> [Show]</a>
                    <div class="links" style="display: none">
                        <hr>
                        <?php foreach($set['links'] as $link){ ?>
                        <a href="<?php echo $link_url ?>">
                            <?php echo $link['link_text']; ?>
                        </a>
                        
                        <br>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php } // foreach set in stes ?>
        </tbody>
        <tfoot>
            <tr>
                <th><strong>Set Name</strong></th>
                <th><strong>Tag</strong></th>
                <th><strong>Short Link</strong></th>
                <th><strong>Links</strong></th>
            </tr>
        </tfoot>
    </table>

        <p>
            <a href="http://refinry.com/affiliate-link-optimizer/">Upgrade to Link Optimizer</a> to get link cloaking, tracking, and <strong>automatic conversion optimization algorithm</strong>.
        </p>

        <p>Need some help? Try the <a href="http://refinry.com/affiliate-link-optimizer/user-guide/">User Guide</a>.</p>
</div>

<script type="text/javascript">
(function($){
    $(function(){
        $('a.show-link-count')
        .css('cursor', 'pointer')
        .click(function(){
            $(this).closest('.link-count').find('.links').toggle();
        });
    })
}(jQuery));
</script>