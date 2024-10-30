<style>
table.lo-set-links th{
    text-align: left;
}
table.lo-set-links th,
table.lo-set-links td {
    padding: 5px;
}
.lo-postbox fieldset{
    clear: both;
}

.lo-postbox label{
    font-weight: bold;
}
.lo-postbox label, .lo-postbox .inner, .lo-postbox .field input{
    display: block;
}
.lo-postbox fieldset .field{
    float: left;
    margin: 1em 1em 0 0;
}
.lo-postbox h3 a{
    font-size: inherit;
}
</style>

<?php $__permalinks_enabled = !!get_option('permalink_structure'); ?>

<?php if($__permalinks_enabled){ ?>
<script>window.permalinks_enabled = true;</script>
<?php } ?>

<div class="wrap">
    <h2>Link Optimizer Lite - Edit</h2>
    <p>Configure your link sets here</p>

    <div class="settings-errors">
    <?php settings_errors() ?>
    </div>

    <div id="lo-sets"></div>

    <a id="lo-add-set" class="button-secondary">+ Add Set</a>

    <form action="" method="POST" id="lo-form">
        <input type="hidden" name="action" value="LinkOptimizer::save">
        <input type="hidden" name="serialized-data" value="">
        <p class="submit">
            <input type="submit" value="Save" class="button-primary">
        </p>

        <p>
            <a href="http://refinry.com/affiliate-link-optimizer/">Upgrade to Link Optimizer</a> to get link cloaking, tracking, and <strong>automatic conversion optimization algorithm</strong>.
        </p>

        <p>Need some help? Try the <a href="http://refinry.com/affiliate-link-optimizer/user-guide/">User Guide</a>.</p>
    </form>

    <script type="application/json" id="lo-serialized-data" style="display: none;"><?php echo $serialized; ?></script>

    <div id="lo-set-prototype" class="set" style="display: none;">
        <input type="hidden" class="lo-set-id">
        <fieldset class="postbox lo-postbox">
            <div class="inside">
                <h3 class="hndle">
                    <span class="lo-set-name"></span>
                    <?php if($__permalinks_enabled){ ?>
                    (<a class="url"><?php echo site_url(); ?>/<span class="lo-set-slug"></span></a>)
                    <?php } ?>
                </h3>
                <fieldset>
                    <div class="field"><label>Name</label>
                    <input type="text" class="lo-set-name">
                    </div>
                    <div class="field">
                        <label>Tag</label>
                        <input type="text" readonly="readonly" class="lo-set-tag-display">
                        <div class="inner">Copy and paste this tag where you want your link in a post, page, or text widget.</div>
                    </div>
                </fieldset>
                <fieldset class="short-link">
                    <div class="field"><label>Short Link</label>
                    <?php if($__permalinks_enabled){ ?>
                    <span><?php echo site_url(); ?>/<input style="display: inline" type="text" class="lo-set-slug"></span>
                    <div class="inner">May contain upper- and lower-case characters, numbers, dashes (-), and underscores (_)</div>
</p>
                    <?php } else { ?>
                        <p>Please choose a permalink scheme under Settings&raquo;Permalinks to enable
short links</p>
                    <?php } ?>
                </fieldset>

                <h4>Links</h4>
                <table class="lo-set-links">
                    <thead>
                        <tr>
                            <th>Link Text</th>
                            <th>URL</th>
                            <th>Link Title</th>

                            <th></th>
                        </tr>
                    </thead>
                </table>
                <p style="float: right">
                    <a class="lo-delete-set button-secondary" style="color: #c00; font-weight: bold;">Delete Set</a>
                </p>
                <p>
                    <a class="lo-add-link button-primary">+ Add Link</a>
                </p>
            </div>
        </fieldset>
    </div>


    <table id="lo-link-prototype" style="display: none;">
        <tr class="link">
            <td class="lo-link-text-td">
                <input type="hidden" class="lo-link-id">
                <input type="hidden" class="lo-link-set-id">
                <input type="text" class="lo-link-text">
            </td>
            <td class="lo-link-url-td">
                <input type="text" class="lo-link-url">
            </td>
            <td class="lo-link-title-td">
                <input type="text" class="lo-link-title">
            </td>

            <td>
                <a class="lo-delete-link button-secondary">Delete</a>
            </td>
        </tr>
    </table>


    <script type="text/javascript">
    (function($){
        $(function(){
            var render, render_set, render_link;
            var data = JSON.parse($('#lo-serialized-data').html());

            var set_prototype = $('#lo-set-prototype').clone().attr('id', '').show();
            var link_prototype = $('#lo-link-prototype tr').clone().attr('id', '').show();

            var clean_set = function(set){
                set.links = set.links || [];
                set.name = set.name || "New Set";
                return set;
            };

            var add_link = function(set, set_el){
                return function(){
                    var link = {};
                    set.links.push(link);
                    $('.lo-set-links', set_el).append(render_link(set, link));
                };
            };

            var add_set = function(data, container_el){
                return function(){
                    var set = clean_set({});
                    data.sets.push(set);
                    container_el.append(render_set(data, set));
                }
            }

            var delete_link = function(link_el){
                return function(){
                    link_el.remove();
                }
            }

            var delete_set = function(set_el){
                return function(){
                    set_el.remove();
                }
            }

            render_link = function(set, link){
                var el = link_prototype.clone();
                el.data('deleted', false);
                $('input.lo-link-set-id', el).val(set.id);
                $('input.lo-link-id', el).val(link.id);
                $('input.lo-link-text', el).val(link.link_text);
                $('input.lo-link-url', el).val(link.link_url);
                $('input.lo-link-title', el).val(link.link_title);



                $('.lo-delete-link', el).click(delete_link(el));

                return el;
            };

            render_set = function(data, set){
                var ii, el = set_prototype.clone();

                if(set.id){
                    $('input.lo-set-tag-display', el).val('[linkoptimizer:' + set.id + ']');
                }else{
                    $('input.lo-set-tag-display', el).remove();
                }

                $('select.lo-rotation-strategy', el).val(set.rotation_strategy);

                $('span.lo-set-name', el).text(set.name);
                $('input.lo-set-id', el).val(set.id);
                $('input.lo-set-name', el).val(set.name);
                if(window.permalinks_enabled){
                    $('h3 a', el).attr('href', $('h3 a', el).text() + set.slug);
                    $('span.lo-set-slug', el).text(set.slug);
                    $('input.lo-set-slug', el).val(set.slug);
                }

                for(ii = 0; ii < ((set.links || {}).length || 0); ii++){
                    $('.lo-set-links', el).append(render_link(set, set.links[ii]));
                }


                $('.lo-add-link', el).click(add_link(set, el));
                $('.lo-delete-set', el).click(delete_set(el));

                return el;
            };

            render = function(data){
                var ii, jj;
                $('#lo-sets').empty();
                for(ii=0; ii<data['sets'].length; ii++){
                    $('#lo-sets').append(
                        render_set(data, clean_set(data['sets'][ii])));
                }

            };

            var link_to_json = function(){
                var el = $(this),
                    result = {
                    'id': $('.lo-link-id', el).val(),
                    'set_id': $('.lo-link-set-id', el).val(),
                    'link_text': $('.lo-link-text', el).val(),
                    'link_url': $('.lo-link-url', el).val(),
                    'link_title': $('.lo-link-title', el).val()
                };
                return result;
            };

            var set_to_json = function(){
                var el = $(this),
                    result = {
                    'id': $('input.lo-set-id', el).val(),
                    'name': $('input.lo-set-name', el).val(),
                    'rotation_strategy': $('select.lo-rotation-strategy', el).val(),
                    'links': $('.lo-set-links .link', el).map(link_to_json).get()
                };
                if(window.permalinks_enabled){
                    result.slug = $('input.lo-set-slug', el).val();
                }
                return result;
            };

            var data_to_json = function(){
                return {
                    'sets': $('#lo-sets .set').map(set_to_json).get()
                };
            };

            var validate_set = function(result){
                return function(){
                    var el = $(this);
                    $('.error', el).remove();

                    // Ensure short_link is valid
                    var short_link = $('input.lo-set-slug', el).val();
                    if(!short_link.match(/^[a-zA-Z0-9_\-]*$/)){
                        result.all_valid = false;
                        $('input.lo-set-slug', el).parent().append(
                            $('<div class="error">Invalid characters</div>')
                        );
                    }
                }
            }

            var validate = function(){
                var result = {all_valid: true};
                $('.settings-errors .error').remove();
                if(window.permalinks_enabled){
                    $('#lo-sets .set').each(validate_set(result));
                }
                if(!result.all_valid){
                    $('.settings-errors').append(
                        $('<div class="error">Could not save. Please correct any errors, then save again.</div>')
                    );
                }
                return result.all_valid;

            }


            $('#lo-add-set').click(add_set(data, $('#lo-sets')));
            render(data);

            $('#lo-form').submit(function(e){
                if(!validate()){
                    e.preventDefault();
                    return false;
                }
                var data = data_to_json();
                $('#lo-form input[name=serialized-data]').val(JSON.stringify(data));
                return true;
            });
        });
    }(jQuery));
    </script>
</div>
