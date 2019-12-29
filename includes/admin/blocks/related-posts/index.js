( function( blocks, i18n, element, components, editor, blockEditor ) {
	var el = element.createElement;
	const {registerBlockType} = blocks;
	const {__} = i18n; //translation functions
	var ServerSideRender = wp.serverSideRender;

	const { RichText, InspectorControls } = blockEditor;
	const {
		TextControl,
		CheckboxControl,
		RadioControl,
		SelectControl,
		TextareaControl,
		ToggleControl,
		RangeControl,
		Panel,
		PanelBody,
		PanelRow,
	} = components;

	registerBlockType( 'contextual-related-posts/related-posts', {
		title: __( 'Related Posts [CRP]', 'contextual-related-posts' ),
		description: __( 'Display related posts by Contextual Related Posts', 'contextual-related-posts' ),
		category: 'widgets',
		icon: 'list-view',
		keywords: [ __( 'related posts' ), __( 'contextual' ), __( 'posts' ) ],

		attributes: {
			heading: {
				type: 'boolean',
				default: false,
			},
			limit: {
				type: 'number',
				default: 6,
			},
			offset: {
				type: 'number',
				default: 0,
			},
			show_excerpt: {
				type: 'boolean',
				default: false,
			},
			show_author: {
				type: 'boolean',
				default: false,
			},
			show_date: {
				type: 'boolean',
				default: false,
			},
			post_thumb_op: {
				type: 'string',
				default: 'inline',
			},
			ordering: {
				type: 'string',
				default: 'relevance',
			},
			random_order: {
				type: 'boolean',
				default: false,
			},
			other_attributes: {
				type: 'string',
				default: '',
			},
		},

		supports: {
			html: false,
		},

		example: { },

		edit: function( props ) {
			const attributes =  props.attributes;
			const setAttributes =  props.setAttributes;

			var orderings = Object.keys(crp_php_variables.orderings).map(function(key) {
				return {value: key, label: crp_php_variables.orderings[key]};
			});

			if(props.isSelected){
	      	//	console.debug(props.attributes);
    		};


			// Functions to update attributes.
			function changeHeading(heading){
				setAttributes({heading});
			}

			function changeExcerpt(show_excerpt){
				setAttributes({show_excerpt});
			}

			function changeAuthor(show_author){
				setAttributes({show_author});
			}

			function changeDate(show_date){
				setAttributes({show_date});
			}

			function changeThumbnail(post_thumb_op){
				setAttributes({post_thumb_op});
			}

			function changeOrdering(ordering){
				setAttributes({ordering});
			}

			function changeRandomOrder(random_order){
				setAttributes({random_order});
			}

			function changeOtherAttributes(other_attributes){
				setAttributes({other_attributes});
			}

			return [
				/**
				 * Server side render
				 */
				el("div", { className: props.className },
					el( ServerSideRender, {
					  block: 'contextual-related-posts/related-posts',
					  attributes: attributes
					} )
				),

				/**
				 * Inspector
				 */
				el( InspectorControls, {},
					el( PanelBody, { title: 'Related Posts Settings', initialOpen: true },

						el( ToggleControl, {
							label: __( 'Show heading', 'contextual-related-posts' ),
							checked: attributes.heading,
							onChange: changeHeading
						} ),
						el( TextControl, {
							label: __( 'No. of posts', 'contextual-related-posts' ),
							value: attributes.limit,
							onChange: function( val ) {
								setAttributes( { limit: parseInt( val ) } );
							},
							type: 'number',
							min: 1,
							step: 1
						} ),

						el( TextControl, {
							label: __( 'Offset', 'contextual-related-posts' ),
							value: attributes.offset,
							onChange: function( val ) {
								setAttributes( { offset: parseInt( val ) } );
							},
							type: 'number',
							min: 0,
							step: 1
						}),

						el( ToggleControl, {
							label: __( 'Show excerpt', 'contextual-related-posts' ),
							checked: attributes.show_excerpt,
							onChange: changeExcerpt
						} ),
						el( ToggleControl, {
							label: __( 'Show author', 'contextual-related-posts' ),
							checked: attributes.show_author,
							onChange: changeAuthor
						} ),
						el( ToggleControl, {
							label: __( 'Show date', 'contextual-related-posts' ),
							checked: attributes.show_date,
							onChange: changeDate
						} ),
						el(SelectControl, {
							value: attributes.post_thumb_op,
							label: __( 'Thumbnail options', 'contextual-related-posts' ),
							onChange: changeThumbnail,
							options: [
								{value: 'inline', label: __( 'Before title', 'contextual-related-posts' )},
								{value: 'after', label: __( 'After title', 'contextual-related-posts' )},
								{value: 'thumbs_only', label: __( 'Only thumbnail', 'contextual-related-posts' )},
								{value: 'text_only', label: __( 'Only text', 'contextual-related-posts' )},
							]
						} ),
						el(RadioControl, {
							selected: attributes.ordering,
							label: __( 'Ordering', 'contextual-related-posts' ),
							onChange: changeOrdering,
							options: orderings
						} ),
						el( ToggleControl, {
							label: __( 'Randomize posts', 'contextual-related-posts' ),
							checked: attributes.random_order,
							onChange: changeRandomOrder
						} ),
						el( TextareaControl, {
							label: __( 'Other attributes', 'contextual-related-posts' ),
							help: __( 'Enter other attributes in a URL-style string-query. e.g. post_types=post,page&link_nofollow=1&exclude_post_ids=5,6', 'contextual-related-posts' ),
							value: attributes.other_attributes,
							onChange: changeOtherAttributes
						} )
					),
				),
			]
		},

		save(){
			return null;//save has to exist. This all we need
		}
	} );
} )(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element,
	window.wp.components,
	window.wp.editor,
	window.wp.blockEditor,
	window.wp.serverSideRender
);
