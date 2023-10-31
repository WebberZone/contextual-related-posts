jQuery(document).ready(function ($) {

    // we create a copy of the WP inline edit post function
    const wp_inline_edit = inlineEditPost.edit;

    // and then we overwrite the function with our own code
    inlineEditPost.edit = function (post_id) {

        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        wp_inline_edit.apply(this, arguments);

        // now we take care of our business

        // get the post ID from the argument
        if (typeof (post_id) == 'object') { // if it is object, get the ID number
            post_id = parseInt(this.getId(post_id));
        }

        if (post_id > 0) {
            // define the edit row
            const edit_row = $('#edit-' + post_id);
            const post_row = $('#post-' + post_id);

            // get the data
            const crp_manual_related = $('.crp_manual_related', post_row).text();
            const crp_exclude_this_post = 1 == $('.crp_exclude_this_post', post_row).text() ? true : false;

            // populate the data
            $(':input[name="crp_manual_related"]', edit_row).val(crp_manual_related);
            $(':input[name="crp_exclude_this_post"]', edit_row).prop('checked', crp_exclude_this_post);
        }
    };

    $('#bulk_edit').on('click', function () {
        const bulk_row = $('#bulk-edit');

        // get the selected post ids that are being edited
        const post_ids = [];

        // get the data
        const crp_manual_related = $(':input[name="crp_manual_related"]', bulk_row).val();
        const crp_exclude_this_post = $('select[name="crp_exclude_this_post"]', bulk_row).val();

        // get post ids from bulk_edit
        bulk_row.find('#bulk-titles-list .ntdelbutton').each(function () {
            post_ids.push($(this).attr('id').replace(/^(_)/i, ''));
        });
        // convert all post_ids to integer
        post_ids.map(function (value, index, array) {
            array[index] = parseInt(value);
        });

        // save the data
        $.ajax({
            url: ajaxurl, // this is a variable that WordPress has already defined for us
            type: 'POST',
            async: false,
            cache: false,
            data: {
                action: 'crp_save_bulk_edit', // this is the name of our WP AJAX function that we'll set up next
                post_ids: post_ids, // and these are the 2 parameters we're passing to our function
                crp_manual_related: crp_manual_related,
                crp_exclude_this_post: crp_exclude_this_post,
                crp_bulk_edit_nonce: crp_bulk_edit.nonce
            }
        });
    });

});
