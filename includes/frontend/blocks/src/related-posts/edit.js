import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { select } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';
import { applyFilters } from '@wordpress/hooks';
import {
	Disabled,
	PanelBody,
	Placeholder,
} from '@wordpress/components';

import { postIcon } from './components/icons';
import { HeadingControls } from './components/heading-controls';
import { PostDisplayControls } from './components/post-display-controls';
import { PostLimitControls } from './components/post-limit-controls';
import { OrderingControls } from './components/ordering-controls';
import { OtherAttributesControl } from './components/other-attributes-control';

export default function Edit({ attributes, setAttributes }) {
	const editor = select('core/editor');
	const postId = editor ? editor.getCurrentPostId() : 0;

	const handleChange = (attributeName) => (newValue) => {
		setAttributes({ [attributeName]: newValue });
	};

	const handleToggle = (attributeName) => () => {
		setAttributes({ [attributeName]: !attributes[attributeName] });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__(
						'Related Posts Settings',
						'contextual-related-posts'
					)}
					initialOpen={true}
				>
					<HeadingControls
						heading={attributes.heading}
						title={attributes.title}
						onChangeTitle={handleChange('title')}
						toggleHeading={handleToggle('heading')}
					/>
					<PostLimitControls
						limit={attributes.limit}
						offset={attributes.offset}
						onChangeLimit={handleChange('limit')}
						onChangeOffset={handleChange('offset')}
					/>
					<PostDisplayControls
						show_excerpt={attributes.show_excerpt}
						show_author={attributes.show_author}
						show_date={attributes.show_date}
						toggleShowExcerpt={handleToggle('show_excerpt')}
						toggleShowAuthor={handleToggle('show_author')}
						toggleShowDate={handleToggle('show_date')}
					/>
					<OrderingControls
						ordering={attributes.ordering}
						random_order={attributes.random_order}
						onChangeOrdering={handleChange('ordering')}
						toggleRandomOrder={handleToggle('random_order')}
					/>

					{applyFilters(
						'crp-edit-inspector-controls',
						'',
						attributes,
						setAttributes
					)}

				</PanelBody>

			</InspectorControls>
			<InspectorControls group="advanced">
				<OtherAttributesControl
					value={attributes.other_attributes}
					onChange={handleChange('other_attributes')}
				/>
			</InspectorControls>

			<div {...useBlockProps()}>
				{!postId ? (
					<Placeholder
						icon={postIcon}
						label={__(
							'Contextual Related Posts',
							'contextual-related-posts'
						)}
						instructions={__(
							'This is a placeholder for the related posts block. Visit the front end of your site to see the related posts.',
							'contextual-related-posts'
						)}
					/>
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
