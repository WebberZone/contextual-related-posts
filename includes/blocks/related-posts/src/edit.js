/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import ServerSideRender from '@wordpress/server-side-render';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

import {
	Disabled,
	TextControl,
	TextareaControl,
	ToggleControl,
	PanelBody,
	PanelRow,
	SelectControl,
	RadioControl,
	Placeholder,
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
//import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const postId =
		null === wp.data.select('core/editor')
			? 0
			: wp.data.select('core/editor').getCurrentPostId();
	const {
		heading,
		title,
		limit,
		offset,
		show_excerpt,
		show_author,
		show_date,
		random_order,
		ordering,
		post_thumb_op,
		other_attributes,
	} = attributes;

	const blockProps = useBlockProps();
	const toggleHeading = () => {
		setAttributes({ heading: !heading });
	};
	const onChangeTitle = (newTitle) => {
		setAttributes({
			title: undefined === newTitle ? '' : newTitle,
		});
	};
	const onChangeLimit = (newLimit) => {
		setAttributes({
			limit: undefined === newLimit ? '' : newLimit,
		});
	};
	const onChangeOffset = (newOffset) => {
		setAttributes({
			offset: undefined === newOffset ? '' : newOffset,
		});
	};
	const toggleShowExcerpt = () => {
		setAttributes({ show_excerpt: !show_excerpt });
	};
	const toggleShowAuthor = () => {
		setAttributes({ show_author: !show_author });
	};
	const toggleShowDate = () => {
		setAttributes({ show_date: !show_date });
	};
	const toggleRandomOrder = () => {
		setAttributes({ random_order: !random_order });
	};
	const onChangeOrdering = (newOrdering) => {
		setAttributes({ ordering: newOrdering });
	};
	const onChangeThumbnail = (newThumbnailLoc) => {
		setAttributes({ post_thumb_op: newThumbnailLoc });
	};
	const onChangeOtherAttributes = (newOtherAttributes) => {
		setAttributes({
			other_attributes:
				undefined === newOtherAttributes ? '' : newOtherAttributes,
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__('Related Posts Settings', 'contextual-related-posts')}
					initialOpen={true}
				>
					<PanelRow>
						<fieldset>
							<ToggleControl
								label={__('Show heading', 'contextual-related-posts')}
								help={
									heading
										? __('Heading displayed', 'contextual-related-posts')
										: __('No Heading displayed', 'contextual-related-posts')
								}
								checked={heading}
								onChange={toggleHeading}
							/>
						</fieldset>
					</PanelRow>
					{heading && (
						<PanelRow>
							<TextControl
								label={__('Heading of posts', 'contextual-related-posts')}
								value={title}
								onChange={onChangeTitle}
								help={__(
									'Displayed before the list of the posts as a master heading. HTML allowed.',
									'contextual-related-posts'
								)}
							/>
						</PanelRow>
					)}
					<PanelRow>
						<fieldset>
							<TextControl
								label={__('Number of posts', 'contextual-related-posts')}
								value={limit}
								onChange={onChangeLimit}
								help={__(
									'Maximum number of posts to display',
									'contextual-related-posts'
								)}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<TextControl
								label={__('Offset', 'contextual-related-posts')}
								value={offset}
								onChange={onChangeOffset}
								help={__(
									'Number of posts to skip from the top',
									'contextual-related-posts'
								)}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								label={__('Show excerpt', 'contextual-related-posts')}
								help={
									show_excerpt
										? __('Excerpt displayed', 'contextual-related-posts')
										: __('No excerpt', 'contextual-related-posts')
								}
								checked={show_excerpt}
								onChange={toggleShowExcerpt}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								label={__('Show author', 'contextual-related-posts')}
								help={
									show_author
										? __(
												'"by Author Name" displayed',
												'contextual-related-posts'
										  )
										: __('No author displayed', 'contextual-related-posts')
								}
								checked={show_author}
								onChange={toggleShowAuthor}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								label={__('Show date', 'contextual-related-posts')}
								help={
									show_date
										? __('Date of post displayed', 'contextual-related-posts')
										: __(
												'Date of post not displayed',
												'contextual-related-posts'
										  )
								}
								checked={show_date}
								onChange={toggleShowDate}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<SelectControl
								label={__('Thumbnail option', 'contextual-related-posts')}
								value={post_thumb_op}
								onChange={onChangeThumbnail}
								help={__(
									'Location of the post thumbnail',
									'contextual-related-posts'
								)}
								options={[
									{
										value: 'inline',
										label: __('Before title', 'contextual-related-posts'),
									},
									{
										value: 'after',
										label: __('After title', 'contextual-related-posts'),
									},
									{
										value: 'thumbs_only',
										label: __('Only thumbnail', 'contextual-related-posts'),
									},
									{
										value: 'text_only',
										label: __('Only text', 'contextual-related-posts'),
									},
								]}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<RadioControl
								label={__('Order posts', 'contextual-related-posts')}
								selected={ordering}
								onChange={onChangeOrdering}
								help={__(
									'This option directly edits the query',
									'contextual-related-posts'
								)}
								options={[
									{
										value: 'relevance',
										label: __('By relevance', 'contextual-related-posts'),
									},
									{
										value: 'random',
										label: __('Randomly', 'contextual-related-posts'),
									},
									{
										value: 'date',
										label: __('By date', 'contextual-related-posts'),
									},
								]}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								label={__('Randomize posts', 'contextual-related-posts')}
								help={
									random_order
										? __(
												'Posts are shuffled on each load',
												'contextual-related-posts'
										  )
										: __(
												'Posts displayed based on above setting',
												'contextual-related-posts'
										  )
								}
								checked={random_order}
								onChange={toggleRandomOrder}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<TextareaControl
								label={__('Other attributes', 'contextual-related-posts')}
								value={other_attributes}
								onChange={onChangeOtherAttributes}
								help={__(
									'Enter other attributes in a URL-style string-query. e.g. post_types=post,page&link_nofollow=1&exclude_post_ids=5,6',
									'contextual-related-posts'
								)}
							/>
						</fieldset>
					</PanelRow>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{!postId ? (
					<Placeholder
						icon={'list-view'}
						label={__('Contextual Related Posts', 'contextual-related-posts')}
						instructions={__(
							'This is a placeholder for the related posts block. Visit the front end of your site to see the related posts.',
							'contextual-related-posts'
						)}
					></Placeholder>
				) : (
					<Disabled>
						<ServerSideRender
							block="contextual-related-posts/related-posts"
							attributes={attributes}
						/>
					</Disabled>
				)}
			</div>
		</>
	);
}
