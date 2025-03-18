jQuery(document).ready(function ($) {
    $.fn.crpAutocompletePosts = function (options) {
        var cache;
        var last;
        var $element = $(this);
        var postid = $('#post_ID').val();

        options = options || {};

        // Define the default options
        const defaults = {
            ajaxAction: 'crp_get_posts_action',
            postList: '#crp-post-list',
            hiddenField: '#crp-manual-related-csv',
            relevance: 1,
        };

        // Get the AJAX action and delete it from the options object
        const ajaxAction = options.ajaxAction || $element.attr('data-wp-ajax-action') || defaults.ajaxAction;
        delete (options.ajaxAction);

        // Define the input field and the list where the posts will be added
        const inputField = $element;
        const postList = options.postList ? $(options.postList) : $(defaults.postList);
        delete (options.postList);
        const hiddenField = options.hiddenField ? $(options.hiddenField) : $(defaults.hiddenField);
        delete (options.hiddenField);
        const relevance = options.relevance || $element.attr('data-wp-relevance') || defaults.relevance;

        // Get the post IDs from the hidden field
        function getManualRelatedIDs() {
            const manualRelatedIDs = [];

            // Get the post IDs from the post list
            postList.children('li').each(function () {
                const manualRelatedId = $(this).attr('class').replace('widefat post-', '');
                manualRelatedIDs.push(manualRelatedId);
            });

            return manualRelatedIDs;
        }

        // Update the hidden field with the current order of posts
        function updateHiddenField() {
            const manualRelatedIDs = getManualRelatedIDs();
            hiddenField.val(manualRelatedIDs.join(','));
        }

        // Initialize sortable functionality
        postList.sortable({
            placeholder: 'crp-sortable-placeholder',
            opacity: 0.7,
            cursor: 'move',
            update: function() {
                updateHiddenField();
            }
        }).disableSelection();

        // Initialize the autocomplete plugin
        options = $.extend({
            position: {
                my: 'left top+2',
                at: 'left bottom',
                collision: 'none'
            },
            source: function (request, response) {
                // Return cached results if they exist
                if (last === request.term) {
                    response(cache);
                    return;
                }

                // Make an AJAX request to get the posts
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        action: ajaxAction,
                        search_term: request.term,
                        crp_get_posts_nonce: crp_metabox.nonce,
                        postid: postid,
                        exclude_post_ids: getManualRelatedIDs(),
                        relevance: relevance
                    }
                }).done(function (data) {
                    // Map the response data to an array of post titles
                    const postTitlesAndIds = data.map(function (post) {
                        return {
                            label: post.title,
                            id: post.id
                        };
                    });

                    // Cache the results
                    cache = postTitlesAndIds;

                    // Call the response callback with the post titles
                    response(postTitlesAndIds);
                }).fail(function () {
                    console.log('Error fetching posts.');
                });

                // Store the last request term
                last = request.term;
            },
            search: function (event, ui) {
                if (inputField.val().match(/^\d+$/)) {
                    if (inputField.val().length < 2) {
                        return false;
                    }
                } else {
                    if (inputField.val().length < 3) {
                        return false;
                    }
                }
            },
            focus: function (event, ui) {
                // Prevent value inserted on focus.
                event.preventDefault();
            },
            select: function (event, ui) {
                // Get the selected post
                const selectedPost = ui.item;

                // Add the post to the post list
                const postItem = $('<li>').addClass('widefat post-' + selectedPost.id);
                const deleteButton = $('<button>').text('').addClass('ntdelbutton button-link').attr('type', 'button');
                const dragHandle = $('<span>').addClass('crp-drag-handle dashicons dashicons-menu').attr('title', 'Drag to reorder');

                // Add the drag handle, delete button and post title to the post item
                postItem.append(dragHandle);
                postItem.append(deleteButton);
                postItem.append(' ');
                postItem.append(selectedPost.label);

                // Add the post item to the post list
                postList.append(postItem);

                // Update the hidden field with the new list of posts
                updateHiddenField();

                // Clear the input field
                inputField.val('');

                // Prevent value inserted on select.
                event.preventDefault();

                // Prevent the default behavior
                return false;
            },
        }, options);

        $element.on("keydown", function (event) {
            // Don't navigate away from the field on tab when selecting an item.
            if (event.keyCode === $.ui.keyCode.TAB &&
                $(this).autocomplete('instance').menu.active) {
                event.preventDefault();
            }
        }).autocomplete(options);

        // Delete a post from the post list when the delete button is clicked. Also remove the post ID from the hidden field.
        postList.on('click', '.ntdelbutton', function () {
            const postItem = $(this).parent();
            postItem.remove();

            // Update the hidden field
            updateHiddenField();
        });
    };

    // Initialize the autocomplete plugin. The input field has an ID of manual-related-posts.
    $('#crp-manual-related').crpAutocompletePosts();
});
