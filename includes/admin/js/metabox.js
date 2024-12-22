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

                // Add the delete button and post title to the post item
                postItem.append(deleteButton);
                postItem.append(' ');
                postItem.append(selectedPost.label);

                // Add the post item to the post list
                postList.append(postItem);

                // Save the selectedPost.id to the hidden input field which is comma-separated and called crp_manual_related. If the field is empty, just add the post ID. If it's not empty, append the post ID to the existing value.
                const manualRelatedIDs = getManualRelatedIDs();
                if (manualRelatedIDs.length === 0) {
                    hiddenField.val(selectedPost.id);
                } else {
                    const hiddenFieldValue = hiddenField.val();
                    if (hiddenFieldValue === '') {
                        hiddenField.val(selectedPost.id);
                    } else {
                        hiddenField.val(hiddenFieldValue + ',' + selectedPost.id);
                    }
                }

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
            const postID = postItem.attr('class').match(/post-(\d+)/)[1];
            postItem.remove();

            // Get the post IDs from the post list
            const manualRelatedIDs = getManualRelatedIDs();

            // Remove the post ID from the array
            const index = manualRelatedIDs.indexOf(postID);
            if (index > -1) {
                manualRelatedIDs.splice(index, 1);
            }

            // Update the hidden field called crp_manual_related
            hiddenField.val(manualRelatedIDs.join(','));

        });
    };

    // Initialize the autocomplete plugin. The input field has an ID of manual-related-posts.
    $('#crp-manual-related').crpAutocompletePosts();
});
